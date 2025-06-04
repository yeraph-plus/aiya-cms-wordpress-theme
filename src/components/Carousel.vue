<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import { computed, ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
//Heroicons
import { ChevronRightIcon, ChevronLeftIcon } from "@heroicons/vue/24/outline";
import { ChevronDoubleRightIcon, PhotoIcon } from "@heroicons/vue/20/solid";

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
        default: "full", // full, cms, mosaic
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

//布局自动自动降级逻辑
const actualLayoutType = computed(() => {
    //移动设备始终使用全宽模板
    if (isMobile.value) return "full";

    //拼搭模式项目数量小于4个时使用全宽模板
    if (props.layoutType === "mosaic" && Object.keys(props.postData).length < 4) {
        return "full";
    }

    return props.layoutType;
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

//如果使用拼搭布局，提取最后三项计算显示
const mosaicItems = computed(() => {
    if (actualLayoutType.value !== "mosaic") return [];

    return carouselItems.value.slice(-3);
});

const sliderItems = computed(() => {
    if (actualLayoutType.value === "mosaic") {
        return carouselItems.value.slice(0, -3);
    }

    return carouselItems.value;
});

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

    if (actualLayoutType.value === "cms") {
        //CMS轮播
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
    } else if (actualLayoutType.value === "mosaic") {
        //拼搭式轮播
        return {
            ...basecommon,
            pagination: {
                clickable: true,
                bulletActiveClass: "swiper-pagination-bullet-active-white",
                bulletClass: "swiper-pagination-bullet swiper-pagination-bullet-white",
            },
            navigation: {
                nextEl: ".swiper-svg-next-button",
                prevEl: ".swiper-svg-prev-button",
                disabledClass: "opacity-30 cursor-not-allowed",
            },
            slidesPerView: 1,
            spaceBetween: 0,
        };
    } else {
        //全宽轮播
        return {
            ...basecommon,
            pagination: {
                clickable: true,
                bulletActiveClass: "swiper-pagination-bullet-active-white",
                bulletClass: "swiper-pagination-bullet swiper-pagination-bullet-white",
            },
            navigation: {
                nextEl: ".swiper-svg-next-button",
                prevEl: ".swiper-svg-prev-button",
                disabledClass: "opacity-30 cursor-not-allowed",
            },
            slidesPerView: 1,
            spaceBetween: 0,
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
                    class="max-h-96">
                    <div class="card w-full h-full relative group overflow-hidden">
                        <!-- IMG -->
                        <img
                            :src="item.thumbnail"
                            :alt="item.title"
                            class="w-full h-96 object-cover transition-transform duration-500 group-hover:scale-105" />
                        <!-- MASK -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
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
                v-else-if="actualLayoutType === 'cms'"
                class="carousel-cms-container">
                <swiper
                    :modules="[Navigation, Pagination, Autoplay]"
                    v-bind="swiperSettings"
                    class="w-full">
                    <swiper-slide
                        v-for="(item, index) in carouselItems"
                        :key="index"
                        class="h-auto">
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
                <div class="swiper-cms-carousel-pagination flex justify-center mt-4"></div>
            </div>
            <!-- Mosaic Layout -->
            <div
                v-else-if="actualLayoutType === 'mosaic'"
                class="carousel-mosaic-container">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Left:Carousel -->
                    <div class="w-1/2">
                        <swiper
                            :modules="[Navigation, Pagination, Autoplay]"
                            v-bind="swiperSettings"
                            class="w-full rounded-box overflow-hidden">
                            <swiper-slide
                                v-for="(item, index) in sliderItems"
                                :key="index"
                                class="max-h-96">
                                <div class="card w-full h-full relative group overflow-hidden">
                                    <!-- IMG -->
                                    <img
                                        :src="item.thumbnail"
                                        :alt="item.title"
                                        class="w-full h-96 object-cover transition-transform duration-500 group-hover:scale-105" />
                                    <!-- MASK -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
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
                    </div>
                    <!-- Right:Cards -->
                    <div class="w-1/2 flex flex-col gap-4">
                        <a
                            v-if="mosaicItems.length > 0"
                            :href="mosaicItems[0].url"
                            class="card bg-base-100 shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-2xl h-46">
                            <div class="relative w-full h-full overflow-hidden">
                                <img
                                    :src="mosaicItems[0].thumbnail"
                                    :alt="mosaicItems[0].title"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
                                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                        <h3 class="text-lg font-bold mb-1 line-clamp-1 transition-transform duration-300 group-hover:translate-y-[-3px]">
                                            {{ mosaicItems[0].title }}
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
                        <div class="grid grid-cols-2 gap-4 flex-1">
                            <a
                                v-if="mosaicItems.length > 1"
                                :href="mosaicItems[1].url"
                                class="card bg-base-100 shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-2xl h-46">
                                <div class="relative w-full h-full overflow-hidden">
                                    <img
                                        :src="mosaicItems[1].thumbnail"
                                        :alt="mosaicItems[1].title"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                            <h3 class="text-base font-bold mb-1 line-clamp-1 transition-transform duration-300 group-hover:translate-y-[-3px]">
                                                {{ mosaicItems[1].title }}
                                            </h3>
                                            <div class="mt-2 opacity-80 transition-all duration-300 group-hover:opacity-100">
                                                <span class="inline-flex items-center text-xs font-medium border-b border-white/50 pb-1 group-hover:border-white">
                                                    {{ t("view_details") }}
                                                    <ChevronDoubleRightIcon class="h-3 w-3 ml-1 transition-transform group-hover:translate-x-1" />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <a
                                v-if="mosaicItems.length > 2"
                                :href="mosaicItems[2].url"
                                class="card bg-base-100 shadow-xl overflow-hidden group transition-all duration-300 hover:shadow-2xl h-46">
                                <div class="relative w-full h-full overflow-hidden">
                                    <img
                                        :src="mosaicItems[2].thumbnail"
                                        :alt="mosaicItems[2].title"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90">
                                        <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                            <h3 class="text-base font-bold mb-1 line-clamp-1 transition-transform duration-300 group-hover:translate-y-[-3px]">
                                                {{ mosaicItems[2].title }}
                                            </h3>
                                            <div class="mt-2 opacity-80 transition-all duration-300 group-hover:opacity-100">
                                                <span class="inline-flex items-center text-xs font-medium border-b border-white/50 pb-1 group-hover:border-white">
                                                    {{ t("view_details") }}
                                                    <ChevronDoubleRightIcon class="h-3 w-3 ml-1 transition-transform group-hover:translate-x-1" />
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- NULL -->
        <div
            v-else
            class="w-full h-48 bg-base-200 rounded-box flex flex-col items-center justify-center gap-2">
            <PhotoIcon class="size-20 text-base-content/30" />
            <p class="text-base-content/50 font-semibold">NONE THUMBNAIL</p>
        </div>
    </div>
</template>

<style scoped>
:deep(.swiper-cms-carousel-pagination .swiper-pagination-bullet-active) {
    transform: scale(1.2);
}
:deep(.swiper-pagination-bullet-white) {
    background-color: rgba(255, 255, 255, 0.5);
    width: 8px;
    height: 8px;
    opacity: 0.7;
    margin: 0 5px;
    transition: all 0.3s ease;
}
:deep(.swiper-pagination-bullet-active-white) {
    background-color: rgba(255, 255, 255, 1);
    opacity: 1;
    transform: scale(1.2);
}
</style>
