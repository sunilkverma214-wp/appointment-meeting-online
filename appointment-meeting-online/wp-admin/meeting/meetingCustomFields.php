<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// ---------------------------- custom fields ----------------------------
//Hook into the admin_init action to add meta boxes
add_action('admin_init', 'add_event_custom_fields');
function add_event_custom_fields() {
    add_meta_box(
        'meeting_details', // Unique ID
        'Meeting Details', // Box title
        'meeting_details_callback', // Content callback function
        'meeting', // Post type to display the meta box
        'normal', // Context: 'normal', 'advanced', or 'side'
        'default' // Priority: 'high' or 'low'
    );
}
// Callback function to display the event date field
function meeting_details_callback($post) {
    $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    // Retrieve the existing value of the event date field
    $shepherd_user_id = get_post_meta($post->ID, 'shepherd_user_id', true);
    $requested_by_user_id = get_post_meta($post->ID, 'requester_user_id', true);
    $creater_user_id = get_post_meta($post->ID, 'creater_user_id', true);
    $start_date_time = get_post_meta($post->ID, 'start_date_time', true);
    $start_date_time = $start_date_time != null? $start_date_time : '' ; 
    $end_date_time = get_post_meta($post->ID, 'end_date_time', true);
    $end_date_time = $end_date_time != null? $end_date_time : '' ; 
    $is_zoom_registered = get_post_meta($post->ID, 'is_zoom_registered', true);
    $zoom_meeting_url = get_post_meta($post->ID, 'zoom_meeting_url', true);
    $meeting_reason = get_post_meta($post->ID, 'meeting_reason', true);    
    $shepherd_display_name = get_the_title($shepherd_user_id);
    // Display the field HTML
  
    echo '<label for="shepherd_user_id">Shepherd Name:</label> &emsp;';
    echo $shepherd_display_name;
    
    $requested_by_first_name = get_user_meta($requested_by_user_id, 'first_name', true);
    $requested_by_last_name = get_user_meta($requested_by_user_id, 'last_name', true);
    $requested_by_display_name = "";
    if($requested_by_first_name != "" && $requested_by_last_name != ""){
        $requested_by_display_name = $requested_by_first_name.' '.$requested_by_last_name ;
    }else{
        $requested_by_user = get_userdata($requested_by_user_id);
        $requested_by_display_name = $requested_by_user->display_name;
    }
    echo "<br><br>";
    echo '<label for="requester_user_id">Requested By:</label> &emsp;';
    echo $requested_by_display_name;
    echo "<br><br>";
    echo '<label for="meeting_reason">Reason of The Meeting:</label> &emsp;';
    echo $meeting_reason;
    
    
    echo "<br><br>";
    echo '<label for="start_date_time">Start Date/Time:</label> &emsp;';
    echo '<input type="datetime-local" name="start_date_time" id="start_date_time" class="form-control" value="'.$start_date_time.'">';
    echo "<br><br>";
    echo '<label for="end_date_time">End Date/Time:</label> &emsp;';
    echo '<input type="datetime-local" name="end_date_time" id="end_date_time" class="form-control" value="'.$end_date_time.'">';
    
    echo "<br><br>";
    echo '<label for="is_zoom_registered">Is Meeting Scheduled :</label> &emsp;';
    
    $is_checked = ($is_zoom_registered == 1) ? "checked" : "";
    echo '<input type="radio" name="is_zoom_registered" value="1" '.$is_checked.' > Yes &emsp;&emsp;';
    $is_checked = ($is_zoom_registered == 0) ? "checked" : "";
    echo '<input type="radio" name="is_zoom_registered" value="0" '.$is_checked.'> No';
    echo ($is_zoom_registered == 0 && $requested_by_user_id != 0 )? "<br><span style='font-size: 11px;font-style: italic;'>Note: Due to some technical reason meeting is not scheduled yet. The system is working on it. The system will let you know once meeting is scheduled.</span>" : "" ;
    
    echo "<br><br>";
    echo '<label for="zoom_meeting_url">Meeting Url:</label> &emsp;';
    echo '<input type="url" id="zoom_meeting_url" name="zoom_meeting_url" value="' . esc_attr($zoom_meeting_url) . '">';
}
// Hook to save the custom field data
add_action('save_post', 'save_meeting_custom_fields');
function save_meeting_custom_fields($post_id) {
    if (isset($_POST['shepherd_user_id'])) {
        update_post_meta($post_id, 'shepherd_user_id', sanitize_text_field($_POST['shepherd_user_id']));
    }
    if (isset($_POST['requester_user_id'])) {
        update_post_meta($post_id, 'requester_user_id', sanitize_text_field($_POST['requester_user_id']));
    }
    if (isset($_POST['creater_user_id'])) {
        update_post_meta($post_id, 'creater_user_id', sanitize_text_field($_POST['creater_user_id']));
    }
    
    if (isset($_POST['start_date_time'])) {
        update_post_meta($post_id, 'start_date_time', sanitize_text_field($_POST['start_date_time']));
    }
    if (isset($_POST['end_date_time'])) {
        update_post_meta($post_id, 'end_date_time', sanitize_text_field($_POST['end_date_time']));
    }
    if (isset($_POST['is_zoom_registered'])) {
        update_post_meta($post_id, 'is_zoom_registered', intval(sanitize_text_field($_POST['is_zoom_registered'])));
    }
    if (isset($_POST['zoom_meeting_url'])) {
        update_post_meta($post_id, 'zoom_meeting_url', sanitize_text_field($_POST['zoom_meeting_url']));
    }
    if (isset($_POST['meeting_reason'])) {
        update_post_meta($post_id, 'meeting_reason', sanitize_text_field($_POST['meeting_reason']));
    }    
    // should   send email to shepherd, admin and user about the change ?
}
?>