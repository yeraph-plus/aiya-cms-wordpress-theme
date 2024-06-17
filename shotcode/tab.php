<?php
/**
 * Tab shortcode
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

function junkie_tabs_shortcode( $atts, $content = null ) {

	$defaults = array();
	extract( shortcode_atts( $defaults, $atts ) );

	STATIC $i = 0;
	$i++;

	// Extract the tab titles for use in the tab widget.
	preg_match_all( '/tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

	$tab_titles = array();
	if( isset( $matches[1] ) ){ $tab_titles = $matches[1]; }

	$output = '';

	if( count( $tab_titles ) ) {
	    $output .= '<div id="junkie-tabs-'. $i .'" class="junkie-tabs"><div class="junkie-tab-inner">';
		$output .= '<ul class="junkie-nav junkie-clearfix">';

		foreach( $tab_titles as $tab ){
			$output .= '<li><a href="#junkie-tab-'. sanitize_title( $tab[0] ) .'">' . $tab[0] . '</a></li>';
		}

	    $output .= '</ul>';
	    $output .= do_shortcode( $content );
	    $output .= '</div></div>';
	} else {
		$output .= do_shortcode( $content );
	}

	return $output;
}

add_shortcode( 'junkie-tabs', 'junkie_tabs_shortcode' );

function junkie_tab_shortcode( $atts, $content = null ) {

	$defaults = array( 'title' => __( 'Tab', 'tj-shortcodes' ) );
	extract( shortcode_atts( $defaults, $atts ) );

	return '<div id="junkie-tab-'. sanitize_title( $title ) .'" class="junkie-tab">' . do_shortcode( stripslashes( $content ) ) . '</div>';

}

add_shortcode( 'junkie-tab', 'junkie_tab_shortcode' );