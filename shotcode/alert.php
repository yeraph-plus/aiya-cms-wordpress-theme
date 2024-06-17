<?php
/**
 * Alert shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */
function junkie_alert_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'style' => 'white'
    ), $atts ) );

   return '<div class="junkie-alert ' . sanitize_html_class( $style ) . '">' . do_shortcode( stripslashes( $content ) ) . '</div>';

}
add_shortcode( 'junkie-alert', 'junkie_alert_shortcode' );