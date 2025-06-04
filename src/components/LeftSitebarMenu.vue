<script setup lang="ts">
import { computed, ref } from "vue";

//Props
const props = defineProps({
    menu: {
        type: Object,
        required: true,
        default: {
            1010: {
                label: "LEFT TITILE",
                url: "http:\/\/#",
                is_active: true,
                child: {
                    2010: {
                        label: "MENU 1",
                        url: "http:\/\/#",
                        is_active: true,
                        child: {
                            3010: {
                                label: "SUB SUB MENU",
                                url: "http:\/\/#",
                                is_active: true,
                                child: {},
                            },
                        },
                    },
                    2020: {
                        label: "LEFT MENU 2",
                        url: "http:\/\/#",
                        is_active: true,
                        child: {},
                    },
                },
            },
            1020: {
                label: "LEFT TITILE 2",
                url: "http:\/\/#",
                is_active: true,
                child: {},
            },
        },
    },
    top_menu: {
        type: Object,
        required: true,
        default: {
            1010: {
                label: "TOP MENU",
                url: "http:\/\/#",
                is_active: true,
            },
            1020: {
                label: "TOP MENU 2",
                url: "http:\/\/#",
                is_active: true,
            },
        },
    },
});

//将对象循环为数组
const menuItems = computed(() => {
    if (!props.menu) {
        console.error("[Option] Left sitebar menu is not set.");
        return [];
    } else {
        return Object.keys(props.menu).map((key) => ({
            id: key,
            ...props.menu[key],
        }));
    }
});

//是否有子菜单
function hasChildren(item) {
    return item.child && Object.keys(item.child).length > 0;
}

/**
 * 当且仅当子菜单有子菜单时，将第一层菜单处理成标题
 * 如果子菜单没有子菜单，则渲染为普通链接
 *
 */
</script>

<template>
    <ul class="menu menu-md w-full font-bold rounded-box p-4 text-base-content">
        <!-- For -->
        <template
            v-for="item in menuItems"
            :key="item.id">
            <!-- If -->
            <template v-if="hasChildren(item)">
                <li
                    class="menu-title my-2"
                    v-html="item.label"></li>
                <!-- Sub For -->
                <li
                    class="menu-dropdown pl-4"
                    v-for="(subItem, subKey) in item.child"
                    :key="subKey">
                    <!-- If -->
                    <template v-if="hasChildren(subItem)">
                        <details>
                            <summary>
                                <span v-html="subItem.label"></span>
                            </summary>
                            <ul>
                                <li
                                    v-for="(subSubItem, subSubKey) in subItem.child"
                                    :key="subSubKey">
                                    <a
                                        :href="subSubItem.url"
                                        :class="{ 'menu-active': subSubItem.is_active }">
                                        <span v-html="subSubItem.label"></span>
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </template>
                    <!-- Else -->
                    <template v-else>
                        <a
                            :href="subItem.url"
                            :class="{ 'menu-active': subItem.is_active }">
                            <span
                                class="ml-2"
                                v-html="subItem.label"></span>
                        </a>
                    </template>
                </li>
            </template>
            <!-- Else -->
            <template v-else>
                <li class="my-2">
                    <a
                        :href="item.url"
                        :class="{ 'menu-active': item.is_active }">
                        <span
                            class="ml-2"
                            v-html="item.label"></span>
                    </a>
                </li>
            </template>
        </template>
    </ul>
</template>
