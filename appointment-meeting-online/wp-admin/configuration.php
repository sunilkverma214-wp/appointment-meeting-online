<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
// Function to display the sub-menu page
function plugin_configuration_page()
{
    // Add content for the sub-menu page
    echo '<div class="wrap">';
    echo '<h1>Plugin Configurations</h1>';
    //$time_zones_list = timezone_identifiers_list();
    $time_zones_list = array(
        'America/New_York' => 'Eastern Time (EST)',
        'America/Chicago' => 'Central Time (CST)',
        'America/Denver' => 'Mountain Time (MST)',
        'America/Los_Angeles' => 'Pacific Time (PST)',
        'America/Anchorage' => 'Alaska Time (AKST)',
        'Pacific/Honolulu' => 'Hawaii-Aleutian Time (HAST)'
    );
    $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    $success_message = '';
    $error_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Process form data and insert into the database
            $duration_of_meeting = sanitize_text_field($_POST['duration_of_meeting']);
            $time_zones = isset($_POST['time_zones']) ? $_POST['time_zones'] : '';
            $common_description = sanitize_textarea_field($_POST['common_description']);             
            $zoom_api_account_id = sanitize_text_field($_POST['zoom_api_account_id']);
            $zoom_api_client_id = sanitize_text_field($_POST['zoom_api_client_id']);
            $zoom_api_client_secret_key = sanitize_text_field($_POST['zoom_api_client_secret_key']);
            $email_notification_to_admin = isset($_POST['email_notification_to_admin']) ? 1 : 0;
            update_option('duration_of_meeting', $duration_of_meeting);
            update_option('time_zones', $time_zones);
            update_option('common_description', $common_description);
            update_option('zoom_api_account_id', $zoom_api_account_id);
            update_option('zoom_api_client_id', $zoom_api_client_id);
            update_option('zoom_api_client_secret_key', $zoom_api_client_secret_key);
            update_option('email_notification_to_admin', $email_notification_to_admin);
            // Insert data into the database (modify this based on your database structure)
            global $wpdb;
            $table_name = $wpdb->prefix . 'meetingsettings';
            $success_message = 'Data successfully updated.';
        } catch (Exception $e) {
            $error_message = 'Error: ' . $e->getMessage();
        }
    }
    $duration_of_meeting = get_option('duration_of_meeting');
    $selected_time_zones = get_option('time_zones');
    $selected_time_zones = $selected_time_zones != null ? $selected_time_zones : null;
    $common_description = get_option('common_description');
    $zoom_api_account_id = get_option('zoom_api_account_id');
    $zoom_api_client_id = get_option('zoom_api_client_id');
    $zoom_api_client_secret_key = get_option('zoom_api_client_secret_key');
    $email_notification_to_admin = get_option('email_notification_to_admin');
    ?>
    <style>
        .horizontal-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .horizontal-checkbox {
            white-space: nowrap;
            margin-right: 10px;
        }
        .weekday-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .weekday-checkbox {
            white-space: nowrap;
            margin-right: 10px;
        }
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        table tr td:first-child {
            width: 40%;
        }
        table tr td:last-child {
            width: 60%;
        }
    </style>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
            <div class="container">        
                <form method="post" action="" class="form-configuration">
                    <!-- Other form fields... -->
                    <table>
                        <tr>
                            <td>
                                <label for="duration_of_meeting">Duration of Meeting (minutes):</label>
                            </td>
                            <td>
                            <select name="duration_of_meeting" id="duration_of_meeting" class="form-control">
                            <?php
                            $time = 15;
                            for ($i = 0; $i < 8; $i++) {
                                $selected = $duration_of_meeting != null && $duration_of_meeting == $time ? 'selected' : '';
                                echo '<option value="' . $time . '" ' . $selected . '>' . $time . '</option>';
                                $time = $time + 15;
                            } ?>
                            </select>
                            </td>
                        </tr>                       
                        <tr>
                            <td>
                                <label for="time_zones">Time Zones:</label>
                            </td>
                            <td>
                                <div class="horizontal-checkboxes">
                                    <?php
                                    // Display checkboxes for all time zones
                                    foreach ($time_zones_list as $timezone => $label) {
                                        $checked = $timezone == $selected_time_zones ? 'checked' :  '';
                                        ?>
                                        <label class="horizontal-checkbox">
                                            <input type="radio" name="time_zones" class="form-control"
                                                value="<?php echo esc_attr($timezone); ?>" id="<?php echo esc_attr($timezone); ?>" <?php echo $checked; ?>>
                                            <?php echo esc_html($label); ?>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="common_description">Common Description:</label>
                            </td>
                            <td>
                                <textarea name="common_description" id="common_description" cols="100"
                                class="form-control"><?php echo $common_description; ?></textarea>
                            </td>
                        </tr>                       
                        <tr>
                            <td>
                                <label for="zoom_api_account_id">Zoom API Account ID:</label>
                            </td>
                            <td>
                                <input type="text" name="zoom_api_account_id" id="zoom_api_account_id" class="form-control"
                                    value="<?php echo $zoom_api_account_id; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="zoom_api_client_id">Zoom API Client ID:</label>
                            </td>
                            <td>
                                <input type="text" name="zoom_api_client_id" id="zoom_api_client_id" class="form-control"
                                    value="<?php echo $zoom_api_client_id; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="zoom_api_client_secret_key">Zoom API Client Secret Key:</label>
                            </td>
                            <td>
                                <input type="text" name="zoom_api_client_secret_key" id="zoom_api_client_secret_key" class="form-control"
                                value="<?php echo $zoom_api_client_secret_key; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="email_notification_to_admin">Email Notification to Admin:</label>
                            </td>
                            <td>
                                <input type="checkbox" name="email_notification_to_admin" id="email_notification_to_admin"
                                class="form-control" <?php echo $email_notification_to_admin != null ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" class="btn btn-success" value="Submit">
                            </td>
                        </tr>
                    </table>
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert" style="color:green">
                            <?php echo esc_html($success_message); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo esc_html($error_message); ?>
                        </div>
                    <?php endif; ?>                    
                </form>
            </div>
        </main><!-- #main -->
    </div><!-- #primary -->
    <?php
    echo '</div>';
}
?>