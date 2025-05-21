<script setup lang="ts">
import { ref } from "vue";
//Heroicons
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";
</script>

<template>
    <div class="form-control w-full">
        <form
            action="#"
            method="get">
            <div class="relative">
                <input
                    type="search"
                    name="s"
                    placeholder="Search..."
                    class="input input-bordered w-full pr-10" />
                <button
                    type="submit"
                    class="btn btn-ghost btn-sm absolute right-0 top-0 h-full">
                    <MagnifyingGlassIcon
                        class="size-5 text-base-content opacity-60"
                        aria-hidden="true" />
                </button>
            </div>
        </form>
    </div>
</template>
