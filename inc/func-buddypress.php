<?php

if (!defined('ABSPATH')) {
    exit;
}

exit;

// 集成 BuddyPress 用户数据
function aya_integrate_buddypress_user_data($user_menu)
{
    if (!function_exists('bp_is_active') || !bp_is_active('activity')) {
        return $user_menu;
    }

    if (!empty($user_menu['data']) && !empty($user_menu['data']['id'])) {
        $user_id = $user_menu['data']['id'];

        // 添加 BuddyPress 特定数据
        $user_menu['data']['bp_profile_url'] = bp_core_get_user_domain($user_id);

        // 添加活动数
        if (bp_is_active('activity')) {
            $user_menu['data']['activity_count'] = bp_get_total_activity_count_for_user($user_id);
        }

        // 添加朋友数
        if (bp_is_active('friends')) {
            $user_menu['data']['friend_count'] = bp_friend_get_total_friend_count($user_id);
        }

        // 添加群组数
        if (bp_is_active('groups')) {
            $user_menu['data']['group_count'] = bp_get_total_group_count_for_user($user_id);
        }
    }

    // 添加 BuddyPress 菜单项
    if (!empty($user_menu['menus'])) {
        $bp_menus = [];

        if (bp_is_active('activity')) {
            $bp_menus[] = [
                'label' => __('活动', 'AIYA'),
                'icon' => 'activity',
                'url' => bp_get_activity_directory_permalink(),
            ];
        }

        if (bp_is_active('groups')) {
            $bp_menus[] = [
                'label' => __('群组', 'AIYA'),
                'icon' => 'users',
                'url' => bp_get_groups_directory_permalink(),
            ];
        }

        if (bp_is_active('friends')) {
            $bp_menus[] = [
                'label' => __('好友', 'AIYA'),
                'icon' => 'user-plus',
                'url' => bp_loggedin_user_domain() . bp_get_friends_slug(),
            ];
        }

        // 插入BP菜单到用户菜单
        array_splice($user_menu['menus'], 1, 0, $bp_menus);
    }

    return $user_menu;
}

// 在用户登录时更新 BuddyPress 活动
function aya_bp_record_login_activity()
{
    if (function_exists('bp_is_active') && bp_is_active('activity')) {
        $user_id = get_current_user_id();
        if ($user_id) {
            bp_activity_add(array(
                'user_id' => $user_id,
                'component' => 'members',
                'type' => 'last_activity',
            ));
        }
    }
}
//add_action('wp_login', 'aya_bp_record_login_activity');