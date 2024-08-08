<?php
if (!defined('ABSPATH')) exit;

//主题图像依赖组件包设置
if (AYF::get_checked('site_expand_image_manager', 'theme')) {
    AYF::new_opt(array(
        'title' => '图像设置',
        'parent' => 'theme',
        'slug' => 'image',
        'desc' => 'AIYA-CMS 主题，图像生成器的相关设置',
        'fields' => array(
            array(
                'desc' => '图像处理组件设置',
                'type' => 'title_2',
            ),
            array(
                'title' => '接管 WP 媒体库上传',
                'desc' => '使图片格式转换、图片压缩、图片水印功能在 WP 媒体库生效',
                'id' => 'site_image_manager_mix_wp_upload',
                'type' => 'switch',
                'default' => false,
            ),
            array(
                'title' => '简码图床插件',
                'desc' => '启用内置的简码图床插件，不使用 WP 媒体库',
                'id' => 'site_expand_picbed_plugin',
                'type' => 'switch',
                'default' => false,
            ),
            array(
                'desc' => '封面&海报生成器模板',
                'type' => 'title_2',
            ),
            array(
                'desc' => '有关此页面的全部设置、参数定义、模板等，请查阅 主题文档 ',
                'type' => 'message',
            ),
            array(
                'desc' => '封面和海报图片在文章保存时自动生成，如需要重新生成，请删除文章自定义字段中的 [code]cover_generated[/code] 或 [code]poster_generated[/code] 字段',
                'type' => 'message',
            ),
            array(
                'title' => '启用文章封面图片生成器',
                'desc' => '使用主题的文章封面图片生成工具（禁用此项时，自动获取文章特色图片或文章图片作为封面）',
                'id' => 'site_image_manager_cover_type',
                'type' => 'switch',
                'default' => true,
            ),
            array(
                'title' => '封面图片模板方法设置',
                'desc' => '为了便于使用时调整，封面和海报是在文章编辑页面工作的，此处设置仅用于定义模板默认格式 [/br] *背景属性建议任选一个，图层会顺序覆盖，使用多个图层时也会看不见',
                'id' => 'site_draw_image_cover_tamplate',
                'type' => 'checkbox',
                'sub' => array(
                    'bg_color' => '背景 - 纯色背景',
                    'bg_inner' => '背景 - 随机素材',
                    'bg_random' => '背景 - 真·随机素材',
                    'bg_image' => '背景 - 文章图片',
                    'mk_blur' => '蒙版 - 模糊特效',
                    'mk_bright' => '蒙版 - 降低亮度',
                    'mk_color' => '蒙版 - 色彩蒙版',
                    'fg_title_center' => '标题 - 居中文字',
                    'fg_title_top' => '标题 - 居上文字',
                    'fg_title_bottom' => '标题 - 居下文字',
                    'fg_title_auto' => '标题 - 自动截取',
                    'fg_thumb_image' => '缩略图 - 文章图片',
                ),
                'default' => ['bg_color', 'mk_blur', 'fg_thumb_image'],
            ),
            /*
            array(
                'title' => '海报生成模板',
                'desc' => '使用 JSON 格式',
                'id' => 'site_draw_image_poster_tamplate',
                'type' => 'checkbox',
                'sub' => array(
                    'lineNumbers' => false,
                ),
                'default' => [],
            ),
            */
            array(
                'desc' => '图像处理组件设置',
                'type' => 'title_2',
            ),
            array(
                'desc' => '如需修改图像的保存格式、保存质量等参数时，在此处填写参数覆盖默认的配置参数即可，参数定义请查阅 主题文档 ',
                'type' => 'message',
            ),
            array(
                'title' => '自定义参数（JSON）',
                'desc' => '参数配置文件使用 JSON 格式，支持指定的参数参照下方',
                'id' => 'site_image_manager_config_json',
                'type' => 'code_editor',
                'settings' => array(
                    'lineNumbers'   => false,
                    'tabSize'       => 0,
                    'theme'         => 'monokai',
                    'mode'          => 'json',
                ),
                'default' => '{"save_quality": 86,"save_format": "jpg","save_backup_raw_file": false,}',
            ),
            array(
                'desc' => '*以下为直接打印的图像处理组件默认的配置参数文件，包含了所有可用的参数，请根据需要自行修改。',
                'type' => 'message',
            ),
            array(
                'type' => 'callback',
                'function' => 'aya_expand_image_default_config',
            ),
        ),
    ));
}
