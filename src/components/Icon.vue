<script setup lang="ts">
import {
    //outline 24x24
    MinusIcon,
    ClockIcon,
    CalendarIcon,
    EyeIcon,
    HomeIcon,
    HeartIcon,
    CodeBracketIcon,
    FunnelIcon,
    LinkIcon,
    AtSymbolIcon,
    PaperClipIcon,
    PaperAirplaneIcon,
    ExclamationTriangleIcon,
    ChatBubbleOvalLeftEllipsisIcon,
} from "@heroicons/vue/24/outline";

import {
    //solid 20x20
    ShieldCheckIcon,
    //HandThumbDownIcon,
    //HandThumbUpIcon,
    Bars3Icon,
    Bars3BottomLeftIcon,
    Bars3CenterLeftIcon,
    Bars3BottomRightIcon,
    ChevronDoubleDownIcon,
    ChevronDoubleUpIcon,
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    HashtagIcon,
    SlashIcon,
    XMarkIcon,
    ArrowUturnDownIcon,
    ArrowUturnLeftIcon,
    ArrowUturnRightIcon,
    ArrowUturnUpIcon,
    ArrowDownIcon,
    ArrowUpLeftIcon,
    ArrowUpRightIcon,
    ArrowLeftIcon,
    ArrowRightIcon,
    ArrowUpIcon,
} from "@heroicons/vue/20/solid";

const iconMap = {
    //outline
    clock: ClockIcon,
    calendar: CalendarIcon,
    minus: MinusIcon,
    eye: EyeIcon,
    home: HomeIcon,
    heart: HeartIcon,
    "code-bracket": CodeBracketIcon,
    funnel: FunnelIcon,
    link: LinkIcon,
    "at-symbol": AtSymbolIcon,
    "paper-clip": PaperClipIcon,
    "paper-airplane": PaperAirplaneIcon,
    "exclamation-triangle": ExclamationTriangleIcon,
    "chat-bubble-oval-left-ellipsis": ChatBubbleOvalLeftEllipsisIcon,
    //solid
    "shield-check": ShieldCheckIcon,
    "bars-3": Bars3Icon,
    "bars-3-bottom-left": Bars3BottomLeftIcon,
    "bars-3-center-left": Bars3CenterLeftIcon,
    "bars-3-bottom-right": Bars3BottomRightIcon,
    "chevron-double-down": ChevronDoubleDownIcon,
    "chevron-double-up": ChevronDoubleUpIcon,
    "chevron-double-left": ChevronDoubleLeftIcon,
    "chevron-double-right": ChevronDoubleRightIcon,
    "chevron-left": ChevronLeftIcon,
    "chevron-right": ChevronRightIcon,
    slash: SlashIcon,
    hashtag: HashtagIcon,
    "x-mark": XMarkIcon,
    "arrow-uturn-down": ArrowUturnDownIcon,
    "arrow-uturn-left": ArrowUturnLeftIcon,
    "arrow-uturn-right": ArrowUturnRightIcon,
    "arrow-uturn-up": ArrowUturnUpIcon,
    "arrow-down": ArrowDownIcon,
    "arrow-up-left": ArrowUpLeftIcon,
    "arrow-up-right": ArrowUpRightIcon,
    "arrow-left": ArrowLeftIcon,
    "arrow-right": ArrowRightIcon,
    "arrow-up": ArrowUpIcon,
};

const props = defineProps({
    name: {
        type: String,
        default: "minus",
    },
    class: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <component
        :is="iconMap[props.name]"
        :class="props.class"
        aria-hidden="true" />
</template>
