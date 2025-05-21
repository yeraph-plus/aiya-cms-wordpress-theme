<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";

const onSwiper = (swiper) => {
    console.log(swiper);
};
const onSlideChange = () => {
    console.log("slide change");
};
</script>

<template>
    <swiper
        :slides-per-view="3"
        :space-between="50"
        @swiper="onSwiper"
        @slideChange="onSlideChange">
        <swiper-slide>Slide 1</swiper-slide>
        <swiper-slide>Slide 2</swiper-slide>
        <swiper-slide>Slide 3</swiper-slide>
    </swiper>
</template>
