<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from "vue";
import Masonry from "masonry-layout";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    options: {
        type: Object,
        default: () => ({
            itemSelector: ".masonry-item",
            columnWidth: ".masonry-sizer",
            percentPosition: true,
            gutter: 16,
        }),
    },
    addMarginB: {
        type: Number,
        default: 16,
    },
});

const masonryContainer = ref(null);
let masonryInstance = null;

// 初始化 Masonry
const initMasonry = () => {
    if (!masonryContainer.value) return;

    // 销毁现有实例
    if (masonryInstance) {
        masonryInstance.destroy();
    }

    // 创建新实例
    nextTick(() => {
        masonryInstance = new Masonry(masonryContainer.value, props.options);
    });
};

// 布局更新
const layoutMasonry = () => {
    if (masonryInstance) {
        nextTick(() => {
            masonryInstance.layout();
        });
    }
};

// 监听数据变化
watch(
    () => props.items,
    () => {
        nextTick(() => {
            if (masonryInstance) {
                //在数据更新后重新排列布局
                masonryInstance.reloadItems();
                masonryInstance.layout();
            } else {
                //如果实例不存在，则初始化
                initMasonry();
            }
        });
    },
    { deep: true }
);

// 监听窗口调整大小
const handleResize = () => {
    layoutMasonry();
};

// 生命周期钩子
onMounted(() => {
    initMasonry();
    window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
    if (masonryInstance) {
        masonryInstance.destroy();
    }
    window.removeEventListener("resize", handleResize);
});
</script>

<template>
    <div ref="masonryContainer" class="masonry-container">
        <div class="masonry-sizer"></div>
        <div
            v-for="(item, index) in items"
            :key="item.id || index"
            class="masonry-item"
            :style="{ marginBottom: addMarginB + 'px' }">
            <slot :item="item" :index="index"></slot>
        </div>
    </div>
</template>

<style scoped>
.masonry-container {
    width: 100%;
}

.masonry-sizer,
.masonry-item {
    width: 100%;
}

@media (min-width: 640px) {
    .masonry-sizer,
    .masonry-item {
        width: calc(50% - 8px);
    }
}

@media (min-width: 1024px) {
    .masonry-sizer,
    .masonry-item {
        width: calc(33.333% - 11px);
    }
}
</style>
