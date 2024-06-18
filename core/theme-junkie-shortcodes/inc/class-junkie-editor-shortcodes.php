<?php

/**
 * Creates the admin interface to add shortcodes to the editor.
 *
 * @package    Theme_Junkie_Shortcodes
 * @since      0.1.0
 * @author     Theme Junkie
 * @copyright  Copyright (c) 2014-2015, Theme Junkie
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

class Junkie_Editor_Shortcodes
{

	/**
	 * Constructor method
	 *
	 * @since  0.1.0
	 */
	public function __construct()
	{

		// Adds the media button.
		add_action('media_buttons', array($this, 'media_button'), 20);

		// Print the needed code for the shortcodes generator.
		add_action('admin_footer', array($this, 'junkie_popup_html'));
	}

	/**
	 * Adds the media button.
	 *
	 * @since  0.1.0
	 */
	public function media_button($editor_id = 'content')
	{

		// Check if the current screen is post base.
		if ('post' != get_current_screen()->base) {
			return;
		}

		echo '<a href="#TB_inline?width=4000&amp;inlineId=junkie-choose-shortcode" class="thickbox button junkie-thicbox" title="' . __('Add Shortcode', 'tj-shortcodes') . '">' . __('Add Shortcode', 'tj-shortcodes') . '</a>';
	}

	/**
	 * Build out the input fields for shortcode content
	 *
	 * @since  0.1.0
	 * @param  string $key
	 * @param  array $param the parameters of the input
	 * @return void
	 */
	public function junkie_build_fields($key, $param)
	{

		$html = '<tr>';
		$html .= '<td class="label">' . esc_html($param['label']) . ':</td>';
		switch ($param['type']) {

			case 'text':

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . esc_attr($key) . '">' . esc_html($param['label']) . '</label>';
				$output .= '<input type="text" class="junkie-form-text junkie-input" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" value="' . $param['std'] . '" />' . "\n";
				$output .= '<span class="junkie-form-desc">' . esc_html($param['desc']) . '</span></td>' . "\n";

				// append
				$html .= stripslashes($output);

				break;

			case 'textarea':

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . esc_attr($key) . '">' . esc_html($param['label']) . '</label>';
				$output .= '<textarea rows="10" cols="30" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" class="junkie-form-textarea junkie-input">' . $param['std'] . '</textarea>' . "\n";
				$output .= '<span class="junkie-form-desc">' . esc_html($param['desc']) . '</span></td>' . "\n";

				// append
				$html .= stripslashes($output);

				break;

			case 'select':

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . esc_attr($key) . '">' . esc_html($param['label']) . '</label>';
				$output .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" class="junkie-form-select junkie-input">' . "\n";

				foreach ($param['options'] as $value => $option) {
					$output .= '<option value="' . esc_attr($value) . '">' . esc_attr($option) . '</option>' . "\n";
				}

				$output .= '</select>' . "\n";
				$output .= '<span class="junkie-form-desc">' . esc_html($param['desc']) . '</span></td>' . "\n";

				// append
				$html .= stripslashes($output);

				break;

			case 'checkbox':

				// prepare
				$output = '<td><label class="screen-reader-text" for="' . esc_attr($key) . '">' . esc_html($param['label']) . '</label>';
				$output .= '<input type="checkbox" name="' . esc_attr($key) . '" id="' . esc_attr($key) . '" class="junkie-form-checkbox junkie-input"' . ($param['default'] ? 'checked' : '') . '>' . "\n";
				$output .= '<span class="junkie-form-desc">' . esc_html($param['desc']) . '</span></td>';

				$html .= stripslashes($output);

				break;

			default:
				break;
		}

		$html .= '</tr>';

		return $html;
	}

	/**
	 * Popup window
	 *
	 * Print the footer code needed for the Insert Shortcode Popup
	 *
	 * @since  0.1.0
	 * @global $pagenow
	 * @return void Prints HTML
	 */
	function junkie_popup_html()
	{
		require_once(TJSH_INC . 'config.php');

		// Check if the current screen is post base.
		if ('post' != get_current_screen()->base) {
			return;
		}
?>

		<script type="text/javascript">
			function Junkie_Insert_Shortcode() {
				// Grab input content, build the shortcodes, and insert them
				// into the content editor
				var select = jQuery('#select-junkie-shortcode').val(),
					type = select.replace('junkie-', '').replace('-shortcode', ''),
					template = jQuery('#' + select).data('shortcode-template'),
					childTemplate = jQuery('#' + select).data('shortcode-child-template'),
					tables = jQuery('#' + select).find('table').not('.junkie-clone-template'),
					attributes = '',
					content = '',
					contentToEditor = '';

				// go over each table, build the shortcode content
				for (var i = 0; i < tables.length; i++) {
					var elems = jQuery(tables[i]).find('input, select, textarea');

					// Build an attributes string by mapping over the input
					// fields in a given table.
					attributes = jQuery.map(elems, function(el, index) {
						var $el = jQuery(el);

						console.log(el);

						if ($el.attr('id') === 'content') {
							content = $el.val();
							return '';
						} else if ($el.attr('id') === 'last') {
							if ($el.is(':checked')) {
								return $el.attr('id') + '="true"';
							} else {
								return '';
							}
						} else {
							return $el.attr('id') + '="' + $el.val() + '"';
						}
					});
					attributes = attributes.join(' ').trim();

					// Place the attributes and content within the provided
					// shortcode template
					if (childTemplate) {
						// Run the replace on attributes for columns because the
						// attributes are really the shortcodes
						contentToEditor += childTemplate.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
					} else {
						// Run the replace on attributes for columns because the
						// attributes are really the shortcodes
						contentToEditor += template.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
					}
				};

				// Insert built content into the parent template
				if (childTemplate) {
					contentToEditor = template.replace('{{child_shortcode}}', contentToEditor);
				}

				// Send the shortcode to the content editor and reset the fields
				window.send_to_editor(contentToEditor);
				Junkie_Reset_Fields();
			}

			// Set the inputs to empty state
			function Junkie_Reset_Fields() {
				jQuery('#junkie-shortcode-title').text('');
				jQuery('#junkie-shortcode-wrap').find('input[type=text], select').val('');
				jQuery('#junkie-shortcode-wrap').find('textarea').text('');
				jQuery('.junkie-was-cloned').remove();
				jQuery('.junkie-shortcode-type').hide();
			}

			// Function to redraw the thickbox for new content
			function Junkie_Resize_TB() {
				var ajaxCont = jQuery('#TB_ajaxContent'),
					tbWindow = jQuery('#TB_window'),
					junkiePopup = jQuery('#junkie-shortcode-wrap');

				ajaxCont.css({
					height: (tbWindow.outerHeight() - 47),
					overflow: 'auto', // IMPORTANT
					width: (tbWindow.outerWidth() - 30)
				});
			}

			// Simple function to clone an included template
			function JunkieCloneContent(el) {
				var clone = jQuery(el).find('.junkie-clone-template').clone().removeClass('hidden junkie-clone-template').removeAttr('id').addClass('junkie-was-cloned');

				jQuery(el).append(clone);
			}

			jQuery(document).ready(function($) {
				var $shortcodes = $('.junkie-shortcode-type').hide(),
					$title = $('#junkie-shortcode-title');

				// Show the selected shortcode input fields
				$('#select-junkie-shortcode').change(function() {
					var text = $(this).find('option:selected').text();

					$shortcodes.hide();
					$title.text(text);
					$('#' + $(this).val()).show();
					Junkie_Resize_TB();
				});

				// Clone a set of input fields
				$('.clone-content').on('click', function() {
					var el = $(this).siblings('.junkie-sortable');

					JunkieCloneContent(el);
					Junkie_Resize_TB();
					$('.junkie-sortable').sortable('refresh');
				});

				// Remove a set of input fields
				$('.junkie-shortcode-type').on('click', '.junkie-remove', function() {
					$(this).closest('table').remove();
				});

				// Make content sortable using the jQuery UI Sortable method
				$('.junkie-sortable').sortable({
					items: 'table:not(".hidden")',
					placeholder: 'junkie-sortable-placeholder'
				});
			});
		</script>

		<div id="junkie-choose-shortcode" style="display: none;">
			<div id="junkie-shortcode-wrap" class="wrap junkie-shortcode-wrap">

				<div class="junkie-shortcode-select">
					<label for="junkie-shortcode"><?php _e('Select the shortcode type', 'tj-shortcodes'); ?></label>
					<select name="junkie-shortcode" id="select-junkie-shortcode">
						<option><?php _e('Select Shortcode', 'junkie'); ?></option>
						<?php foreach ($junkie_shortcodes as $shortcode) {
							echo '<option data-title="' . $shortcode['title'] . '" value="' . $shortcode['id'] . '">' . $shortcode['title'] . '</option>';
						} ?>
					</select>
				</div>

				<h3 id="junkie-shortcode-title"></h3>

				<?php

				$html = '';
				$clone_button = array('show' => false);

				// Loop through each shortcode building content
				foreach ($junkie_shortcodes as $key => $shortcode) {

					// Add shortcode templates to be used when building with JS
					$shortcode_template = ' data-shortcode-template="' . $shortcode['template'] . '"';
					if (array_key_exists('child_shortcode', $shortcode)) {
						$shortcode_template .= ' data-shortcode-child-template="' . $shortcode['child_shortcode']['template'] . '"';
					}

					// Individual shortcode 'block'
					$html .= '<div id="' . $shortcode['id'] . '" class="junkie-shortcode-type" ' . $shortcode_template . '>';

					// If shortcode has children, it can be cloned and is sortable.
					// Add a hidden clone template, and set clone button to be displayed.
					if (array_key_exists('child_shortcode', $shortcode)) {

						// This array thrown an undefined index, I don't know what exactly is this for.
						// $html .= $shortcode['child_shortcode']['shortcode'];
						// ===

						$shortcode['params'] = $shortcode['child_shortcode']['params'];
						$clone_button['show'] = true;
						$clone_button['text'] = $shortcode['child_shortcode']['clone_button'];
						$html .= '<div class="junkie-sortable">';
						$html .= '<table id="clone-' . $shortcode['id'] . '" class="hidden junkie-clone-template"><tbody>';
						foreach ($shortcode['params'] as $key => $param) {
							$html .= $this->junkie_build_fields($key, $param);
						}
						if ($clone_button['show']) {
							$html .= '<tr><td colspan="2"><a href="#" class="junkie-remove">' . __('Remove', 'junkie') . '</a></td></tr>';
						}
						$html .= '</tbody></table>';
					}

					// Build the actual shortcode input fields
					$html .= '<table><tbody>';
					foreach ($shortcode['params'] as $key => $param) {
						$html .= $this->junkie_build_fields($key, $param);
					}

					// Add a link to remove a content block
					if ($clone_button['show']) {
						$html .= '<tr><td colspan="2"><a href="#" class="junkie-remove">' . __('Remove', 'tj-shortcodes') . '</a></td></tr>';
					}
					$html .= '</tbody></table>';

					// Close out the sortable div and display the clone button as needed
					if ($clone_button['show']) {
						$html .= '</div>';
						$html .= '<a id="add-' . $shortcode['id'] . '" href="#" class="button-secondary clone-content">' . $clone_button['text'] . '</a>';
						$clone_button['show'] = false;
					}

					// Display notes if provided
					if (array_key_exists('notes', $shortcode)) {
						$html .= '<p class="junkie-notes">' . $shortcode['notes'] . '</p>';
					}
					$html .= '</div>';
				}

				echo $html;
				?>

				<p class="submit">
					<input type="button" id="junkie-insert-shortcode" class="button-primary" value="<?php _e('Insert Shortcode', 'tj-shortcodes'); ?>" onclick="Junkie_Insert_Shortcode();" />
					<a href="#" id="junkie-cancel-shortcode-insert" class="button-secondary junkie-cancel-shortcode-insert" onclick="tb_remove();"><?php _e('Cancel', 'tj-shortcodes'); ?></a>
				</p>
			</div>
		</div>

<?php

	}
}

new Junkie_Editor_Shortcodes();
