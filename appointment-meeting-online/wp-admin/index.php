<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



require_once dirname( __FILE__ ) . '/configuration.php';
require_once dirname( __FILE__ ) . '/meeting/index.php';
require_once dirname( __FILE__ ) . '/shepherd/index.php';



// Hook into the admin menu
add_action('admin_menu', 'appointment_meeting_online_plugin_menu');

// Function to create the menu
function appointment_meeting_online_plugin_menu() {
    add_menu_page(
        'appointment meeting online', // Page title
        'appointment meeting online', // Menu title
        'manage_options',  // Capability
        'appointment-meeting-online', // Menu slug
        'appointment_meeting_online' // Function to display the settings page
    );

  
    // Add a sub-menu under the main menu
    add_submenu_page(
        'appointment-meeting-online', // Parent slug
        'Configuration', // Page title
        'Configuration', // Menu title
        'manage_options', // Capability
        'plugin-configuration', // Menu slug
        'plugin_configuration_page' // Function to display the sub-menu page
    );

}

// Function to display the settings page
function appointment_meeting_online() {
    // Add your configuration options HTML/forms here
    echo '<div class="wrap">';
    echo '<h1>appointment meeting online</h1>';
    // Your settings form goes here
    echo '</div>';
}


?>