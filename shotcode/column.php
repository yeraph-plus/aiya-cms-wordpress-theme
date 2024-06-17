<?php
/**
 * Columns shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

function junkie_column_shortcode( $atts, $content = null ) {

	extract( shortcode_atts( array(
		'column' => 'one-third',
		'last'   => false
	), $atts ) );

	$last_class = '';
	$last_div   = '';
	if ( $last ) {
		$last_class = ' junkie-column-last';
		$last_div   = '<div class="junkie-clearfix"></div>';
	}

	return '<div class="junkie-' . sanitize_html_class( $column ) . esc_attr( $last_class ) . '">' . do_shortcode( stripslashes( $content ) ) . '</div>' . $last_div;

}
add_shortcode( 'junkie-column', 'junkie_column_shortcode' );