<script setup>
import { computed, ref } from "vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    level: {
        type: Number,
        default: 1,
    },
});

function hasChild(item) {
    return item.child && Object.keys(item.child).length > 0;
}

//将子菜单对象转换为数组
const childItems = computed(() => {
    if (!hasChild(props.item)) return [];

    return Object.keys(props.item.child).map((key) => ({
        id: key,
        ...props.item.child[key],
    }));
});

/**
 * 最外层不包含ul层级，用于递归渲染
 *
 */
</script>

<template>
    <li>
        <template v-if="hasChild(item)">
            <details>
                <summary @click="toggleMenu">
                    <span>{{ item.label }}</span>
                </summary>
                <ul>
                    <!-- Sub -->
                    <MenuDorpdownItem
                        v-for="child in childItems"
                        :key="child.id"
                        :item="child"
                        :level="level + 1" />
                </ul>
            </details>
        </template>
        <!-- 没有子菜单的项目 -->
        <template v-else>
            <a
                :href="item.url"
                :class="{ 'menu-active': item.is_active }">
                {{ item.label }}
            </a>
        </template>
    </li>
</template>
