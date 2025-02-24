<?php
if (!defined('ABSPATH')) exit;

//初始化组件
add_action('init', 'aya_image_procer_init');
//操作WP上传方法
//add_filter('wp_handle_upload_prefilter', 'aya_image_procer_handle_wp_upload');

//更新配置
function aya_image_procer_init()
{
    if (class_exists('AYA_Image_Action')) {
        //启动处理器
        if (aya_opt('site_expand_image_manager', 'theme', true)) {
            //json转为数组
            $config = json_decode(aya_opt('site_image_manager_config_json', 'image'), true);
            //加载组件
            AYA_Image_Action::instance($config);
        }
        //启动简码图床
        if (is_admin() && aya_opt('site_expand_picbed_plugin', 'image', true)) {
            AYA_Shortcode_Pic_Bed::instance();
        }
        //创建封面使用的MetaBox
        if (aya_opt('site_image_manager_cover_type', 'image', true)) {
            //注册MetaBox
            add_action('add_meta_boxes', function () {
                add_meta_box('aya-draw-cover-meta-box', __('文章封面', 'AIYA'), 'aya_image_drawing_tamplate', 'post', 'side', 'core');
            });
            //注册AJAX动作
            add_action('wp_ajax_draw_cover', 'aya_expand_loading_cover');
        }
    }
}
//操作WP媒体库上传
function aya_image_procer_handle_wp_upload($file)
{
    if (aya_opt('site_image_manager_mix_wp_upload', 'image', true))  return;

    //判断是否是图片
    $file_type = exif_imagetype($file);
    //是图片
    if ($file_type == IMAGETYPE_JPEG || $file_type == IMAGETYPE_PNG || $file_type == IMAGETYPE_WEBP || $file_type == IMAGETYPE_GIF || $file_type == IMAGETYPE_BMP) {
    }
    //重命名
    $file_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    $file['name'] = date("YmdHis") . mt_rand(1, 100) . '_' . preg_replace('/\s+/', '_', $file_name) .  '.' . $file_extension;

    return $file;
}

/*
 * ------------------------------------------------------------------------------
 * 主题方法
 * ------------------------------------------------------------------------------
 */

//Loop组件
function aya_the_loop_image($post_id, $thumb_width = 400, $thumb_hight = 300)
{
    if (class_exists('AYA_Image_Action')) {
        //查询Metabox字段
        $the_image = get_post_meta($post_id, 'cover_generated', true);
        //没有生成的封面，查找缩略图
        if (empty($the_image)) {
            //生成缩略图
            $post = aya_get_post($post_id);
            $the_thumb = aya_expand_loading_thumb($post, $thumb_width, $thumb_hight);
            //没有缩略图，返回默认
            if ($the_thumb) {
                $the_image = $the_thumb;
            } else {
                $the_image = aya_get_default_thumbnail();
            }
        }

        return $the_image;
    }
    //使用主题的默认方法
    return aya_get_loop_thumb($post_id, $thumb_width, $thumb_hight);
}
//MetaBox组件
function aya_image_drawing_tamplate($post)
{
    //添加nonce字段
    wp_nonce_field('draw_action_meta_box_nonce', 'draw_action_meta_box_yes');
    //判断是否是新文章
    if ($post->post_title == '' || $post->post_content == '') {
        echo '<p class="description">' . __('请先添加文章标题和文章内容，封面模板将在文章第一次保存时生成', 'AIYA') . '</p>';
        return;
    }

    $meta_box = '';

    //获取设置中的模板数据
    $image_cover_opt = aya_opt('site_draw_image_cover_tamplate', 'image');
    $tamplate_load = array();
    //添加格式
    foreach ($image_cover_opt as $tamplate) {
        switch ($tamplate) {
            case 'bg_color':
                $tamplate_load['bg_by_color'] = '#FFFAFA';
                break;
            case 'bg_inner':
                $tamplate_load['bg_material'] = 0;
                break;
            case 'bg_random':
                $tamplate_load['bg_material_random'] = 0;
                break;
            case 'bg_image':
                $tamplate_load['bg_by_custom_image'] = aya_match_post_first_image($post, false);
                break;
            case 'mk_blur':
                $tamplate_load['mask_blur'] = 10;
                break;
            case 'mk_bright':
                $tamplate_load['mask_bright'] = -10;
                break;
            case 'mk_color':
                $tamplate_load['mask_color'] = '#333';
                break;
            case 'fg_title_center':
                $tamplate_load['title_center'] = aya_match_post_first_words($post);
                break;
            case 'fg_title_top':
                $tamplate_load['title_top'] = aya_match_post_first_words($post);
                break;
            case 'fg_title_bottom':
                $tamplate_load['title_bottom'] = aya_match_post_first_words($post);
                break;
            case 'fg_title_auto':
                $tamplate_load['title_auto'] = $post->post_title;
                break;
            case 'fg_thumb_image':
                $tamplate_load['thumb_image'] = aya_match_post_first_image($post, false);
                break;
        }
    }
    //编码为JSON
    $tamplate_json = json_encode($tamplate_load, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    //已有封面
    $the_cover = get_post_meta($post->ID, 'cover_generated', true);

    //$meta_box .= '<label for="draw_cover_status">' . __('当前封面', 'AIYA') . '</label>';
    $meta_box .= '<img id="drawing_spinner" src="' . esc_url(get_admin_url() . 'images/spinner.gif') . '" />';
    if (empty($the_cover)) {
        $button_text = __('创建封面', 'AIYA');

        $meta_box .= '<div id="draw_cover_status"></div>';
    } else {
        $button_text = __('重新生成', 'AIYA');

        $meta_box .= '<div id="draw_cover_status"><img src="' . $the_cover . '" style="width: 100%; height: auto;" /></div>';
        $meta_box .= '<hr />';
    }

    //模板表单
    $meta_box .= '<label for="draw_cover_tamplate_json">' . __('模板参数（JSON）', 'AIYA') . '</label>';
    $meta_box .= '<input type="button" id="draw_cover_action" class="button" style="width: 100%; margin: 10px 0px;" value="' . $button_text . '" />';
    $meta_box .= '<textarea id="draw_cover_tamplate_json" name="draw_cover_tamplate_json" rows="5" cols="50" style="width: 100%; height: 200px;">' . stripslashes($tamplate_json) . '</textarea>';
    $meta_box .= '<p class="description">' . __('*参数模板可在主题“图像设置”中调整', 'AIYA') . '</p>';

    //隐藏输入框，提交给保存函数操作
    //$meta_box .= '<input type="text" id="draw_cover_post_save" name="draw_cover_post_save" style="display:none;" value="" />';

    echo $meta_box;

    //ajaxJS
?>
    <script>
        jQuery(document).ready(function($) {
            $('#drawing_spinner').hide();

            $('#draw_cover_action').on('click', function() {
                $('#drawing_spinner').show();
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        nonce: '<?php echo wp_create_nonce('action_draw_nonce'); ?>',
                        action: 'draw_cover',
                        post_id: '<?php echo $post->ID; ?>',
                        draw_json: $('#draw_cover_tamplate_json').val(),
                    },
                    success: function(response) {
                        //$('#draw_cover_status').html(response);
                        $('#draw_cover_status').html('<img src="' + response.cover_url + '" style="width: 100%; height: auto;" />');
                        $('#draw_cover_action').val(response.status);
                        $('#drawing_spinner').hide();
                    },
                    error: function(xhr, status, error) {
                        console.log(status, error);
                        $('#drawing_spinner').hide();
                    }
                });
            });
        });
    </script>
<?php
}
//提取自动生成的封面
function aya_get_cover_image($post_id)
{
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }

    $the_cover = get_metadata('post', $post_id, 'cover_generated', true);

    return $the_cover;
}
//提取自动生成的海报
function aya_get_poster_image($post_id)
{
    if (empty($post_id)) {
        $post_id = get_the_ID();
    }

    $the_poster = get_metadata('post', $post_id, 'poster_generated', true);

    return $the_poster;
}

/*
 * ------------------------------------------------------------------------------
 * Imagine封装库的调用
 * ------------------------------------------------------------------------------
 */

//默认配置
function aya_expand_image_default_config()
{
    $default_config = AYA_Image_Action::$default_config;
    //转为json
    $config_data = json_encode($default_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    echo '<div class="form-field section-content-field">';
    echo '<hr />';
    echo '<pre><code>' . $config_data . '</code></pre>';
    echo '<hr />';
    echo '</div>';

    return;
}
//缩略图
function aya_expand_loading_thumb($post_obj, $thumb_width = 400, $thumb_hight = 300)
{

    //提取特色图片
    $post_image = aya_get_post_thumbnail($post_obj);

    //没有特色图片，尝试提取文章的第一个图片
    if (empty($post_image)) {
        $post_image = aya_match_post_first_image($post_obj, false);
    }
    //没有图片or不是本地图片，返回默认
    if (empty($post_image) || !cur_is_home($post_image)) {
        return false;
    }

    //加载驱动
    $image = new AYA_Imagine_Trans();
    $the_thumb = $image->image_thumb_generate($post_image, $thumb_width, $thumb_hight);

    if ($the_thumb == false) {
        return false;
    } else {
        return aya_local_path_with_url($the_thumb, false);
    }
}
//封面（ajax）
function aya_expand_loading_cover()
{
    //验证请求
    check_ajax_referer('action_draw_nonce', 'nonce');
    //检查传回json
    if (empty($_POST['draw_json'])) {
        echo '<b>' . __('模板参数为空', 'AIYA') . '</b>';
        wp_die();
    }

    $post_id = intval($_POST['post_id']);
    $tamplate_json = stripslashes($_POST['draw_json']);
    //读取JSON
    $drawing_work = json_decode($tamplate_json, true);

    //检查参数设置
    if (isset($drawing_work['bg_by_custom_image'])) {
        $drawing_work['bg_by_custom_image'] = aya_local_path_with_url($drawing_work['bg_by_custom_image'], true);
    }
    if (isset($drawing_work['thumb_image'])) {
        $drawing_work['thumb_image'] = aya_local_path_with_url($drawing_work['thumb_image'], true);
    }

    //加载驱动
    $cover_draw = new AYA_Imagine_Draws();
    $the_cover = $cover_draw->image_cover_drawing($drawing_work);

    if ($the_cover !== false) {
        $the_cover = aya_local_path_with_url($the_cover, false);
    }
    //保存数据
    if (update_post_meta($post_id, 'cover_generated', $the_cover)) {
        $message = __('保存成功', 'AIYA');
    } else {
        $message = __('保存失败', 'AIYA');
    };
    //返回
    wp_send_json(array(
        'cover_url' => $the_cover,
        'status' => $message,
    ));
    return;
}
//海报（ajax）
function aya_expand_loading_poster()
{
}
