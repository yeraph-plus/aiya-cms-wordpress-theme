<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * 
 * This theme used the anyother further customize, you may not need to change anything here.
 *  
 * If you want create a new template file for each one
 * you can create a new file in the theme "templates" folder.
 *
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no, email=no">
    <meta http-equiv="content-language" content="<?php aya_echo(get_locale()); ?>" />
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta http-equiv="Cache-Control" content="private">
    <?php wp_head(); ?>
</head>

<body <?php //body_class(); ?>>
    <div id="main-container">
        <?php wp_body_open(); ?>
        <?php
        //单一入口
        aya_core_route_entry();
        ?>
    </div>
    <?php wp_footer(); ?>
</body>

</html>