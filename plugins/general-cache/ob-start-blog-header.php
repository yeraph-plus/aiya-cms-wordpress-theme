<?php

if (!isset($wp_did_header)) {

	$wp_did_header = true;

	ob_start();

	// Load the WordPress library.
	require_once __DIR__ . '/wp-load.php';

	// Set up the WordPress query.
	wp();

	ob_end_clean();

	// Load the theme template.
	require_once ABSPATH . WPINC . '/template-loader.php';
}
