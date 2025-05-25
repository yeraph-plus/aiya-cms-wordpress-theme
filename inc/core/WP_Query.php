<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AIYA-CMS 接口文件 直接提取WP的查询数据结构到数组
 * 
 * Author: Yeraph Studio
 * Author URI: http://www.yeraph.com/
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AIYA-CMS Theme Options Framework
 * @version 1.0
 **/

class AYA_WP_Query_Object
{

    //原查询方法
    public function get_query($args = array())
    {
        //检查参数
        if (!is_array($args) || count($args) == 0)
            return;

        /**
         * WP_Query()参数指南：
         * https://developer.wordpress.org/reference/classes/wp_query/
         * 
         * 如需使用query_posts()方法：
         * 
         * //新建查询
         * if (!is_main_query()) $the_query = query_posts($args);
         * 
         * //重置查询
         * wp_reset_query();
         * 
         * Tips: 由于query_posts可以替代主查询，并不建议这样使用，可以直接new WP_Query()，防止互相影响。
         */

        //新建查询
        $post_query = get_posts($args);

        //重置查询
        wp_reset_postdata();

        //输出查询结果
        if (!empty($post_query)) {
            return $post_query;
        }
        //没有数据
        else {
            return false;
        }
    }
    //自定义单联查询函数
    public function aya_generate_post_li_query($type = '', $query_key = '', $query_value = '', $limit = 5, $order_by = 'date')
    {
        //检查输入是否为字符串
        if (!is_string($type) || !is_string($query_key) || !is_string($query_value))
            return;

        //配置查询类型
        switch ($type) {
            case 'post':
                $args = array(
                    'p' => $query_key, //文章ID
                    'name' => $query_value, //文章'sulg'
                );
                break;
            case 'page':
                $args = array(
                    'page_id' => $query_key, //页面ID
                    'pagename' => $query_value, //页面'sulg'
                );
                break;
            case 'meta':
                $args = array(
                    'meta_key' => $query_key, //Meta键名
                    'meta_value' => $query_value, //Meta键值
                );
                break;
            case 'cat':
                $args = array(
                    'cat' => $query_key, //分类ID, 整数型或为'1,2,3,'
                    'category_name' => $query_value, //此参数仅检索分类'sulg'
                );
                break;
            case 'tag':
                $args = array(
                    'tag' => $query_key, //此参数仅检索标签'sulg'
                    'tag_id' => $query_value, //标签ID, 整数型或为'1,2,3,'
                );
                break;
            case 'author':
                $args = array(
                    'author' => $query_key, //作者ID, 整数型或为'1,2,3,'
                    'author_name' => $query_value, //此参数用于检索'user_nicename'，不是用户名称ID
                );
                break;
            case 'search':
                $args = array(
                    's' => $query_key,
                );
                break;
            case 'year':
                $args = array(
                    'year' => $query_key,
                );
                break;
            case 'month':
                $args = array(
                    'monthnum' => $query_key,
                );
                break;
            case 'day':
                $args = array(
                    'day' => $query_key,
                );
                break;
            default:
                echo 'ERROR: Query $args[type] is null.';
                return;
        }

        //配置默认查询参数
        $defalt_args = array(
            'order' => 'DESC', //可选值：'ASC'（升序）'DESC'（降序）
            'orderby' => $order_by, //排序依据，默认为'date'，可选值：none'（不排序）, 'ID', 'rand'（随机）, 'author', 'title', 'date', 'modified', 'parent', 'comment_count', 'post__in'
            'ignore_sticky_posts' => false, //是否忽略置顶文章
            'posts_per_page' => $limit, //每页显示的文章数
            //'post_type' => array('post', 'page',), //可添加值：'post'（文章）, 'page'（页面）, 'attachment'（媒体附件）, 'nav_menu_item'（导航菜单项）, 'revision'（修订版本）, 'custom_post_type'（自定义文章类型）
            //'post_status' => 'publish', //指定文章状态，可选值：'','publish'（已发布）, 'pending'（等待审核）, 'draft'（草稿）, 'auto-draft'（自动草稿）, 'future'（定时发布）, 'private'（私有）, 'inherit'（继承）
            //'offset' => 0, //偏移，跳过的文章数量
            //'perm' => 'readable', //可用的值有：'readable', 'editable'
        );

        //合并参数
        $query_args = array_merge($args, $defalt_args);

        return ($query_args);
    }

    //自定义分类法查询函数
    public function aya_generate_tax_terms_query($taxonomy = '', $terms = array(), $limit = 5, $field = 'id')
    {
        //检查数据
        if (!is_string($taxonomy) || !is_array($terms))
            return;

        //Tips: tax_query 使用多维数组，但是此处没有写关联查询的方法
        $query_taxs = array(
            'taxonomy' => $taxonomy, //自定义分类法
            'field' => $field, //查询方式，可选'id'或'slug'
            'terms' => $terms, //分类名称
            'include_children' => true, //是否包含子分类
            'operator' => 'IN',
        );
        $query_args = array(
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'posts_per_page' => $limit,
            'tax_query' => array(
                'relation' => 'AND', //SQL参数，可用'AND', 'OR'
                $query_taxs,
            ),
        );

        return ($query_args);
    }

    //自定义文章类型查询函数
    public function aya_generate_post_type_query($post_type, $limit = 5, $paged = 0)
    {
        //允许输入'1,2,3,'或直接输入数组
        if (is_string($post_type)) {
            $post_type = explode(", ", $post_type);
        }
        //检查数据
        if (!is_array($post_type))
            return;

        //Tips: post_type 是数组
        $query_args = array(
            'order' => 'DESC',
            'orderby' => 'date',
            'ignore_sticky_posts' => false,
            'posts_per_page' => $limit,
            'paged' => ($paged == 0) ? '' : $paged, //判断分页
            'post_type' => $post_type,
        );

        return ($query_args);
    }
}
class AYA_Post_Query extends AYA_WP_Query_Object
{
    //列表查询
    public function query($query_array)
    {
        $post_query = parent::get_query($query_array);

        if ($post_query === false)
            return NULL;

        //返回结果
        return $content;
    }

    //列表查询
    public function li_query($type = '', $query_key = '', $query_value = '', $limit = 5, $order_by = 'date')
    {
        $query_array = parent::aya_generate_post_li_query($type, $query_key, $query_value, $limit, $order_by);

        return self::query($query_array);
    }

    //分类和自定义分类查询
    public function tax_terms_query($taxonomy = '', $terms = array(), $limit = 5, $field = 'id')
    {
        $query_array = parent::aya_generate_tax_terms_query($taxonomy, $terms, $limit, $field);

        return self::query($query_array);
    }

    //自定义文章类型查询
    public function post_type_query($post_type, $limit = 5, $paged = 0)
    {
        $query_array = parent::aya_generate_post_type_query($post_type, $limit, $paged);

        return self::query($query_array);
    }
}
