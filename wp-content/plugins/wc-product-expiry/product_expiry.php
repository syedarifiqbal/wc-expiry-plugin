<?php

/**
 * The plugin bootstrap file
 *
 * @woocommerce-plugin
 * Plugin Name:       Woocommerce Product Expiry
 * Description:       Allow admin set expiry for the products to get notification before x days.
 * Version:           1.0.0
 * Author:            Syed Arif Iqbal
 * Author URI:        https://webiloop.com
 */

defined('WPINC') || die;

class WC_product_expiry
{
	/* Bootstraps the class and hooks required actions & filters.
     *
     */
	public static function init()
	{
		add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
		add_action('woocommerce_settings_tabs_product_expiry', __CLASS__ . '::settings_tab');
		add_action('woocommerce_update_options_product_expiry', __CLASS__ . '::update_settings');
		/* Tab content */
		add_action('woocommerce_product_data_panels', __CLASS__ . '::product_panels');
		/* Product Data Tab */
		add_filter('woocommerce_product_data_tabs', __CLASS__ . '::expiry_product_options');
		add_action('woocommerce_process_product_meta', __CLASS__ . '::save_fields', 10, 2);

		if (!is_admin() && isset($_GET['cron'])) {
			add_action('wp_loaded', __CLASS__ . '::notify_via_email');
		}
	}

	/* Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
	public static function add_settings_tab($settings_tabs)
	{
		$settings_tabs['product_expiry'] = __('Product Expiry', 'arif-woocommerce-product-expiry');
		return $settings_tabs;
	}


	/* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
	public static function settings_tab()
	{
		woocommerce_admin_fields(self::get_settings());
	}


	/* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
	public static function update_settings()
	{
		woocommerce_update_options(self::get_settings());
	}


	/* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
	public static function get_settings()
	{

		$settings = array(
			'section_title' => array(
				'name'     => __('Notification Settings', 'arif-woocommerce-product-expiry'),
				'type'     => 'title',
				'desc'     => '',
				'id'       => 'wc_product_expiry_section_title'
			),
			'title' => array(
				'name' => __('Email Address', 'arif-woocommerce-product-expiry'),
				'type' => 'email',
				'desc' => __('Type the email address that you want to receive notification email.', 'arif-woocommerce-product-expiry'),
				'id'   => 'wc_product_expiry_email',
				'desc_tip' => true,
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_product_expiry_section_end'
			)
		);

		return apply_filters('wc_product_expiry_settings', $settings);
	}

	/* Product expiry will be notified to the admin email which.
     *
     * @return void.
     */
	public static function notify_via_email()
	{
		global $wpdb;

		$query = "SELECT
			`date`.`post_id`,
			`date`.`meta_id` AS `date_meta_id`,
			`date`.`meta_value` AS `expiry_date`,
			`day`.`meta_id` AS `day_meta_id`,
			`day`.`meta_value` AS `expiry_in`,
			post.*
			FROM
				`$wpdb->postmeta` AS `date`
			JOIN `$wpdb->postmeta` AS `day`
			ON
				`day`.`post_id` = `date`.`post_id`
			JOIN `$wpdb->posts` AS post
			ON
				post.id = `date`.`post_id`
			WHERE
			`date`.`meta_key` = 'expiry_date' AND `day`.`meta_key` = 'expiry_in' AND CURRENT_DATE = `date`.`meta_value` - INTERVAL `day`.`meta_value` DAY;";

			$results = $wpdb->get_results($query);

			if(count($results) === 0) return;

			$to = get_option('wc_product_expiry_email', true);
			$subject = 'Product Expiry Notification';
			$headers = ['Content-Type: text/html; charset=UTF-8; Subject: Arifiqbal'];
			$message = 'Following Products are about to out of stock.
			<table border="1" cellpadding="10" style="margin-top: 20px; width: 100%;">
			
			<thead>
				<tr>
				<th>Product Name</th>
				<th>Expiry Date</th>
				<th>Notify Before</th>
				</tr>
			</thead>
			<tbody>';

		foreach ($results as $product) {
			$message .= "<tr>
				<td>{$product->post_title}</td>
				<td>{$product->expiry_date}</td>
				<td>Before <strong>{$product->expiry_in}</strong> Days</td>
				</tr>";
		}

		$message .= "</tbody>
		
		</table>";

		$result = wp_mail($to, $subject, $message, $headers);
	}

	/* Add Expiry Product tab on product page in data table.
     *
     * @return void.
     */
	public static function product_panels()
	{
		echo '<div id="arif_product_data" class="panel woocommerce_options_panel hidden">';

		woocommerce_wp_text_input(array(
			'id'                => 'expiry_date',
			'value'             => get_post_meta(get_the_ID(), 'expiry_date', true),
			'label'             => 'Expiry Date',
			'type'              => 'date',
			// 'desc_tip'          => true,
			// 'description'       => 'Description when desc_tip param is not true'
		));

		$expire_in = get_post_meta(get_the_ID(), 'expiry_in', true);

		woocommerce_wp_text_input(array(
			'id'                => 'expiry_in',
			'value'             =>  $expire_in ? $expire_in : 5,
			'label'             => 'Alter before',
			'type'              => 'number',
			'description'       => 'Days',
		));

		echo '</div>';
	}

	/* Add Expiry Product tab on product page in data table.
     *
     * @return void.
     */
	public static function expiry_product_options($tabs)
	{
		$tabs['arif'] = array(
			'label'    => 'Product Expiry',
			'target'   => 'arif_product_data',
			'priority' => 21,
		);
		return $tabs;
	}

	/* Save the field value to the database.
     *
	 * @params $meta_id, $post
     * @return void.
     */
	public static function save_fields($id, $post)
	{
		update_post_meta($id, 'expiry_date', $_POST['expiry_date']);
		update_post_meta($id, 'expiry_in', $_POST['expiry_in']);
	}
}

WC_product_expiry::init();
