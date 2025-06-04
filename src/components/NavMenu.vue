<script setup lang="ts">
import { computed } from "vue";
import MenuDorpdownItem from "./units/MenuDorpdownItem.vue";

//Props
const props = defineProps({
    menu: {
        type: Object,
        required: true,
        default: {
            1010: {
                label: "MENU",
                url: "http:\/\/#",
                is_active: true,
                child: {
                    2010: {
                        label: "SUB MENU",
                        url: "http:\/\/#",
                        is_active: true,
                        child: {
                            3010: {
                                label: "SUB SUB MENU",
                                url: "http:\/\/#",
                                is_active: true,
                                child: {
                                    4010: {
                                        label: "SUB SUB SUB MENU",
                                        url: "http:\/\/#",
                                        is_active: true,
                                        child: {},
                                    },
                                },
                            },
                        },
                    },
                },
            },
            1020: {
                label: "MENU 2",
                url: "http:\/\/#",
                is_active: true,
                child: {},
            },
        },
    },
    dorpdown: {
        type: Boolean,
        default: true,
    },
});

//将对象循环为数组
const menuItems = computed(() => {
    if (!props.menu) {
        //console.error("[Option] Top navbar menu is not set.");
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
</script>

<template>
    <ul class="menu menu-md menu-horizontal flex items-center justify-center rounded-box bg-gray-100/10 p-0.5">
        <!-- For -->
        <template
            v-for="item in menuItems"
            :key="item.id">
            <!-- If -->
            <template v-if="hasChildren(item) && dorpdown">
                <MenuDorpdownItem
                    :item="item"
                    :level="1" />
            </template>
            <!-- Else -->
            <template v-else>
                <li>
                    <a
                        class="whitespace-nowrap"
                        :class="{ 'menu-active': item.is_active }"
                        :href="item.url">
                        <span v-html="item.label"></span>
                    </a>
                </li>
            </template>
        </template>
    </ul>
</template>
