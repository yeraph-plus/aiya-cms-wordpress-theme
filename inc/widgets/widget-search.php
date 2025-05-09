<?php

//小工具：搜索

class AYA_Widget_Search extends AYA_Widget
{
    function widget_args()
    {
        $widget_args = array(
            'id' => 'widget-search',
            'title' => 'AIYA-CMS 搜索',
            'classname' => 'widget-panel',
            'desc' => '搜索框卡片',
        );

        return $widget_args;
    }

    function widget_func()
    {
?>
        <!-- overlay search -->
        <div class="widget-content">
            <form x-data="{ focus: false }" @click.outside="focus = false" action="/" method="get">
                <div class="search-form-overlay relative border border-white-dark/20 rounded-md h-12 w-full transition-all duration-300" @click="focus = true" :class="focus && 'input-focused'">
                    <button type="submit" class="text-dark/70 absolute ltr:right-1 rtl:left-1 inset-y-0 my-auto w-9 h-9 p-0 flex items-center justify-center peer-focus:text-primary" :class="{'ltr:!right-auto ltr:left-1 rtl:right-1': focus}">
                        <i data-feather="search" width="20" height="20" class="mr-1"></i>
                    </button>
                    <input type="text" name="s" placeholder="Search..." class="form-input bg-white h-full placeholder:tracking-wider hidden ltr:pl-12 rtl:pr-12 peer" :class="{'!block':focus}" />
                </div>
            </form>
        </div>
<?php

    }
}
