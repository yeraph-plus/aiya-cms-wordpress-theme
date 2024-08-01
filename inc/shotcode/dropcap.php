<?php
/**
 * Dropcap shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.3
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */
function junkie_dropcap_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'letter' => ''
    ), $atts ) );

   return '<span class="junkie-dropcap">' . do_shortcode( wp_filter_post_kses( $content ) ) . '</span>';

}
add_shortcode( 'junkie-dropcap', 'junkie_dropcap_shortcode' );