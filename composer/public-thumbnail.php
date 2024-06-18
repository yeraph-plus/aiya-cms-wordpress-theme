<?php
if (!defined('ABSPATH')) exit;

/*
 * ------------------------------------------------------------------------------
 * 主题设置
 * 
 * 说明：如果使用CDN时，需要替代WP的默认上传目录，如下
 * ------------------------------------------------------------------------------
 */

//define('WP_CONTENT_DIR','/home/user/public_html/cdn');
//define('WP_CONTENT_URL','http://cdn.yeraph.com');

/*
 * ------------------------------------------------------------------------------
 * 基于 Intervention/image 项目的方法封装函数
 * 
 * 文档：https://image.intervention.io/v3
 * ------------------------------------------------------------------------------
 */

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// create image manager with desired driver
$manager = new ImageManager(new Driver());

// read image from file system
$image = $manager->read('images/example.jpg');

// resize image proportionally to 300px width
$image->scale(width: 300);

// insert watermark
$image->place('images/watermark.png');

// save modified image in new format 
$image->toPng()->save('images/foo.png');


/*
 * ------------------------------------------------------------------------------
 * 缩略图组件及相关函数
 * ------------------------------------------------------------------------------
 */

//基于 Intervention/image 项目的方法https://image.intervention.io/v3

class AYA_ImageManager
{
}
