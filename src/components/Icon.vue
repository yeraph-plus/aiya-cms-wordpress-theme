<script>
import { computed, defineComponent } from "vue";
import {
    ChevronDownIcon,
    ChevronUpIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
    MagnifyingGlassIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    ArrowRightIcon,
    ArrowLeftIcon,
    CalendarDaysIcon,
    UserIcon,
    HomeIcon,
    PlusIcon,
    MinusIcon,
    CheckIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    // 新增图标
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    ClockIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    EyeIcon,
    HeartIcon as HeartOutlineIcon,
    HomeIcon as HomeOutlineIcon,
    StarIcon as StarOutlineIcon,
    ClipboardIcon,
    FlagIcon,
    HashtagIcon,
    PaperClipIcon,
    UserIcon as UserOutlineIcon,
    CloudIcon,
    ShieldCheckIcon,
} from "@heroicons/vue/24/outline";

// 实心版本的图标
import {
    ChevronDownIcon as ChevronDownSolidIcon,
    ChevronUpIcon as ChevronUpSolidIcon,
    HeartIcon,
    StarIcon,
    // 新增实心图标
    ChevronDoubleLeftIcon as ChevronDoubleLeftSolidIcon,
    ChevronDoubleRightIcon as ChevronDoubleRightSolidIcon,
    ClockIcon as ClockSolidIcon,
    ChatBubbleOvalLeftEllipsisIcon as ChatBubbleOvalLeftEllipsisSolidIcon,
    EyeIcon as EyeSolidIcon,
    HomeIcon as HomeSolidIcon,
    ClipboardIcon as ClipboardSolidIcon,
    FlagIcon as FlagSolidIcon,
    HashtagIcon as HashtagSolidIcon,
    PaperClipIcon as PaperClipSolidIcon,
    UserIcon as UserSolidIcon,
    CloudIcon as CloudSolidIcon,
    ShieldCheckIcon as ShieldCheckSolidIcon,
} from "@heroicons/vue/24/solid";

export default defineComponent({
    name: "Icon",
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: true,
        },
        class: {
            type: [String, Array, Object],
            default: "",
        },
        solid: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        // 图标映射表 - 外边框版本
        const outlineIcons = {
            "chevron-down": ChevronDownIcon,
            "chevron-up": ChevronUpIcon,
            "chevron-left": ChevronLeftIcon,
            "chevron-right": ChevronRightIcon,
            "x-mark": XMarkIcon,
            search: MagnifyingGlassIcon,
            email: EnvelopeIcon,
            phone: PhoneIcon,
            location: MapPinIcon,
            "arrow-right": ArrowRightIcon,
            "arrow-left": ArrowLeftIcon,
            calendar: CalendarDaysIcon,
            user: UserOutlineIcon,
            home: HomeOutlineIcon,
            plus: PlusIcon,
            minus: MinusIcon,
            check: CheckIcon,
            warning: ExclamationTriangleIcon,
            info: InformationCircleIcon,
            // 新增图标
            "chevron-double-left": ChevronDoubleLeftIcon,
            "chevron-double-right": ChevronDoubleRightIcon,
            clock: ClockIcon,
            "chat-bubble-oval-left-ellipsis": ChatBubbleOvalLeftEllipsisIcon,
            eye: EyeIcon,
            heart: HeartOutlineIcon,
            star: StarOutlineIcon,
            clipboard: ClipboardIcon,
            flag: FlagIcon,
            hashtag: HashtagIcon,
            "paper-clip": PaperClipIcon,
            cloud: CloudIcon,
            "shield-check": ShieldCheckIcon,
        };

        // 图标映射表 - 实心版本
        const solidIcons = {
            "chevron-down": ChevronDownSolidIcon,
            "chevron-up": ChevronUpSolidIcon,
            heart: HeartIcon,
            star: StarIcon,
            // 新增实心图标
            "chevron-double-left": ChevronDoubleLeftSolidIcon,
            "chevron-double-right": ChevronDoubleRightSolidIcon,
            clock: ClockSolidIcon,
            "chat-bubble-oval-left-ellipsis": ChatBubbleOvalLeftEllipsisSolidIcon,
            eye: EyeSolidIcon,
            home: HomeSolidIcon,
            clipboard: ClipboardSolidIcon,
            flag: FlagSolidIcon,
            hashtag: HashtagSolidIcon,
            "paper-clip": PaperClipSolidIcon,
            user: UserSolidIcon,
            cloud: CloudSolidIcon,
            "shield-check": ShieldCheckSolidIcon,
        };

        // 获取指定的图标组件
        const iconComponent = computed(() => {
            const iconMap = props.solid ? solidIcons : outlineIcons;
            return iconMap[props.name] || null;
        });

        // 合并自定义类名
        const customClass = computed(() => {
            return ["icon-component", props.class];
        });

        return {
            iconComponent,
            customClass,
        };
    },
});
</script>

<template>
  <component
    :is="iconComponent"
    v-if="iconComponent"
    :class="[customClass]"
    aria-hidden="true"
    v-bind="$attrs"
  />
</template>
