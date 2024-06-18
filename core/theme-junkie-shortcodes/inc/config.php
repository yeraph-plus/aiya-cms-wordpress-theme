<?php
/**
 * Define the shortcode parameters
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/* Button Config --- */

$junkie_shortcodes['button'] = array(
	'title'    => __( 'Button', 'tj-shortcodes' ),
	'id'       => 'junkie-button-shortcode',
	'template' => '[junkie-button {{attributes}}] {{content}} [/junkie-button]',
	'params'   => array(
		'url'  => array(
			'std'   => 'http://example.com',
			'type'  => 'text',
			'label' => __( 'Button URL', 'tj-shortcodes' ),
			'desc'  => __( 'Add the button\'s url eg http://example.com', 'tj-shortcodes' )
		),
		'style' => array(
			'type'    => 'select',
			'label'   => __( 'Button Style', 'tj-shortcodes' ),
			'desc'    => __( 'Select the button\'s style, ie the button\'s colour', 'tj-shortcodes' ),
			'options' => array(
				'grey'       => __( 'Grey', 'tj-shortcodes' ),
				'black'      => __( 'Black', 'tj-shortcodes' ),
				'green'      => __( 'Green', 'tj-shortcodes' ),
				'light-blue' => __( 'Light Blue', 'tj-shortcodes' ),
				'blue'       => __( 'Blue', 'tj-shortcodes' ),
				'red'        => __( 'Red', 'tj-shortcodes' ),
				'orange'     => __( 'Orange', 'tj-shortcodes' ),
				'purple'     => __( 'Purple', 'tj-shortcodes' )
			)
		),
		'size' => array(
			'type'    => 'select',
			'label'   => __( 'Button Size', 'tj-shortcodes' ),
			'desc'    => __( 'Select the button\'s size', 'tj-shortcodes' ),
			'options' => array(
				'small'  => __( 'Small', 'tj-shortcodes' ),
				'medium' => __( 'Medium', 'tj-shortcodes' ),
				'large'  => __( 'Large', 'tj-shortcodes' )
			)
		),
		'type' => array(
			'type'    => 'select',
			'label'   => __( 'Button Type', 'tj-shortcodes' ),
			'desc'    => __( 'Select the button\'s type', 'tj-shortcodes' ),
			'options' => array(
				'round'  => __( 'Round', 'tj-shortcodes' ),
				'square' => __( 'Square', 'tj-shortcodes' )
			)
		),
		'target' => array(
			'type'    => 'select',
			'label'   => __( 'Button Target', 'tj-shortcodes' ),
			'desc'    => __( 'Set the browser behavior for the click action.', 'tj-shortcodes' ),
			'options' => array(
				'_self'  => __( 'Same window', 'tj-shortcodes' ),
				'_blank' => __( 'New window', 'tj-shortcodes' )
			)
		),
		'content' => array(
			'std'   => __( 'Button Text', 'tj-shortcodes' ),
			'type'  => 'text',
			'label' => __( 'Button\'s Text', 'tj-shortcodes' ),
			'desc'  => __( 'Add the button\'s text', 'tj-shortcodes' ),
		)
	)
);

/* Alert Config --- */

$junkie_shortcodes['alert'] = array(
	'title'    => __( 'Alert', 'tj-shortcodes' ),
	'id'       => 'junkie-alert-shortcode',
	'template' => '[junkie-alert {{attributes}}] {{content}} [/junkie-alert]',
	'params'   => array(
		'style' => array(
			'type'  => 'select',
			'label' => __( 'Alert Style', 'tj-shortcodes' ),
			'desc'  => __( 'Select the alert\'s style, ie the alert colour', 'tj-shortcodes' ),
			'options' => array(
				'white'  => __( 'White', 'tj-shortcodes' ),
				'grey'   => __( 'Grey', 'tj-shortcodes' ),
				'red'    => __( 'Red', 'tj-shortcodes' ),
				'yellow' => __( 'Yellow', 'tj-shortcodes' ),
				'green'  => __( 'Green', 'tj-shortcodes' )
			)
		),
		'content' => array(
			'std'   => __( 'Your Alert!', 'tj-shortcodes' ),
			'type'  => 'textarea',
			'label' => __( 'Alert Text', 'tj-shortcodes' ),
			'desc'  => __( 'Add the alert\'s text', 'tj-shortcodes' ),
		)

	)
);

/* Toggle Config --- */

$junkie_shortcodes['toggle'] = array(
	'title'    => __( 'Toggle', 'tj-shortcodes' ),
	'id'       => 'junkie-toggle-shortcode',
	'template' => ' {{child_shortcode}} ', // There is no wrapper shortcode
	'notes'    => __( 'Click \'Add Toggle\' to add a new toggle. Drag and drop to reorder toggles.', 'tj-shortcodes' ),
	'params'   => array(),
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Toggle Content Title', 'tj-shortcodes' ),
				'desc'  => __( 'Add the title that will go above the toggle content', 'tj-shortcodes' ),
				'std'  => __( 'Title', 'tj-shortcodes' )
			),
			'content' => array(
				'std'   => __( 'Content', 'tj-shortcodes' ),
				'type'  => 'textarea',
				'label' => __( 'Toggle Content', 'tj-shortcodes' ),
				'desc'  => __( 'Add the toggle content. Will accept HTML', 'tj-shortcodes' ),
			),
			'state' => array(
				'type'    => 'select',
				'label'   => __( 'Toggle State', 'tj-shortcodes' ),
				'desc'    => __( 'Select the state of the toggle on page load', 'tj-shortcodes' ),
				'options' => array(
					'open'   => __( 'Open', 'tj-shortcodes' ),
					'closed' => __( 'Closed', 'tj-shortcodes' )
				)
			)
		),
		'template'     => '[junkie-toggle {{attributes}}] {{content}} [/junkie-toggle]',
		'clone_button' => __( 'Add Toggle', 'tj-shortcodes' )
	)
);

/* Tabs Config --- */

$junkie_shortcodes['tabs'] = array(
	'title'    => __( 'Tab', 'tj-shortcodes' ),
	'id'       => 'junkie-tabs-shortcode',
	'template' => '[junkie-tabs] {{child_shortcode}} [/junkie-tabs]',
	'notes'    => __('Click \'Add Tag\' to add a new tag. Drag and drop to reorder tabs.', 'tj-shortcodes' ),
	'params'   => array(),
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'std'   => __( 'Title', 'tj-shortcodes' ),
				'type'  => 'text',
				'label' => __( 'Tab Title', 'tj-shortcodes' ),
				'desc'  => __( 'Title of the tab.', 'tj-shortcodes' ),
			),
			'content' => array(
				'std'   => __( 'Tab Content', 'tj-shortcodes' ),
				'type'  => 'textarea',
				'label' => __( 'Tab Content', 'tj-shortcodes' ),
				'desc'  => __( 'Add the tabs content.', 'tj-shortcodes' )
			)
		),
		'template'     => '[junkie-tab {{attributes}}] {{content}} [/junkie-tab]',
		'clone_button' => __( 'Add Tab', 'tj-shortcodes' )
	)
);

/* Columns Config --- */

$junkie_shortcodes['columns'] = array(
	'title'    => __( 'Columns', 'tj-shortcodes' ),
	'id'       => 'junkie-columns-shortcode',
	'template' => ' {{child_shortcode}} ', // There is no wrapper shortcode
	'notes'    => __( 'Click \'Add Column\' to add a new column. Drag and drop to reorder columns.', 'tj-shortcodes' ),
	'params'   => array(),
	'child_shortcode' => array(
		'params' => array(
			'column' => array(
				'type'    => 'select',
				'label'   => __( 'Column Type', 'tj-shortcodes' ),
				'desc'    => __( 'Select the width of the column.', 'tj-shortcodes' ),
				'options' => array(
					'one-third'    => __( 'One Third', 'tj-shortcodes' ),
					'two-third'    => __( 'Two Thirds', 'tj-shortcodes' ),
					'one-half'     => __( 'One Half', 'tj-shortcodes' ),
					'one-fourth'   => __( 'One Fourth', 'tj-shortcodes' ),
					'three-fourth' => __( 'Three Fourth', 'tj-shortcodes' ),
					'one-fifth'    => __( 'One Fifth', 'tj-shortcodes' ),
					'two-fifth'    => __( 'Two Fifth', 'tj-shortcodes' ),
					'three-fifth'  => __( 'Three Fifth', 'tj-shortcodes' ),
					'four-fifth'   => __( 'Four Fifth', 'tj-shortcodes' ),
					'one-sixth'    => __( 'One Sixth', 'tj-shortcodes' ),
					'five-sixth'   => __( 'Five Sixth', 'tj-shortcodes' )
				)
			),
			'last' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Last column', 'tj-shortcodes' ),
				'desc'    => __( 'Set whether this is the last column.', 'tj-shortcodes' ),
				'default' => false
			),
			'content' => array(
				'std'   => __( 'Column content', 'tj-shortcodes' ),
				'type'  => 'textarea',
				'label' => __( 'Column Content', 'tj-shortcodes' ),
				'desc'  => __( 'Add the column content.', 'tj-shortcodes' )
			)
		),
		'template'     => '[junkie-column {{attributes}}] {{content}} [/junkie-column]',
		'clone_button' => __( 'Add Column', 'tj-shortcodes' )
	)
);

/**
 * Dropcap
 */
$junkie_shortcodes['dropcap'] = array(
	'title'    => __( 'Dropcap', 'tj-shortcodes' ),
	'id'       => 'junkie-dropcap-shortcode',
	'template' => '[junkie-dropcap{{attributes}}]{{content}}[/junkie-dropcap]',
	'params'   => array(
		'content' => array(
			'std'   => __( 'A', 'tj-shortcodes' ),
			'type'  => 'text',
			'label' => __( 'Letter', 'tj-shortcodes' ),
			'desc'  => '',
		)
	)
);

/**
 * Highlights
 */
$junkie_shortcodes['hightlights'] = array(
	'title'    => __( 'Hightlights', 'tj-shortcodes' ),
	'id'       => 'junkie-hightlights-shortcode',
	'template' => '[junkie-hightlights {{attributes}}]{{content}}[/junkie-hightlights]',
	'params'   => array(
		'color' => array(
			'type'    => 'select',
			'label'   => __( 'Color', 'tj-shortcodes' ),
			'desc'    => __( 'Select the hightlight\'s color.', 'tj-shortcodes' ),
			'options' => array(
				'green'  => __( 'Green', 'tj-shortcodes' ),
				'grey'   => __( 'Grey', 'tj-shortcodes' ),
				'red'    => __( 'Red', 'tj-shortcodes' ),
				'yellow' => __( 'Yellow', 'tj-shortcodes' ),
				'blue'   => __( 'Blue', 'tj-shortcodes' ),
			)
		),
		'content' => array(
			'std'   => __( 'Hightlited words', 'tj-shortcodes' ),
			'type'  => 'text',
			'label' => __( 'Hightlited Words', 'tj-shortcodes' ),
			'desc'  => '',
		)
	)
);