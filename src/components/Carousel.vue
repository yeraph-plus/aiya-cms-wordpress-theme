<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import { computed, ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { ChevronRightIcon, ChevronLeftIcon } from "@heroicons/vue/24/outline";
import { ChevronDoubleRightIcon } from "@heroicons/vue/20/solid";

// 导入 Swiper 样式
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
//Data
const props = defineProps({
    postData: {
        type: Object,
        default: () => ({
            0: {
                url: "",
                thumbnail: "https://",
                title: "Post Title",
                description: "",
            },
        }),
    },
    layoutType: {
        type: String,
        default: "full", //full, cms
    },
});

const { t } = useI18n();

//全局声明获取window.isMobile
declare global {
    interface Window {
        isMobile?: (() => boolean) & { onChange?: (cb: (status: boolean) => void) => void };
    }
}

//检测移动设备状态
const isMobile = ref(window.isMobile ? window.isMobile() : false);

//如果是移动设备，始终使用全宽布局
const actualLayoutType = computed(() => {
    return isMobile.value ? "full" : props.layoutType;
});

//组件挂载时订阅移动设备状态变化
onMounted(() => {
    if (window.isMobile && window.isMobile.onChange) {
        window.isMobile.onChange((status) => {
            isMobile.value = status;
        });
    }
});

//将对象转换为数组以便迭代
const carouselItems = computed(() => Object.values(props.postData));

//计算 CMS 布局下每个断点的配置
const getSlidesConfig = computed(() => {
    const count = carouselItems.value.length;
    return {
        max: Math.min(3, count),
        medium: Math.min(2, count),
        small: Math.min(1, count),
    };
});

//计算能否启用循环模式
const enableLoop = computed(() => {
    if (actualLayoutType.value === "full") {
        return carouselItems.value.length > 1;
    } else {
        return carouselItems.value.length > getSlidesConfig.value.max;
    }
});

//初始化Swiper设置
const swiperSettings = computed(() => {
    const basecommon = {
        loop: enableLoop.value,
        autoplay: carouselItems.value.length > 1 ? { delay: 5000, disableOnInteraction: false } : false,
    };

    if (actualLayoutType.value === "full") {
        return {
            ...basecommon,
            pagination: { clickable: true },
            navigation: {
                // 使用自定义类名，而不是默认值
                nextEl: ".swiper-svg-next-button",
                prevEl: ".swiper-svg-prev-button",
                disabledClass: "opacity-30 cursor-not-allowed",
            },
            slidesPerView: 1,
            spaceBetween: 0,
        };
    } else {
        return {
            ...basecommon,
            slidesPerView: getSlidesConfig.value.max,
            spaceBetween: 30,
            pagination: {
                el: ".swiper-cms-carousel-pagination",
                clickable: true,
                bulletActiveClass: "bg-primary",
                bulletClass: "inline-block w-2 h-2 mx-1 rounded-full bg-base-300 cursor-pointer transition-all",
            },
            breakpoints: {
                640: { slidesPerView: getSlidesConfig.value.small, spaceBetween: 10 },
                768: { slidesPerView: getSlidesConfig.value.medium, spaceBetween: 20 },
                1024: { slidesPerView: getSlidesConfig.value.max, spaceBetween: 30 },
            },
        };
    }
});
</script>

<template>
    <div class="carousel-container mb-4">
        <template v-if="carouselItems.length > 0">
            <!-- Full Layout -->
            <swiper
                v-if="actualLayoutType === 'full'"
                :modules="[Navigation, Pagination, Autoplay]"
                v-bind="swiperSettings"
                class="w-full rounded-box overflow-hidden">
                <swiper-slide
                    v-for="(item, index) in carouselItems"
                    :key="index"
                    class="max-h-100">
                    <div class="card w-full h-full relative group overflow-hidden">
                        <!-- 卡片背景图片 -->
                        <img
                            :src="item.thumbnail"
                            :alt="item.title"
                            class="w-full h-100 object-cover transition-transform duration-500 group-hover:scale-105" />

                        <!-- 内容遮罩 - 从底部渐变 -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
                            <!-- 内容容器 - 放置在底部，作为可点击区域 -->
                            <a
                                :href="item.url"
                                class="absolute bottom-0 left-0 right-0 p-5 md:p-8 text-white block">
                                <h2 class="text-2xl md:text-3xl font-bold mb-3 transition-transform duration-300 group-hover:translate-y-[-5px]">
                                    {{ item.title }}
                                </h2>
                                <p
                                    v-if="item.description"
                                    class="text-sm md:text-base line-clamp-3 opacity-90 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ item.description }}
                                </p>
                                <div class="mt-4 opacity-80 transition-all duration-300 group-hover:opacity-100">
                                    <span class="inline-flex items-center text-sm font-medium border-b border-white/50 pb-1 group-hover:border-white">
                                        {{ t("view_details") }}
                                        <ChevronDoubleRightIcon class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" />
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </swiper-slide>
                <div class="swiper-svg-prev-button absolute left-4 top-1/2 z-10 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-box transition-all">
                    <ChevronLeftIcon class="size-6 text-white" />
                </div>
                <div class="swiper-svg-next-button absolute right-4 top-1/2 z-10 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-box transition-all">
                    <ChevronRightIcon class="size-6 text-white" />
                </div>
            </swiper>

            <!-- CMS Layout -->
            <div
                v-else
                class="cms-carousel-container">
                <swiper
                    :modules="[Navigation, Pagination, Autoplay]"
                    v-bind="swiperSettings"
                    class="w-full">
                    <swiper-slide
                        v-for="(item, index) in carouselItems"
                        :key="index"
                        class="h-100">
                        <a
                            :href="item.url"
                            class="card h-full bg-base-100 shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-2xl">
                            <div class="relative w-full h-full overflow-hidden">
                                <!-- IMG -->
                                <img
                                    :src="item.thumbnail"
                                    :alt="item.title"
                                    class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" />

                                <!-- MASK -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
                                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                        <h3 class="text-lg font-bold mb-1 line-clamp-1 transition-transform duration-300 group-hover:translate-y-[-3px]">
                                            {{ item.title }}
                                        </h3>
                                        <div class="mt-2 opacity-80 transition-all duration-300 group-hover:opacity-100">
                                            <span class="inline-flex items-center text-xs font-medium border-b border-white/50 pb-1 group-hover:border-white">
                                                {{ t("view_details") }}
                                                <ChevronDoubleRightIcon class="h-4 w-4 ml-1 transition-transform group-hover:translate-x-1" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </swiper-slide>
                </swiper>
                <!-- 自定义分页指示器，显示在卡片下方 -->
                <div class="swiper-cms-carousel-pagination flex justify-center mt-4"></div>
            </div>
        </template>

        <!-- 无内容状态 -->
        <div
            v-else
            class="w-full h-64 bg-base-200 rounded-box flex items-center justify-center">
            <p class="text-base-content/50">暂无轮播内容</p>
        </div>
    </div>
</template>

<style scoped>
:deep(.swiper-cms-carousel-pagination .swiper-pagination-bullet-active) {
    transform: scale(1.2);
}
</style>
