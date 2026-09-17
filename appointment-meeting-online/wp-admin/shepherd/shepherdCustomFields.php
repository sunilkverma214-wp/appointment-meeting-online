<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
// ---------------------------- custom fields ----------------------------
// Hook into the admin_init action to add meta boxes
add_action('admin_init', 'add_shepherd_custom_fields');
function add_shepherd_custom_fields()
{
    add_meta_box(
        'shepherd_date', // Unique ID
        'Shepherd Date', // Box title
        'shepherd_date_callback', // Content callback function
        'shepherd', // Post type to display the meta box
        'normal', // Context: 'normal', 'advanced', or 'side'
        'default' // Priority: 'high' or 'low'
    );
}
// Callback function to display the event date field
function shepherd_date_callback($post)
{
    $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $first_name = get_post_meta($post->ID, 'first_name', true);
    $last_name = get_post_meta($post->ID, 'last_name', true);
    // Retrieve the existing value of the event date field
    $event_date = get_post_meta($post->ID, 'sub_title', true);
    $contact_email = get_post_meta($post->ID, 'contact_email', true);
    $default_working_hours_start = get_post_meta($post->ID, 'default_working_hours_start', true);
    $default_working_hours_start = $default_working_hours_start != null ? $default_working_hours_start : '';
    $default_working_hours_end = get_post_meta($post->ID, 'default_working_hours_end', true);
    $default_working_hours_end = $default_working_hours_end != null ? $default_working_hours_end : '';
    $default_working_days = get_post_meta($post->ID, 'default_working_days', true);
    $default_working_days = $default_working_days != null ? explode(";", $default_working_days) : null;
    $admin_shephred_timezone = get_post_meta($post->ID, 'admin_shephred_timezone', true);
    $admin_shephred_timezone = $admin_shephred_timezone != null ? $admin_shephred_timezone : '';
    // Display the field HTML

    echo '<label for="sub_title_field">First Name:</label> &emsp;';
    echo '<input type="text" id="first_name_field" name="first_name_field" value="' . esc_attr($first_name) . '">';

    echo "<br><br>";

    echo '<label for="sub_title_field">Last Name:</label> &emsp;';
    echo '<input type="text" id="last_name_field" name="last_name_field" value="' . esc_attr($last_name) . '">';

    echo "<br><br>";
    echo '<label for="sub_title_field">Sub Title:</label> &emsp;';
    echo '<input type="text" id="sub_title_field" name="sub_title_field" value="' . ($event_date == "" ? "Web conferencing details provided upon confirmation" : esc_attr($event_date)) . '">';
    echo "<i>This field will have a default value if left empty</l>";
    echo "<br><br>";

    echo '<label for="contact_email_field">Contact Email:</label> &emsp;';
    echo '<input type="text" id="contact_email_field" name="contact_email_field" value="' . esc_attr($contact_email) . '" ' . ($contact_email === '' ? '' : 'disabled') . ' >';
    echo "<br><br>";

    // Password field — only shown when creating a new shepherd (no existing WP user linked yet)
    $shepherd_wp_user_id = get_post_meta($post->ID, 'shepherd_wp_user_id', true);
    if (!$shepherd_wp_user_id) {
        echo '<label for="shepherd_password_field">Password:</label> &emsp;';
        echo '<span style="display:inline-flex; align-items:center; gap:8px;">';
        echo '<input type="password" id="shepherd_password_field" name="shepherd_password_field" value="" autocomplete="new-password" placeholder="Leave blank to auto-generate">';
        echo '<button type="button" onclick="(function(btn){var inp=document.getElementById(\'shepherd_password_field\');inp.type=inp.type===\'password\'?\'text\':\'password\';btn.textContent=inp.type===\'password\'?\'Show\':\'Hide\';})(this)" style="cursor:pointer;">Show</button>';
        echo '</span>';
        echo '<br><small><em>Leave blank to auto-generate a secure password.</em></small>';
        echo "<br><br>";
    }

    echo '<label for="default_working_hours_start">Default Working Hours Start:</label> &emsp;';
    echo '<input type="time" onchange="this.value = formatTimeTo24Hour(this.value)" name="default_working_hours_start" id="default_working_hours_start" class="form-control" value="' . $default_working_hours_start . '">';
    echo "<br><br>";

    echo '<label for="default_working_hours_end">Default Working Hours End:</label> &emsp;';
    echo '<input type="time" onchange="this.value = formatTimeTo24Hour(this.value)" name="default_working_hours_end" id="default_working_hours_end" class="form-control" value="' . $default_working_hours_end . '">';
    echo "<br><br>";
    echo '<label for="default_working_hours_end">Default Working Days:</label> &emsp;';
    foreach ($weekdays as $day) {
        // /default_working_days
        $checked = $default_working_days != null ? in_array($day, $default_working_days) ? 'checked' : '' : '';

        echo '<label class="weekday-checkbox">
            <input type="checkbox" name="default_working_days[]" class="form-control" value="' . esc_attr($day) . '" id="' . esc_attr($day) . '" ' . $checked . '>
            ' . esc_html($day) . '
        </label> &emsp;';
    }
    //add timezone for shephred
    echo "<br><br>";
    echo '<label for="Timezones">Default Timezone:</label> &emsp;';
    $usTimeZones = [
        'America/New_York' => 'Eastern Standard Time',
        'America/Chicago' => 'Central Standard Time',
        'America/Phoenix' => 'Mountain Standard Time',
        'America/Los_Angeles' => 'Pacific Standard Time',
        'America/Anchorage' => 'Alaska Standard Time',
        'Pacific/Honolulu' => 'Hawaii-Aleutian Standard Time',
    ];
    echo '<select id="admin_shephred_timezone" name="admin_shephred_timezone">';
    // Display current time in each USA time zone
    foreach ($usTimeZones as $timezone => $timezoneName) {
        echo '<option value="' . $timezone . '"' . ($admin_shephred_timezone == $timezone ? ' selected="selected"' : '') . '>' . $timezone . '</option>';
    }
    echo '</select>';

}
// Hook to save the custom field data
add_action('save_post', 'save_shepherd_custom_fields', 10, 3);
function save_shepherd_custom_fields($post_id, $post, $update)
{
    if ($post->post_type === 'shepherd') {
        // Check if the current user has permission to edit the post
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        // Save the event date
        if (isset($_POST['first_name_field'])) {
            update_post_meta($post_id, 'first_name', sanitize_text_field($_POST['first_name_field']));
        }
        if (isset($_POST['last_name_field'])) {
            update_post_meta($post_id, 'last_name', sanitize_text_field($_POST['last_name_field']));
        }
        if (isset($_POST['sub_title_field'])) {
            update_post_meta($post_id, 'sub_title', sanitize_text_field($_POST['sub_title_field']));
        }
        if (isset($_POST['contact_email_field'])) {
            update_post_meta($post_id, 'contact_email', sanitize_text_field($_POST['contact_email_field']));
        }
        // Save plain password temporarily to use during user creation (cleared after use)
        if (isset($_POST['shepherd_password_field']) && !empty($_POST['shepherd_password_field'])) {
            update_post_meta($post_id, '_shepherd_plain_password_temp', $_POST['shepherd_password_field']);
        }
        if (isset($_POST['default_working_hours_start'])) {
            update_post_meta($post_id, 'default_working_hours_start', sanitize_text_field($_POST['default_working_hours_start']));
        }
        if (isset($_POST['default_working_hours_end'])) {
            update_post_meta($post_id, 'default_working_hours_end', sanitize_text_field($_POST['default_working_hours_end']));
        }
        if (isset($_POST['default_working_days'])) {
            $default_working_days = isset($_POST['default_working_days']) ? implode(';', $_POST['default_working_days']) : '';
            update_post_meta($post_id, 'default_working_days', sanitize_text_field($default_working_days));
        }
        if (isset($_POST['admin_shephred_timezone'])) {
            update_post_meta($post_id, 'admin_shephred_timezone', sanitize_text_field($_POST['admin_shephred_timezone']));
        }


        //call a function for create and update wp user after shephred created
        create_and_update_new_user_as_shepherd_user_role($post_id, $post, $update);
    }
}






 function create_and_update_new_user_as_shepherd_user_role($post_id, $post, $update)
{

    $first_name  = get_post_meta($post_id, 'first_name', true) ?: '';
    $last_name   = get_post_meta($post_id, 'last_name', true) ?: '';
    $sub_title   = get_post_meta($post_id, 'sub_title', true) ?: '';
    $contact_email = get_post_meta($post_id, 'contact_email', true) ?: '';

    $default_working_hours_start = get_post_meta($post_id, 'default_working_hours_start', true) ?: '';
    $default_working_hours_end   = get_post_meta($post_id, 'default_working_hours_end', true) ?: '';
    $default_working_days        = get_post_meta($post_id, 'default_working_days', true);

    $admin_shephred_timezone = get_post_meta($post_id, 'admin_shephred_timezone', true) ?: '';

    $shepherd_wp_user_id = get_post_meta($post_id, 'shepherd_wp_user_id', true);

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */
    if (!$shepherd_wp_user_id) {

        $post_title = trim($first_name . ' ' . $last_name);

        // Generate username
        $username = sanitize_user(strtolower(str_replace(' ', '_', $post_title)));

        // Check existing email
        $existing_user = get_user_by('email', $contact_email);

        if (!$existing_user) {

            // Use admin-provided password if set, otherwise auto-generate
            $plain_password = get_post_meta($post_id, '_shepherd_plain_password_temp', true);
            $password = !empty($plain_password) ? $plain_password : wp_generate_password();

            // Clear the temporary plain password immediately after reading
            delete_post_meta($post_id, '_shepherd_plain_password_temp');

            $user_id = wp_create_user($username, $password, $contact_email);

            if (!is_wp_error($user_id)) {

                // Assign role on main site
                $user = new WP_User($user_id);
                $user->set_role('shepherd');

                /*
                |--------------------------------------------------------------------------
                | ADD USER TO ALL SUBSITES
                |--------------------------------------------------------------------------
                */
                if (is_multisite()) {

                    $sites = get_sites();

                    foreach ($sites as $site) {

                        $blog_id = (int) $site->blog_id;

                        // Add user to subsite with shepherd role
                        add_user_to_blog($blog_id, $user_id, 'shepherd');
                    }
                }

                // Save user id in post meta
                update_post_meta($post_id, 'shepherd_wp_user_id', $user_id);

                // User meta
                update_user_meta($user_id, 'first_name', $first_name);
                update_user_meta($user_id, 'last_name', $last_name);
                update_user_meta($user_id, 'sub_title', $sub_title);

                update_user_meta($user_id, 'default_working_hours_start', $default_working_hours_start);
                update_user_meta($user_id, 'default_working_hours_end', $default_working_hours_end);
                update_user_meta($user_id, 'default_working_days', $default_working_days);

                update_user_meta($user_id, 'admin_shephred_timezone', $admin_shephred_timezone);
            }

        } else {

            // Email already exists
            error_log('Shepherd user already exists with email: ' . $contact_email);
        }

    } else {

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER META
        |--------------------------------------------------------------------------
        */

        update_user_meta($shepherd_wp_user_id, 'first_name', $first_name);
        update_user_meta($shepherd_wp_user_id, 'last_name', $last_name);
        update_user_meta($shepherd_wp_user_id, 'sub_title', $sub_title);

        update_user_meta($shepherd_wp_user_id, 'default_working_hours_start', $default_working_hours_start);
        update_user_meta($shepherd_wp_user_id, 'default_working_hours_end', $default_working_hours_end);
        update_user_meta($shepherd_wp_user_id, 'default_working_days', $default_working_days);

        update_user_meta($shepherd_wp_user_id, 'admin_shephred_timezone', $admin_shephred_timezone);
    }
}


// 🔁 Triggered when a Shepherd post is moved to trash
add_action('wp_trash_post', 'schedule_shepherd_delete_cron');
// Handles when a shepherd post is permanently deleted
add_action('before_delete_post', 'schedule_shepherd_delete_cron');
function schedule_shepherd_delete_cron($post_id)
{
    // Proceed only if it's the 'shepherd' post type
    if (get_post_type($post_id) !== 'shepherd')
        return;

    // Get the mapped user ID from custom field
    $user_id = get_post_meta($post_id, 'shepherd_wp_user_id', true);

    // if ($user_id && is_numeric($user_id)) {
    //     // Schedule a cron job to delete both user and post after 60 seconds
    //     wp_schedule_single_event(time() + 60, 'cron_delete_shepherd_pair', array($post_id, (int)$user_id));
    // }
    if ($user_id && is_numeric($user_id)) {
        global $wpdb;

        // Delete associated user and user meta using SQL (faster)
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->base_prefix}users WHERE ID = %d",
                $user_id
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->base_prefix}usermeta WHERE user_id = %d",
                $user_id
            )
        );
    }

    // Permanently delete the shepherd post (force delete)
    //wp_delete_post($post_id, true);
}


// Triggered when a user is deleted manually from WordPress admin
add_action('delete_user', 'schedule_delete_shepherd_post_on_user_delete');

function schedule_delete_shepherd_post_on_user_delete($user_id)
{
    /*
    |--------------------------------------------------------------------------
    | REMOVE USER FROM ALL SUBSITES
    |--------------------------------------------------------------------------
    */

    if (is_multisite()) {

        $sites = get_sites();

        foreach ($sites as $site) {

            $blog_id = (int) $site->blog_id;

            // Remove user from subsite
            remove_user_from_blog($user_id, $blog_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FIND SHEPHERD POST
    |--------------------------------------------------------------------------
    */

    $args = array(
        'post_type'      => 'shepherd',
        'meta_key'       => 'shepherd_wp_user_id',
        'meta_value'     => $user_id,
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
    );

    $post_ids = get_posts($args);

    /*
    |--------------------------------------------------------------------------
    | DELETE SHEPHERD POST
    |--------------------------------------------------------------------------
    */

    if (!empty($post_ids)) {

        // Proper WP delete
        wp_delete_post($post_ids[0], true);
    }
}









// show the message to user that if they delete the shepherd/ user or tried to move to trash user will get deleted permana
//  Always show warning message on Shepherd post list page
add_action('admin_notices', 'show_shepherd_warning_notice');

function show_shepherd_warning_notice()
{
    $screen = get_current_screen();

    // Show only on Shepherd post list screen
    if ($screen && $screen->post_type === 'shepherd' && $screen->base === 'edit') {
        ?>
        <div class="notice notice-warning">
            <p>
                <strong>Heads up:</strong> 
                Trashing a Shepherd post will immediately and permanently delete it 
                <em>along with its associated user account</em>. 
                This action cannot be undone. If you find it in the trash later, please do not restore it — make sure to delete it permanently.
            </p>
        </div>
        <?php
    }

    // Check if we're on the Users list page (users.php)
    if ($screen->base === 'users' && $screen->id === 'users') {

        // Display the notice about syncing Shepherd post type and Shepherd user role
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>Info:</strong> The <strong>Shepherd</strong> post type and <strong>Shepherd user role</strong> are
                synchronized. Changes to one will reflect in the other.</p>
        </div>
        <?php
    }
}
?>