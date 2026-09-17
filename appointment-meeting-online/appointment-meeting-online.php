<?php
/*
Plugin Name: appointment meeting online
Description: User can book appointment or meeting using this plugin.
Version: 1.0
Author: Protos Soft
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __FILE__ ) . '/wp-admin/index.php';
require_once dirname( __FILE__ ) . '/booking/booking.php';
// Hook to enqueue the style.css file
function my_appoinment_plugin_enqueue_styles() {
    // Plugin directory URL
    $plugin_url = plugin_dir_url( __FILE__ );
    // Enqueue the style.css file
    wp_enqueue_style( 'my-appoinment-plugin-style', $plugin_url . 'css/style.css' );
}
// Hook into the 'wp_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', 'my_appoinment_plugin_enqueue_styles' );

function enqueue_shepherd_custom_post_validation_script() {
    global $typenow;
    $plugin_url = plugin_dir_url(__FILE__);
    // Check if we are editing a specific post type (replace 'your_post_type' with your actual post type)
    if ($typenow == 'shepherd') {
        // Enqueue your custom script
        wp_enqueue_script('shepherd-custom-validation-script',$plugin_url. 'js/shepherd-custom-validation.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_shepherd_custom_post_validation_script');