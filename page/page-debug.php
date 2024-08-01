<?php

/**
 * Template Name: 测试页面
 */
//@header('Content-type:image/png');
/*head*/
//include_once AYA_PATH . '/inc/basic-html/body-head.php';

$file = 'http://host.local/wp-content/uploads/2024/06/2024061712005951.jpg';
$thisfile = aya_local_path_with_url($file, true);

//这部分参数发送到metabox
$cover = array(
    //'bg_by_color' => '',
    //'bg_material' => 6,
    'bg_by_custom_image' => $thisfile,
    //'bg_material_random' => 5,
    //'mask_blur' => 1,
    //'mask_bright' => -10,
    //'mask_color' => '#66ccff',
    //'title_center' => '测试文字',
    //'title_top' => '测试文字',
    //'title_bottom' => '测试文字',
    'title_auto' => '恶魔妹妹卖卖萌恶魔妹妹卖卖萌',
    //'thumb_image' => $thisfile,
);
$cover_draw = new AYA_Imagine_Draws();
$cover_draw->image_cover_drawing($cover);
//$img = new AYA_Imagine_Captcha();
//$img->captcha_generate($img->captcha_code(6));

//字体宽度计算 字体大小x1.5
//$img = new AYA_Imagine_Trans();
//$img->image_generate('thumb',$file,'400x200');

$config = array(
    //基本
    'save_max_width' => '1200',
    'save_upload_path' => 'thumbnail',
    'save_thumb_prefix' => 'thumb_',
    'save_format' => 'jpg', //png webp
    'save_quality' => 86,
    'save_backup_raw_file' => true,
    'thumb_default_width' => 400,
    'thumb_default_height' => 300,
    'font_path' => (__DIR__) . '/font/SourceHanSansCN-Bold.otf',
    'offset_x' => 10,
    'offset_y' => 10,
    //水印参数
    'watermark_type' => 'text', //text image
    'watermark_position' => 'center-center',
    'watermark_image' => (__DIR__) . '/image/default_watermark.png',
    'watermark_font_path' => (__DIR__) . '/font/SourceHanSansCN-Light.otf',
    'watermark_font_text' => 'AIYA-CMS | Yeraph.com [2024]',
    'watermark_font_size' => 24,
    'watermark_font_color' => '#ffffff',
    'watermark_font_opacity' => 80,
    //封面生成器
    'cover_width' => 800,
    'cover_height' => 600,
    'cover_bg_material_in' => array(
        (__DIR__) . '/material/circle-paint-1.png',
        (__DIR__) . '/material/circle-paint-2.png',
        (__DIR__) . '/material/circle-paint-3.png',
        (__DIR__) . '/material/circle-paint-4.png',
        (__DIR__) . '/material/color-splash-1.png',
        (__DIR__) . '/material/color-splash-2.png',
    ),
    'cover_fg_font_size' => 70,
    'cover_fg_font_width' => 100, //估算字体宽度，应根据字体不同
    'cover_fg_element_color_auto' => false,
    'cover_fg_element_color_light' => '#333333',
    'cover_fg_element_color_dark' => '#ffffff',
    'cover_fg_thumb_margin' => 30,
    'cover_fg_thumb_frame_width' => 5, //0则无边框
    'cover_fg_thumb_frame_color' => '#ffffff',
    //海报生成器
    'poster_width' => 900,
    'poster_height' => 1600,
    'background_default_color' => '#FFFAFA',
);