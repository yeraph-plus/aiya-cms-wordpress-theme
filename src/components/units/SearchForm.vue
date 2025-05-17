<script setup lang="ts">
import { ref } from "vue";
//Heroicons
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
</script>

<template>
    <div class="relative gap-2">
        <form
            action="#"
            method="get">
            <span class="absolute inset-y-0 left-0 flex items-center py-4">
                <button
                    type="submit"
                    class="p-2 focus:outline-none focus:ring-3">
                    <MagnifyingGlassIcon
                        class="size-5"
                        aria-hidden="true" />
                </button>
            </span>
            <input
                type="search"
                name="s"
                placeholder="Search..."
                class="w-full form-input py-2 pl-10 rounded-lg border border-gray-200 bg-gray-50 text-sm leading-5 font-normal text-gray-800 hover:border-gray-300 hover:text-gray-900 hover:shadow-xs focus:ring-3 focus:ring-gray-300/25 active:border-gray-200 active:shadow-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:text-gray-200 dark:focus:ring-gray-600/40 dark:active:border-gray-700" />
        </form>
    </div>
</template>
