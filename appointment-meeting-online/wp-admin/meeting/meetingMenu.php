<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// ----------------------------- custom post type --------------------------------
// Hook into the init action and create the custom post type
add_action('init', 'create_meeting_post_type');
function create_meeting_post_type() {

    // Run only on the MAIN SITE of multisite
    if ( ! is_main_site() ) {
        return; // stop here, do not register CPT on subsites
    }

    
    // Define labels for the 'event' post type
    $labels = array(
        'name'               => 'Meetings',
        'singular_name'      => 'Meeting',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Meeting',
        'edit_item'          => 'Edit Meeting',
        'new_item'           => 'New Meeting',
        'all_items'          => 'All Meetings',
        'view_item'          => 'View Meeting',
        'search_items'       => 'Search Meetings',
        'not_found'          => 'No meetings found',
        'not_found_in_trash' => 'No meetings found in Trash',
        'menu_name'          => 'Meetings'
    );
    // Define arguments for the 'Meeting' post type
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'appointment-meeting-online', // Place it under your plugin's main menu
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'rewrite'             => array('slug' => 'meetings') // Set the slug for the URL
    );
    // Register the 'event' post type
    register_post_type('Meeting', $args);
}
?>