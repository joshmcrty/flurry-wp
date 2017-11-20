<?php
/*
Plugin Name: Flurry
Plugin URI: https://github.com/joshmcrty/flurry-wp
Description: Adds falling snow to your site using the Flurry plugin for jQuery.
Version: 1.0.1
Author: Josh McCarty
Author URI: http://joshmccarty.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: flurry
Domain Path: /languages

Copyright 2016 Josh McCarty  (email : josh@joshmccarty.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Enable internationalization
load_plugin_textdomain( 'flurry', false, basename( dirname( __FILE__ ) ) . 'languages' );

/**
 * Unregisters flurry_options
 */
function flurry_deactivate() {
	unregister_setting( 'flurry_options', 'flurry_settings' );
}
register_deactivation_hook( __FILE__, 'flurry_deactivate' );

/**
 * Register the form setting for our flurry array.
 *
 * @since Flurry 1.0
 */
function flurry_settings_init() {

	// Register the setting
	register_setting(
		'flurry_options',         // Name for the group of settings
		'flurry_settings',        // Name of the actual option to sanitize and save in the database
		'flurry_options_validate' // Name of the function to validate settings
	);

	// Add section to group basic option fields into
	add_settings_section(
		'flurry_basic_options_section',           // Unique identifier for the section
		__( 'Configure Falling Snow', 'flurry' ), // Title of the section displayed on the page
		'flurry_basic_options_section_callback',  // Callback function to run when the section is output on the page (can be used to output additional HTML before the fields are rendered)
		'flurry_options'                          // Menu page to display this section on; see `flurry_add_admin_menu()`
	);

	// Add a section to group advanced option fields into
	add_settings_section(
		'flurry_advanced_options_section',
		__( 'Advanced Options', 'flurry' ),
		'flurry_advanced_options_section_callback',
		'flurry_options'
	);
	
	// Get the array of settings fields
	$flurry_settings = flurry_settings_fields();

	// Register individual settings fields
	foreach ( $flurry_settings as $setting ) {

		$id = 'flurry_' . $setting['name'] . '_' . $setting['type'];
		$label = $setting['label'];
		$callback = 'flurry_' . $setting['type'] . '_render';
		$callback_args = $setting;
		$callback_args['id'] = $id;
		if ( !isset( $setting['no_label_for'] ) ) {
			$callback_args['label_for'] = $id;
		}
		$section = isset( $setting['section'] ) ? $setting['section'] : 'flurry_basic_options_section';
		add_settings_field(
			$id,              // HTML `id` attribute to use for the field when rendered
			$label,           // Label displayed for the field
			$callback,        // Callback function to render the field
			'flurry_options', // Menu page to display this field on; see `flurry_add_admin_menu_`
			$section,         // Identifier of the section to include this field in; see first argument in `add_settings_section()`
			$callback_args    // Arguments to pass to the callback function; `label_for` outputs a label with a `for` attribute matching the field `id` attribute
		);
	}
}
add_action( 'admin_init', 'flurry_settings_init' );

/**
 * Add Flurry options page to the admin menu under 'Appearance'.
 */
function flurry_add_admin_menu() {
	add_submenu_page(
		'themes.php',         // Which admin section to show this page under
		'Flurry',             // Name of the page
		'Flurry',             // Label in the admin menu
		'manage_options',     // Capability required to access this page
		'flurry',             // Menu slug used to uniquely identify the page
		'flurry_options_page' // Function that renders the page
	);
}
add_action( 'admin_menu', 'flurry_add_admin_menu' );

/**
 * Adds a settings link to the plugin listing page
 * @param array $links The default action links shown for the plugin
 */
function flurry_add_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'themes.php?page="flurry"' ) ) . '">' . __( 'Settings', 'flurry' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

/**
 * Adds the plugin action link filter for the plugin listing page
 */
function flurry_after_setup_theme() {
		 add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'flurry_add_action_links' );
}
add_action( 'after_setup_theme', 'flurry_after_setup_theme' );

/**
 * Provides information about the settings fields for Flurry
 *
 * @return array An array of arrays with each field's information
 */
function flurry_settings_fields() {
	
	$monthValues = array(
		1 => __( 'January', 'flurry' ),
		2 => __( 'February', 'flurry' ),
		3 => __( 'March', 'flurry' ),
		4 => __( 'April', 'flurry' ),
		5 => __( 'May', 'flurry' ),
		6 => __( 'June', 'flurry' ),
		7 => __( 'July', 'flurry' ),
		8 => __( 'August', 'flurry' ),
		9 => __( 'September', 'flurry' ),
		10 => __( 'October', 'flurry' ),
		11 => __( 'November', 'flurry' ),
		12 => __( 'December', 'flurry' ),
	);

	return array(
		array(
			'name' => 'character',
			'type' => 'checkbox',
			'values' => flurry_character_options(),
			'label' => __( 'Snowflake Character', 'flurry' ),
			'no_label_for' => 1,
			'description' => __( 'Determines the character(s) to use for snowflakes. If more than one are chosen, each flake will randomly select a character to use. The "snowflake" and "snowman" characters may be converted into emoji by WordPress.', 'flurry' ),
		),
		array(
			'name' => 'color',
			'type' => 'color',
			'label' => __( 'Color', 'flurry' ),
			'description' => __( 'Determines the CSS <code>color</code> of the snowflake. Default is <code>#ffffff</code> (white).', 'flurry' ),
		),
		array(
			'name' => 'height',
			'type' => 'number',
			'min' => '10',
			'label' => __( 'Height', 'flurry' ),
			'description' => __( 'Controls how far down the page the snow will fall (in pixels). Default is <code>200</code>.', 'flurry' ),
		),
		array(
			'name' => 'frequency',
			'type' => 'number',
			'label' => __( 'Frequency', 'flurry' ),
			'description' => __( 'Controls how frequently new snowflakes are generated (in milliseconds). Default is <code>100</code>.', 'flurry' ),
		),
		array(
			'name' => 'speed',
			'type' => 'number',
			'min' => '100',
			'label' => __( 'Speed', 'flurry' ),
			'description' => __( 'Controls how long it takes for each flake to fall (in milliseconds). Default is <code>3000</code>.', 'flurry' ),
		),
		array(
			'name' => 'small',
			'type' => 'number',
			'min' => '1',
			'label' => __( 'Small', 'flurry' ),
			'description' => __( 'Determines the font size of the smallest snowflakes in pixels. Default is <code>8</code>.', 'flurry' ),
		),
		array(
			'name' => 'large',
			'type' => 'number',
			'min' => '1',
			'label' => __( 'Large', 'flurry' ),
			'description' => __( 'Determines the font size of the largest snowflakes in pixels. Default is <code>20</code>.', 'flurry' ),
		),
		array(
			'name' => 'disableBlur',
			'type' => 'checkbox',
			'label' => __( 'Disable Blur', 'flurry' ),
			'checkbox_label' => __( 'Removes the blur/depth-of-field effect on smaller snowflakes.', 'flurry' ),
		),
		array(
			'name' => 'wind',
			'type' => 'number',
			'min' => '-9999',
			'label' => __( 'Wind', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls how far to the left each snowflake will drift in pixels. Default is <code>40</code>. Use a negative number to make snowflakes drift to the right.', 'flurry' ),
		),
		array(
			'name' => 'windVariance',
			'type' => 'number',
			'label' => __( 'Wind Variance', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls how much each snowflake will randomly drift in pixels using the <b>wind</b> as a base; lower creates less random drift. Default is <code>20</code>.', 'flurry' ),
		),
		array(
			'name' => 'rotation',
			'type' => 'number',
			'label' => __( 'Rotation', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls how much each snowflake will rotate in degrees while it falls; lower is less rotation. Default is <code>90</code>.', 'flurry' ),
		),
		array(
			'name' => 'rotationVariance',
			'type' => 'number',
			'label' => __( 'Rotation Variance', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls how much each snowflake’s rotation will be randomized by in degrees; lower creates less random rotation. Default is <code>180</code>.'),
		),
		array(
			'name' => 'startOpacity',
			'type' => 'number',
			'max' => '100',
			'label' => __( 'Start Opacity', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls the percentage of <code>opacity</code> of the snowflakes when they start to fall. Default is <code>100</code> which is fully opaque.', 'flurry' ),
		),
		array(
			'name' => 'endOpacity',
			'type' => 'number',
			'min' => '0',
			'max' => '100',
			'label' => __( 'End Opacity', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Controls the percentage of <code>opacity</code> of the snowflakes when they finish falling and are removed from the page. Default is <code>0</code> which is fully transparent.', 'flurry' ),
		),
		array(
			'name' => 'opacityEasing',
			'type' => 'text',
			'label' => __( 'Opacity Easing', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Determines the easing function used to transition the flakes from their <b>startOpacity</b> to their <b>endOpacity</b>. Default is <code>cubic-bezier(1,.3,.6,.74)</code>. You may use any valid CSS transition-timing-function value.', 'flurry' ),
		),
		array(
			'name' => 'overflow',
			'type' => 'text',
			'label' => __( 'Overflow', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Sets the CSS overflow property for the element on the page that the flakes are generated within (typically the body element). Default is <code>hidden</code>.', 'flurry' ),
		),
		array(
			'name' => 'container',
			'type' => 'text',
			'label' => __( 'Snowflake Container', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Determines the element on the page that snowflakes will be generated within. Default is <code>body</code>. Use a valid jQuery selector e.g. <code>.site-header</code>.', 'flurry' ),
		),
		array(
			'name' => 'useRelative',
			'type' => 'select',
			'values' => array(
				'auto' => 'Auto',
				'always' => 'Always',
				'never' => 'Never',
			),
			'label' => __( 'Use Relative', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Determines whether CSS <code>position: relative</code> is applied to the <b>Snowflake Container</b>. Default is <code>Auto</code> which will apply <code>position: relative</code> to the container unless it is the <code>body</code> element.', 'flurry' ),
		),
		array(
			'name' => 'zIndex',
			'type' => 'number',
			'max' => '999999',
			'label' => __( 'Z Index', 'flurry' ),
			'section' => 'flurry_advanced_options_section',
			'description' => __( 'Sets the CSS z-index property for the snowflakes. Default is <code>9999</code>.', 'flurry' ),
		),
	);
}

/**
 * Provides a list of allowed HTML tags for use in settings descriptions (used with wp_kses())
 *
 * @return array An array of allows HTML tags for settings descriptions
 */
function flurry_safe_description_tags() {
	return array(
		'a' => array(
			'href' => array(),
			'title' => array(),
			'target' => array(),
		),
		'br' => array(),
		'b' => array(),
		'i' => array(),
		'em' => array(),
		'strong' => array(),
		'code' => array(),
	);
}

/**
 * Renders checkboxes/radios for settings
 *
 * @param  array $args Array containing the checkbox type (checkbox/radio), id, name, label, description, and/or values for multiple checkboxes/radios
 */
function flurry_checkbox_render( $args ) {

	$options = get_option( 'flurry_settings' );

	// If multiple values are provided, loop through them and output a checkbox for each value
	if ( isset( $args['values'] ) ) : ?>

		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $args['label'] ); ?></span></legend>

			<?php foreach ( $args['values'] as $value => $character ) :

				printf(
					'<label><input type="%1$s" name="flurry_settings[%3$s][]" %4$s value="%5$s"> %6$s</label><br>',
					$args['type'],
					$args['id'],
					$args['name'],
					checked( isset( $options[$args['name']] ) && in_array( $value, $options[$args['name']] ) ? $value : false, $value, false ),
					$value,
					$character
				);

			endforeach; ?>

		</fieldset>

	<?php // If multiple values are not provided, output a single checkbox
	else :

		printf(
			'<label><input type="%1$s" id="%2$s" name="flurry_settings[%3$s]" %4$s value="1"> %5$s</label>',
			$args['type'],
			$args['id'],
			$args['name'],
			checked( isset( $options[$args['name']] ) ? $options[$args['name']] : false, 1, false ),
			$args['checkbox_label']
		); ?>

	<?php endif;

	// Output field description if provided
	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . wp_kses( $args['description'], flurry_safe_description_tags() ) . '</p>';
	}
}

/**
 * Proxy function that calls flurry_checkbox_render to render radios
 *
 * @param  array $args Array containing the checkbox type (checkbox/radio), id, name, label, description, and/or values for multiple checkboxes/radios
 */
function flurry_radio_render( $args ) {
	flurry_checkbox_render( $args );
}

/**
 * Renders select dropdown fields for settings
 *
 * @param  array $args Array containing the id, name, label, description, and/or values for dropdown options
 */
function flurry_select_render( $args ) {

	echo '<select id="' . $args['id'] . '" name="' . $args['name'] . '">';
	echo '<option>' . __( 'Select &hellip;', 'flurry' ) . '</option>';

	foreach ( $args['values'] as $value => $displayValue ) :

		printf(
			'<option value="%1$s" %3$s> %2$s</option>',
			$value,
			$displayValue,
			selected( isset( $options[$args['name']] ) && in_array( $value, $options[$args['name']] ) ? $value : false, $value, false )
		);

	endforeach;

	echo '</select>';

	// Output field description if provided
	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . wp_kses( $args['description'], flurry_safe_description_tags() ) . '</p>';
	}
}

/**
 * Renders text fields for settings
 *
 * @param  array $args Array containing the field id, name, label, description, placeholder, etc.
 */
function flurry_text_render( $args ) {

	$options = get_option( 'flurry_settings' );

	printf(
		'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s">',
		esc_attr( $args['type'] ),
		esc_attr( $args['id'] ),
		'flurry_settings[' . esc_attr( $args['name'] ) . ']',
		esc_attr( isset( $options[$args['name']] ) ? $options[$args['name']] : '' )
	);

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . wp_kses( $args['description'], flurry_safe_description_tags() ) . '</p>';
	}
}

/**
 * Renders number fields for settings
 *
 * @param  array $args Array containing the field id, name, label, description, placeholder, min, max, etc.
 */
function flurry_number_render( $args ) {

	$options = get_option( 'flurry_settings' );

	printf(
		'<input type="%1$s" class="%2$s" min="%3$s" max="%4$s" step="1" id="%5$s" name="%6$s" value="%7$s">',
		esc_attr( $args['type'] ),
		isset( $args['class'] ) ? esc_attr( $args['class'] ) : 'small-text',
		isset( $args['min'] ) ? esc_attr( $args['min'] ) : '0',
		isset( $args['max'] ) ? esc_attr( $args['max'] ) : '9999',
		esc_attr( $args['id'] ),
		'flurry_settings[' . esc_attr( $args['name'] ) . ']',
		esc_attr( isset( $options[$args['name']] ) ? $options[$args['name']] : '' )
	);

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . wp_kses( $args['description'], flurry_safe_description_tags() ) . '</p>';
	}
}

function flurry_color_render( $args ) {

	$options = get_option( 'flurry_settings' );

	printf(
		'<input type="%1$s" class="flurry-color-picker" id="%2$s" name="%3$s" value="%4$s">',
		'text',
		esc_attr( $args['id'] ),
		'flurry_settings[' . esc_attr( $args['name'] ) . ']',
		esc_attr( isset( $options[$args['name']] ) ? $options[$args['name']] : '' )
	);

	if ( isset( $args['description'] ) ) {
		echo '<p class="description">' . wp_kses( $args['description'], flurry_safe_description_tags() ) . '</p>';
	}
}

/**
 * Returns an array of character options for Flurry
 *
 * @return array Character options to use as snowflakes
 */
function flurry_character_options() {
	$characters = array(
		'❄' => '❄ snowflake',
		'❅' => '❅ tight trifoliate snowflake',
		'❆' => '❆ heavy chevron snowflake',
		'☃' => '☃ snowman',
	);

	return apply_filters( 'flurry_character_options', $characters );
}

/**
 * Callback function used for the settings section. Outputs some descriptive text.
 */
function flurry_basic_options_section_callback() {
	echo __( '<p>Flurry already uses default settings, so you don’t have to change any settings unless you want to modify the falling snow effect.</p><p><strong>Note:</strong> If you leave a setting blank it will use the default value.</p>', 'flurry' );
}

/**
 * Callback function used for the settings section. Outputs some descriptive text.
 */
function flurry_advanced_options_section_callback() {
	echo __( '<p>You can fine-tune the falling snow with these advanced settings.</p><p><strong>Note:</strong> If you leave a setting blank it will use the default value.</p>', 'flurry' );
}

/**
 * Renders the page with the Flurry settings
 */
function flurry_options_page() {

	?>
	<div class="wrap">
		<h1>Flurry</h1>
		<?php settings_errors(); ?>
		<form action='options.php' method='post' novalidate>

			<?php
			settings_fields( 'flurry_options' );
			do_settings_sections( 'flurry_options' );
			submit_button();
			?>

		</form>
	</div>
	<?php
}

function flurry_options_validate( $input ) {

	// Make sure there is an array of settings
	if ( ! is_array( $input ) ) {
		return array();
	}

	// Create output array
	$output = array();

	// Get previous values
	$old_settings = get_option( 'flurry_settings' );

	// Get the array of settings fields
	$flurry_settings_fields = flurry_settings_fields();

	foreach( $flurry_settings_fields as $setting ) {

		$name = $setting['name'];
		$type = $setting['type'];

		// Only validate fields if they are set
		if ( isset( $input[$name] ) && $input[$name] !== '' ) {

			// Validate text fields
			if ( $type === 'text' ) {
				$output[$name] = sanitize_text_field( $input[$name] );
			}

			// Validate single checkboxes
			if ( $type === 'checkbox' && ! isset( $setting['values'] ) ) {
				$output[$name] = 1; // If checked, should always be "1"
			}

			// Validate multiple checkboxes
			if ( $type === 'checkbox' && isset( $setting['values'] ) ) {

				// Ensure that each checked box is in the array of possible values for the field
				foreach ( $input[$name] as $value ) {
					if ( array_key_exists( $value, $setting['values'] ) ) {
						$output[$name][] = $value;
					}
				}
			}

			// Validate number fields
			if ( $type === 'number' ) {

				// Ensure this is a number
				if ( is_numeric( $input[$name] ) ) {

					// Ensure the number is geq the min value (no floats, only integers)
					if ( isset( $setting['min'] ) && intval( $input[$name] ) < intval( $setting['min'] ) ) {

						// Show error message
						add_settings_error(
							'flurry_options',
							$name . '-above-max',
							sprintf(
								__( '%1$s was set to “%2$s.” It must be at least %3$s.', 'flurry' ),
								esc_html( $setting['label'] ),
								esc_html( $input[$name] ),
								esc_html( $setting['min'] )
							),
							'error'
						);

						// Reset to last valid value or the minimum value
						if ( isset( $old_settings[$name] ) ) {
							$input[$name] = $old_settings[$name];
						} else {
							$input[$name] = $setting['min'];
						}
					}

					// Ensure the number is leq the max value (no floats, only integers)
					if ( isset( $setting['max'] ) && intval( $input[$name] ) > intval( $setting['max'] ) ) {

						// Show error message
						add_settings_error(
							'flurry_options',
							$name . '-above-max',
							sprintf(
								__( '%1$s was set to “%2$s.” It must be no greater than %3$s.', 'flurry' ),
								esc_html( $setting['label'] ),
								esc_html( $input[$name] ),
								esc_html( $setting['max'] )
							),
							'error'
						);

						// Reset to last valid value or the maximum value
						if ( isset( $old_settings[$name] ) ) {
							$input[$name] = $old_settings[$name];
						} else {
							$input[$name] = $setting['max'];
						}
					}

					// Set output to validated input value (no floats, only integers)
					$output[$name] = intval( $input[$name] );

				} else {

					// Not a number
					add_settings_error(
						'flurry_options',
						$name . '-not-numeric',
						sprintf(
							__( '%1$s was set to “%2$s.” It must be a number.', 'flurry' ),
							esc_html( $setting['label'] ),
							esc_html( $input[$name] )
						),
						'error'
					);
				}
			}

			// Validate color fields
			if ( $type === 'color' ) {

				// Ensure color is a valid hex color
				if ( ! preg_match( '/^#[a-f0-9]{6}$/i', $input[$name] ) ) {

					// Not a valid hex color
					add_settings_error(
						'flurry_options',
						$name . '-not-hex-color',
						sprintf(
							__( '%1$s was set to “%2$s.” Please select a valid color in the format “#ffffff.”', 'flurry' ),
							esc_html( $setting['label'] ),
							esc_html( $input[$name] )
						),
						'error'
					);

					// Reset to last valid value if available
					if ( isset( $old_settings[$name] ) ) {
						$input[$name] = $old_settings[$name];
					}
				} else {
					$output[$name] = $input[$name];
				}
			}

		}

	}

	return $output;
}

/**
 * Load CSS and JavaScript for the flurry options page
 *
 * @param  string $hook The page hook for admin_enqueue_scripts
 */
function flurry_load_admin_scripts( $hook ) {
		if ( 'appearance_page_flurry' !== $hook ) {
				return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'flurry_admin', plugins_url( 'admin/js/flurry-admin.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'flurry_load_admin_scripts' );

/**
 * Enqueues the public-facing Flurry script and outputs settings via wp_localize_script()
 */
function flurry_load_flurry_scripts() {

	$options = get_option( 'flurry_settings' );

	// Format array of characters to string of characters
	if ( isset( $options['character'] ) ) {
		$options['character'] = implode( '', $options['character']);
	}

	// Set `blur` to false if `disableBlur` is checked
	if ( isset( $options['disableBlur'] ) ) {
		$options['blur'] = 0;
		unset( $options['disableBlur'] );
	}

	// Set opacity settings to valid CSS values
	if ( isset( $options['startOpacity'] ) ) {
		$options['startOpacity'] = intval( $options['startOpacity'] ) / 100;
	}
	if ( isset( $options['endOpacity'] ) ) {
		$options['endOpacity'] = intval( $options['endOpacity'] ) / 100;
	}

	// Set jQuery selector for Flurry
	if ( ! isset( $options['container'] ) || $options['container'] === '' ) {
		$options['container'] = 'body';
	}

	// Remove any empty settings from the array
	$present_options = array_diff( $options, array( '' ) );

	// Enqueue Flurry jQuery plugin
	wp_enqueue_script( 'flurry', plugins_url( 'public/js/jquery.flurry.min.js', __FILE__ ), array( 'jquery' ), false, true );

	// Output Flurry settings on the page
	wp_localize_script( 'flurry' , 'flurryOptions', $present_options );

	// Print script to call Flurry plugin with settings provided
	$inline_script = 'jQuery( document ).ready( function( $ ) {

		// Cast boolean options as boolean if necessary
		if ( flurryOptions.blur ) {
			flurryOptions.blur = flurryOptions.blur === "true";
		}
		$( flurryOptions.container ).flurry( flurryOptions );
	});';
	wp_add_inline_script( 'flurry', $inline_script );
}
add_action( 'wp_enqueue_scripts', 'flurry_load_flurry_scripts' );
