<?php
/**
 * Plugin Name: CartGateway
 * Plugin URI: https://cartgateway.com/
 * Description: CartGateway Is The Best Global Payments Plugin for WooCommerce. The CartGateway plugin for WooCommerce allows you to accept payments directly on your store for web and mobile. With CartGateway, customers stay on your store during checkout instead of being redirected to an externally hosted checkout page, which has been proven to lead to higher conversion rates. Start taking payments today!
 * Version: 1.0.0
 * Author: CartGateway Inc.
 * Author Email: support@cartgateway.com
 * License: GPLv3 or later License
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.2
 * Tested up to: 6.1.1
 * WC requires at least: 3.3.6
 * WC tested up to: 6.7.0
 */

namespace cart_gateway;

 // Exit if accessed directly
if (!defined('ABSPATH')) exit; 

/**
 * initialize main class to load the plugin
 */
$mwcp_plugin       = wpg_cart_gateway::get_instance();
add_action( 'plugins_loaded',[$mwcp_plugin,'wpg_cart_gateway_initialize']);
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),[$mwcp_plugin,'wpg_cart_gateway_settings_link']);

class wpg_cart_gateway {

    protected static $instance;

    protected function __construct() {
        // Made protected to prevent calls.
    }
     
    static function get_instance() {
        if ( ! self::$instance ) {
            self::$instance = new wpg_cart_gateway();
        }

        return self::$instance;
    }
    /**
     * Initiate plugin core files & methods
     */
    static function wpg_cart_gateway_initialize() {
   
        // Check if WooCommerce enabled
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			add_action( 'admin_notices', [self::$instance, 'wpg_cart_gateway_admin_notice' ] );
			return;
        }
        
        add_filter( 'woocommerce_payment_gateways', [self::$instance, 'wpg_cart_gateway_class' ] );
    
        if (class_exists('WC_Payment_Gateway')) {
            require_once(plugin_dir_path(__FILE__) . "includes/cart_gateway_payment.php"); 
            require_once(plugin_dir_path(__FILE__) . "includes/cart_gateway.php");
        } 
        
    }
    /**
     * define unique method name here
     */
    static function wpg_cart_gateway_class($methods) {
        $methods[] = 'WC_cart_Gateway';
        return $methods;
    }

    /**
     * WooCommerce activate or install notice
     */
    static function wpg_cart_gateway_admin_notice(){
        echo sprintf(
			'<div class="error"><p>%s</p></div>',
			sprintf(
				esc_html( 'The CartGateway plugin is depended on the WooCommerce Plugin. So please activate or install %s to work!'),
				'<a href="'.esc_url('http://wordpress.org/extend/plugins/woocommerce/').'">'.esc_html('WooCommerce').'</a>'
			));
    }

    /**
     * Redirect to the CartGateway settings page to configure the plugin.
     * @param array $links
     * @return array
     */
    static function wpg_cart_gateway_settings_link($links) {
        $settings = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wpg_cart_gateway'),
				esc_html( 'Settings')
			),
		];
		return array_merge( $settings, $links );
    }
}
 