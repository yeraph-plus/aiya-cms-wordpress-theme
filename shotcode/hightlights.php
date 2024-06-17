<?php
/**
 * Hightlights shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.3
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */
function junkie_hightlights_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'color' => 'green'
    ), $atts ) );

   return '<span class="junkie-hightlights ' . sanitize_html_class( $color ) . '">' . do_shortcode( wp_filter_post_kses( $content ) ) . '</span>';

}
add_shortcode( 'junkie-hightlights', 'junkie_hightlights_shortcode' );