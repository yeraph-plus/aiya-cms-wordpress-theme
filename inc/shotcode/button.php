<?php
/**
 * Button shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

function junkie_button_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'url'    => '#',
		'target' => '_self',
		'style'  => 'grey',
		'size'   => 'small',
		'type'   => 'round'
    ), $atts ) );

   return '<a target="' . $target . '" class="junkie-button ' . sanitize_html_class( $size ) . ' ' . sanitize_html_class( $style ) . ' ' . sanitize_html_class( $type ) . '" href="' . esc_url( $url ) . '">' . do_shortcode( wp_filter_post_kses( $content ) ) . '</a>';
}

add_shortcode( 'junkie-button', 'junkie_button_shortcode' );