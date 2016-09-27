<?php
/*

Plugin Name: Gravity HTTP Request Plugin
Plugin URI: http://www.chrisgoddard.me
Description: Send Gravity Forms submissions via HTTP request
Version: 0.1.1
Author: Chris Goddard
Author Email: chris@chrisgoddard.me
License:

Copyright 2014 Chris Goddard (chris@odddogmedia.com)

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


if ( !defined( 'GRAVITY_HTTP_PATH' ) ) define( 'GRAVITY_HTTP_PATH',   plugin_dir_path( __FILE__ ) . 'inc/' );
if ( !defined( 'GRAVITY_HTTP_URL' ) ) define( 'GRAVITY_HTTP_URL',   plugin_dir_url( __FILE__ ) . 'inc/' );

class GravityHTTPRequest {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Gravity HTTP Request';
	const slug = 'gravity_http_request';

	function __construct() {

		add_filter('gform_form_settings_menu', array($this, 'gravity_http_settings_menu'));

		add_action('gform_form_settings_page_'.self::slug, array($this, 'gravity_http_settings_page'));
		add_action('admin_enqueue_scripts', array($this, 'register_scripts_and_styles'));
		add_action('gform_after_submission', array($this, 'gravity_http_post_data'), 10, 2);
	}

	function gravity_http_post_data($entry, $form) {

		if ($form['http_request_settings']['http_request_active']):

		$post_url = $form['http_request_settings']['form_http_url'];

		$request = new WP_Http();

		foreach ( $form['fields'] as $field ) {

			if ( $field['inputs'] ) {

				if ($field['http_output'] === 'map') {

					foreach ($field['inputs'] as $input) {

						$id = (string)$input['id'];

						$post_body[$input['http_map']] = $entry[$id];
					}

				} elseif ($field['http_output'] === 'serialize') {

                    $serialize = [];

					foreach ($field['inputs'] as $input) {

						$id =  (string)$input['id'];

						$serialize[] = $entry[$id];
					}

					$post_body[$field['http_map']] = json_encode($serialize);
				}

			} else {

				if( ! isset($field['http_map'])) continue;

				$post_body[$field['http_map']] = $entry[$field['id']];
			}
		}

		if ($form['http_request_settings']['http_request_type'] === 'post_request') {
						
			$response = $request->request( $post_url, array( 'method' => 'POST', 'body' => $post_body, 'sslverify' => false ) );

		} elseif ($form['http_request_settings']['http_request_type'] === 'get_request') {

			$response = $request->request( $post_url, array( 'method' => 'GET', 'body' => $post_body) );
		}

		if (is_wp_error($response)) {

			$error = $response->get_error_message();
			error_log("WP Error:" . $error, 0);
		}

		else {

			$log = $response['body'];
			error_log("Form:" . $log, 0);
		}

		endif;
	}


	// add a custom menu item to the Form Settings page menu
	function gravity_http_settings_menu($menu_items) {

		$menu_items[] = array(
			'name' => self::slug,
			'label' => self::name
		);

		return $menu_items;
	}


	// handle displaying content for our custom menu when selected
	function gravity_http_settings_page() {

		//see if there is a form id in the querystring
		$form_id = RGForms::get("id");

		$form = GFAPI::get_form($form_id);

		if ( ! empty( $_POST ) &&  check_admin_referer('gforms_save_http_settings', 'gforms_save_http_settings') ) {

			$form['http_request_settings']['form_http_url'] = rgpost('form_http_url');
			$form['http_request_settings']['http_request_type'] = rgpost('form_request_type');
			$form['http_request_settings']['date_updated'] = rgpost('date_updated');
			$form['http_request_settings']['http_request_active'] = rgpost('http_request_active');

			foreach ($form['fields'] as $idx => $field) {

				if ($field['inputs']) {

					$form['fields'][$idx]['http_map'] = rgpost('field_'.$field['id'].'_map');
					$form['fields'][$idx]['http_output'] = rgpost('field_'.$field['id'].'_serialize');

					foreach ($field['inputs'] as $iidx => $input) {

						$input_id = str_replace('.', '_', $input['id']);

						$form['fields'][$idx]['inputs'][$iidx]['http_map'] = rgpost('field_'.$input_id.'_map');
					}

				} else {

					$form['fields'][$idx]['http_map'] = rgpost('field_'.$field['id'].'_map');

					if ($field['choices']) {

						$form['fields'][$idx]['value_output'] = rgpost('field_'.$field['id'].'_value_output');
					}
				}

			}

			$result = GFAPI::update_form($form);
		}

		GFFormSettings::page_header();

		include_once(GRAVITY_HTTP_PATH.'settings-screen.php');
		
		GFFormSettings::page_footer();
	}

	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	function register_scripts_and_styles() {
		if ( is_admin() ) {

			wp_register_style('gravity_http_styles', GRAVITY_HTTP_URL . 'css/plugin-admin.css');
			wp_enqueue_style('gravity_http_styles');

		} else {

		} 
	}

} 

if (class_exists('GFForms')) {
	new GravityHTTPRequest();
}

