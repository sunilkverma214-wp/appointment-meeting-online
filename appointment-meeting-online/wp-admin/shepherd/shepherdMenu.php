<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// ----------------------------- custom post type --------------------------------
// Hook into the init action and create the custom post type
add_action('init', 'create_shepherd_post_type');
function create_shepherd_post_type() {
        // Run only on the MAIN SITE of multisite
    if ( ! is_main_site() ) {
        return; // stop here, do not register CPT on subsites
    }
    // Define labels for the 'event' post type
    $labels = array(
        'name'               => 'Shepherds',
        'singular_name'      => 'Shepherd',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'New Event',
        'all_items'          => 'All Shepherds',
        'view_item'          => 'View Event',
        'search_items'       => 'Search Shepherds',
        'not_found'          => 'No shepherds found',
        'not_found_in_trash' => 'No shepherds found in Trash',
        'menu_name'          => 'Shepherds'
    );
    // Define arguments for the 'event' post type
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'appointment-meeting-online', // Place it under your plugin's main menu
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'             => array('slug' => 'shepherds') // Set the slug for the URL
    );
    // Register the 'event' post type
    register_post_type('shepherd', $args);
}
?>