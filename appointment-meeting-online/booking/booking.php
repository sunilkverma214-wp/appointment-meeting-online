<?php
/*################################################################################################*/
// create shortcode for displaying calender booking 
/*################################################################################################*/
// Include constants file
include plugin_dir_path(__FILE__) . 'constants/constants.php';

function display_booking_calendar()
{
	ob_start();


	if (is_user_logged_in()) {

		// Example usage:
		$allowed_roles = array('seller', 'buyer'); // Define roles allowed to access the page

		if (!check_loggedin_user_role_fun($allowed_roles)) {
			// Redirect or display an error message
			echo '<span style="color:#61B94E;">Sorry, you do not have access to this page.</span>';
			exit;
		}

		// Check if the user have alerdy booked meeting or not    
		$current_user = wp_get_current_user();
		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		$user_posts = get_posts(
			array(
				'post_type' => 'meeting',
				'author' => $current_user->ID,
				'posts_per_page' => 1 // Change the number of posts to retrieve if needed
			)
		);
		restore_current_blog(); // <-- ADDED: Restore the context
		$postcont = 1;
		if (empty($user_posts)) {
			$postcont = 0;
		}

		//hide wecome section on clicked cross buttons
		$hide_welcome_section = get_user_meta($current_user->ID, 'hide_welcome_section', true);
		// Check if the meta value exists and is equal to 1
		if ($hide_welcome_section !== false && $hide_welcome_section == 1) {
			// Meta value exists and is equal to 1
			$hide_welcome_section_var = 1;
		} elseif ($hide_welcome_section !== false) {
			// Meta value exists but is not equal to 1
			$hide_welcome_section_var = 0;
		} else {
			// Meta value does not exist
			$hide_welcome_section_var = 0;
		} ?>

		<script>
			jQuery(document).ready(function () {
				// On page load, add data attribute to all li items
				let cout = "<?php echo $postcont; ?>";
				jQuery('ul#time-slots').attr('data-userpostscount', cout); // Change 'value' as per your requirement

			});
		</script>

		<link rel="stylesheet"
			href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
		<script src="https://kit.fontawesome.com/07b3d56790.js" crossorigin="anonymous"></script>

		<section class="main-section schedulemeet">
			<?php if ($hide_welcome_section_var != 1) { //check user have alery meeting scheduled or not  
							$user_roles = $current_user->roles;
							$current_role = reset($user_roles); // Get the first role (usually the primary role)
							$welcome_note = "";
							if ($current_role == 'buyer') {
								$welcome_note = "You've marked a listing as 'Interested.' Please schedule a call with the Deal Shepherd to explore business details and get guidance through the buying process.";
							} else {
								$welcome_note = "We will guide you through the registration process. Please select a Date and a Time Slot from the
						following, according to your availability.
						<div>
							We will connect with you over a google meeting and
							help you set up your profile.
						</div>";
							}
							?>
				<section class="container cutom-container-width">
					<div class="row">
						<div class="col-sm-12">
							<div class="top-heading-sec">
								<span class="hideshowcross">
									<a class="crosBtns" id="click_btn_action_welcome_sec"><img
											src="<?php echo plugin_dir_url(__FILE__); ?>images/xbuttonBlack.svg"></a></span>
								<div id="sub-heading">Welcome to B4L!</div>
								<div>
									<?php echo $welcome_note; ?>
								</div>
							</div>
						</div>
				</section>
			<?php } ?>
			<div class="container maxwidth">
				<div class="row min-border-height">
					<div class="col-sm-4 mp-top">
						<div class="mp-top"></div>
						<div class="shephared-block">
							<?php
							$user_roles = $current_user->roles;
							$shephered_b_name = '';
							$current_role = reset($user_roles); // Get the first role (usually the primary role)
							if ($current_role == 'buyer') {
								//load all shephred list for seller
								$shephreposts = loadShepharedDataForBuyer();
							} else {
								//load all shephred list for seller
								$shephreposts = loadDefaultAllShepharedData();

								//print_r($shephreposts);
							}
							?>
							<div class="shephared-sec">
								<div class="shephared-image-sec">
									<div class="shephared-image" id="shephared-image-block"></div>
									<div class="custom-dropdown">
										<div id="selected-shephred-namd-div" class="selected-shephred-item"
											onclick="toggleDropdown()" datashephredid="<?php echo $shephreposts[0]->ID; ?>">
											<span id="sheparedNameSpan"><?php echo $shephreposts[0]->post_title;

											if (($current_role == 'buyer') && (!empty($shephreposts[0]->business_name))) {
												//echo $shephered_b_name = ' ('.$shephreposts[0]->business_name.')';  //remove assign shepherd list
											}

											?></span> <i class="fa-solid fa-angle-down"></i>
										</div>
										<ul class="shephrddropdown-content" style="display:none;">
											<?php
											foreach ($shephreposts as $post) {
												$thumbnail_urll = get_the_post_thumbnail_url($post->ID, 'full');
												if (empty($thumbnail_urll)) {
													$thumbnail_urll = plugin_dir_url(__FILE__) . 'images/defaultFeatureImage.png';
												}

												if ($current_role == 'buyer') {
													echo '<style>.shephrddropdown-content { width: auto; } .custom-dropdown{width:60%;}</style>';
													$shephered_b_name = ' (' . $post->business_name . ')';
												}

												?>
												<li
													onclick="selectShephredOption('<?php echo $post->post_title . '' . $shephered_b_name; ?>', '<?php echo $thumbnail_urll; ?>',<?php echo $post->ID; ?>)">
													<img src="<?php echo $thumbnail_urll; ?>" alt="Image 1">
													<?php echo esc_html($post->post_title);
													if (isset($shephered_b_name)) {
														//echo $shephered_b_name;  //remove assign shepherd list
													}
													?>
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<div id="shephreddetailsBlock"> </div>
							</div>
						</div>
					</div>
					<div class="col-sm-4 mp-top">
						<div class="mp-top"></div>
						<div class="calendar-heding">Select a Date and Time</div>
						<div class="main-div">
							<div class="calendar-container">
								<div class="calendar-header">
									<div class="calendar-current-date"></div>
									<div class="calendar-navigation">
										<span id="calendar-prev" class="material-symbols-rounded">
											chevron_left</span>
										<span id="calendar-next" class="material-symbols-rounded"> chevron_right </span>
									</div>
								</div>

								<div class="calendar-body">
									<ul class="calendar-weekdays">
										<li>Sun</li>
										<li>Mon</li>
										<li>Tue</li>
										<li>Wed</li>
										<li>Thu</li>
										<li>Fri</li>
										<li>Sat</li>
									</ul>
									<ul class="calendar-dates"></ul>
								</div>
							</div>
						</div>
						<div id="timeWithTimeZonesec">
							<label id="selecLabl">Select your timezone:</label>
							<div class="countryselect"><img class="globeIcon"
									src="<?php echo plugin_dir_url(__FILE__); ?>images/globe.svg"> <span id="timeWithTimeZone">

									<?php
									// List of USA time zones 
									$usTimeZones = [
										'America/New_York' => 'America/New York (EST)',
										'America/Chicago' => 'America/Chicago (CST)',
										'America/Phoenix' => 'America/Phoenix (MST)',
										'America/Los_Angeles' => 'America/Los Angeles (PST)',
										'America/Anchorage' => 'America/Anchorage (AKST)',
										'Pacific/Honolulu' => 'Pacific/Honolulu (HST)',
									];
									echo '<select id="user_timezone_option" name="user_timezone_option" onchange="user_timezone_option()" >';
									// Display current time in each USA time zone
									foreach ($usTimeZones as $timezone => $timezoneName) {
										//$dateTime = new DateTime('now', new DateTimeZone($timezone));
										echo '<option value="' . $timezone . '">' . $timezoneName . '</option>';
									}
									echo '</select></span> ';
									?>
							</div>

						</div>
					</div>
					<div class="col-sm-4">
						<div class="mp-top"></div>
						<div id="time-slot-container" style="display: none;">
							<div id="selected-date" class="top-titles"></div>

							<ul id="time-slots"></ul>
						</div>
					</div>
				</div>
			</div>
		</section>

	<?php } else {
		echo "Only logged user can access this page.";
	} ?>

	<?php
	return ob_get_clean();
}
add_shortcode('booking_calendar', 'display_booking_calendar'); //disply booking calender 

// added all modalopup at foote of the page
function add_modal_popup_html()
{
	?>
	<style>
		.botum-btns {
			cursor: pointer;
		}

		#meetingSuccessModal,
		#myCustomModal {
			display: none;
		}
	</style>
	<!--###############################################################################################*/
// show modal popup on click of timeslot
/*################################################################################################-->
	<!-- The Modal -->
	 <?php 
	 $commoconfig = include get_stylesheet_directory() . '/config/config.php';
	  ?> 
	<div class="modal ourCustomModalcss x" id="myCustomModal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<a class="crosBtn" data-bs-dismiss="modal"><img
							src="<?php echo plugin_dir_url(__FILE__); ?>images/xbutton.svg"></a>

				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<h3 class="modal-title">Meeting Details</h3>
					<div class="container" id="selected-meeting-time-render" style="display: block;">

					</div>
					<div class="botum-btns">
						<button type="button" class="cancel_meting" id="cansel" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="cancel_meting activeGreen" id="confirm_mycustom_meting"
							disabled>Confirm</button>
					</div>
					<input type="hidden" class="form-control" id="meeting_selected_date" value="">
					<input type="hidden" class="form-control" id="meeting_selected_time" value="">
					<input type="hidden" class="form-control" id="shaphredId" value="">
					<input type="hidden" class="form-control" id="currentISTTime" value="">
					<input type="hidden" class="form-control" id="currentDomainsName" value="<?php echo $commoconfig['B4L_CURRENT_DOMAIN'];; ?>">
					<input type="hidden" id="zoom_security"
						value="<?php echo wp_create_nonce('create_zoom_meeting_nonce'); ?>">
					<!-- <input type="hidden" class="form-control" id="userID" value=""> -->
				</div>

			</div>
		</div>
	</div>
	<!--###############################################################################################*/
// meeting success popop
/*################################################################################################-->

	<!-- Button to Open the Modal -->
	<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#meetingSuccessModal">
  Open modal
</button> -->
	<!-- The Modal -->
	<div class="modal ourCustomModalcss" id="meetingSuccessModal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<a class="crosBtn" id="cros-success-Btn" data-bs-dismiss="modal"><img
							src="<?php echo plugin_dir_url(__FILE__); ?>images/xbutton.svg"></a>

				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<div class="suceesmsg-meting">Thank You, <br /> your meeting is confirmed.  A reminder invite was sent to the email you provided.</div>
				</div>
				<div class="botum-btns">
					<button type="button" class="cancel_meting activeGreen" id="closeSuccesspopu"
						data-bs-dismiss="modal">Close</button>
				</div>

			</div>
		</div>
	</div>

	<?php
}
add_action('wp_footer', 'add_modal_popup_html');

// hide admin bar from current login user
function hide_admin_bar_except_admin_shepherd()
{
	// Check if the user is an administrator or has the 'shepherd' role
	$user = wp_get_current_user();
	$is_administrator = current_user_can('administrator');
	$is_shepherd = in_array('shepherd', (array) $user->roles);

	// Show the admin bar only for administrators and shepherds
	return $is_administrator || $is_shepherd;
}
add_filter('show_admin_bar', 'hide_admin_bar_except_admin_shepherd');

/*################################################################################################*/
// added js and css files
/*################################################################################################*/
function enqueue_custom_scriptss()
{
	$plugin_url = plugin_dir_url(__FILE__);
	if (is_page(array('schedule-meeting'))) { // show below files only schedule-meeting page
		// Get the plugin directory URL

		// Enqueue your custom script
		//wp_enqueue_script('custom-ajax-script', $plugin_url . 'js/custom-ajax-script.js?v=rand'.rand(), array('jquery'), null, true);

		wp_enqueue_script(
			'custom-ajax-script',
			plugins_url('js/custom-ajax-script.js', __FILE__) . '?v=' . filemtime(plugin_dir_path(__FILE__) . 'js/custom-ajax-script.js'),
			array('jquery'),
			null,
			true
		);

		// Pass the AJAX URL to the script
		wp_localize_script('custom-ajax-script', 'custom_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

	}
	// Enqueue the stylesheet

	wp_enqueue_style('your-plugin-styles1', $plugin_url . 'css/style.css', array(), '1.0', 'all');

}
add_action('wp_enqueue_scripts', 'enqueue_custom_scriptss');



/*################################################################################################*/
// Get Date and time in this format ('Y-m-d\TH:i') 
/*################################################################################################*/
function getDateTimeFormatLikeMeetingDatetime()
{
	// Get the current date and time in the desired format
	$dateTimeFormatted = date('Y-m-d\TH:i');
	return $dateTimeFormatted;
}

/*################################################################################################*/
// function get all Default Shephred List / set default shepherd for user 
/*################################################################################################*/
function loadDefaultAllShepharedData()
{
	global $wpdb;
	$current_user = wp_get_current_user();
	$userID = $current_user->ID;
	// Table name
	$table_name = $wpdb->base_prefix . 'users_shepherd';
	// Prepare and execute the SQL query
	$query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d LIMIT 1", $userID);
	$result = $wpdb->get_row($query, ARRAY_A);
		
	if ($result) {
		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		 error_log('Your message efffffffffffffffffffffffseeeeeeeeeeeeeeeeeeeeeeee');
		$shephreposts = get_posts(
			array(
				'post_type' => 'shepherd',
				'posts_per_page' => 1,
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => 'shepherd_wp_user_id',
						'value' => $result['shepherd_user_id'],
						'compare' => '=',
					),
				),
			)
		);
		 restore_current_blog(); // <-- ADDED: Restore the context
	} else {
		 error_log('Your message elseeeeeeeeeeeeeeeeeeeeeeee');
 switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		// Get all post titles
		$shephreposts = get_posts(
			array(
				'post_type' => 'shepherd', // Change 'post' to your custom post type if needed
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
			)
		);
	restore_current_blog(); // <-- ADDED: Restore the context
	}
		
		
	return $shephreposts;
}




/*################################################################################################*/
// function get current Shephred for buyer user 
/*################################################################################################*/
function loadShepharedDataForBuyer()
{
	global $wpdb;
	$b4l_business = $wpdb->base_prefix . 'b4l_business';
	$users_shepherd = $wpdb->base_prefix . 'users_shepherd';
	$BWn_b4l_user_interested_businesses = $wpdb->base_prefix . 'b4l_user_interested_businesses';
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$meta_query = array();

	if (isset($_GET['businessId'])) {
		// load page with specific business's shepherd
		$businessId = $_GET['businessId'];
		$results = $wpdb->get_row("SELECT *  FROM $b4l_business INNER JOIN $users_shepherd ON $b4l_business.users_shepherd_tbl_id = $users_shepherd.id WHERE $b4l_business.id = $businessId");
		$shepherdid = $results->shepherd_user_id;
		$meta_query = array(
			array(
				'key' => 'shepherd_wp_user_id',
				'value' => $shepherdid,
				'compare' => '=',
			),
		);

		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		$repost =  get_posts(
			array(
				'post_type' => 'shepherd',
				'posts_per_page' => 1,
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'meta_query' => $meta_query
			)
		);
		restore_current_blog(); // <-- ADDED: Restore the context
		return $repost;
	} else {
		// if business id is not in url parameter then load page with all interested business shepherd's
		$results = $wpdb->get_results($wpdb->prepare("SELECT distinct t3.shepherd_user_id,t2.business_name
									FROM $BWn_b4l_user_interested_businesses AS t1
									INNER JOIN $b4l_business AS t2 ON t1.b4l_businesses_id = t2.id
									INNER JOIN $users_shepherd AS t3 ON t2.users_shepherd_tbl_id = t3.id
									WHERE t1.user_id = %d
									", $user_id));

		$shepherd_ids = array_map(function ($item) {
			return $item->shepherd_user_id;
		}, $results);
		$meta_query = array(
			array(
				'key' => 'shepherd_wp_user_id',
				'value' => $shepherd_ids,
				'compare' => 'IN',
			),
		);

		// Initialize an array to store the result
		$businessData = array();

		// Group by shepherd_user_id and concatenate business names
		foreach ($results as $item) {
			if (!isset($businessData[$item->shepherd_user_id])) {
				$businessData[$item->shepherd_user_id] = (object) array(
					'shepherd_user_id' => $item->shepherd_user_id,
					'business_name' => $item->business_name
				);
			} else {
				// Concatenate business_name with a comma if the shepherd_user_id already exists
				$businessData[$item->shepherd_user_id]->business_name .= ', ' . $item->business_name;
			}
		}
		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		$posts = get_posts(
			array(
				'post_type' => 'shepherd',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'post_status' => 'publish',
				'meta_query' => $meta_query
			)
		);
		restore_current_blog(); // <-- ADDED: Restore the context



		// Adding a new element to each WP_Post object
		// Adding business_name to each WP_Post object based on shepherd_wp_user_id
		foreach ($posts as $post) {
			if (isset($businessData[$post->shepherd_wp_user_id])) {
				$post->business_name = $businessData[$post->shepherd_wp_user_id]->business_name;
			} else {
				$post->business_name = null; // or any default value
			}
		}

		return $posts;
		// Redirect users to the homepage
		//wp_redirect(home_url());
		//exit;
	}

}












/*################################################################################################*/
// function get Todays And Upcoming Shepherd Meetings By ShepherdId 
/*################################################################################################*/
function getTodaysAndUpcomingShepherdMeetingsByShepherdId($shepherd, $defaultUserTimeZone, $defaultShephredTimeZone)
{
	//get datetime of Today/selected from calender
	//$formattedDateTime = getDateTimeFormatLikeMeetingDatetime();

	// Create a DateTime object with the current time in the specified timezone
	$dateTimess = new DateTime('now', new DateTimeZone($defaultShephredTimeZone));
	// Format the DateTime object as per the desired format
	$formattedDateTime = $dateTimess->format('Y-m-d\TH:i');
	switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
	$args = array(
		'post_type' => 'meeting', // Replace 'your_post_type' with your actual post type
		'posts_per_page' => -1, // Retrieve all posts
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'shepherd_user_id', // Replace 'user' with your actual meta key
				'value' => $shepherd,
				'compare' => '=',
				'type' => 'NUMERIC', // Adjust the type if needed (numeric, string, etc.)
			),
			array(
				'key' => 'start_date_time', // Replace 'your_datetime_meta_key' with your actual datetime meta key
				'value' => $formattedDateTime,
				'compare' => '>',
				'type' => 'DATETIME',
			),
		),
	);

	$query = new WP_Query($args);

	$allBookedTimeOfAllShepherd = [];
	// Check if there are any posts
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$shepherd_user_id = get_post_meta($query->post->ID, 'shepherd_user_id', true);
			$m_starttime = get_post_meta($query->post->ID, 'start_date_time', true);
			$stsrtTimMting = explode('T', $m_starttime);

			// Create a DateTime object with the original time and timezone
			$shephredTimeZon = new DateTime($stsrtTimMting[1], new DateTimeZone($defaultShephredTimeZone));

			// Set the new timezone
			$newUserTimeZone = new DateTimeZone($defaultUserTimeZone);
			$shephredTimeZon->setTimezone($newUserTimeZone);

			// If the key exists, update the existing value
			$allBookedTimeOfAllShepherd[$shepherd_user_id][] = $stsrtTimMting[0] . 'T' . $shephredTimeZon->format('H:i');


		}
		wp_reset_postdata(); // Restore global post data

		
	} else {
		$allBookedTimeOfAllShepherd[$shepherd][] = 'No Bookings';
	}
	restore_current_blog(); // <-- ADDED: Restore the context
	$allBookedTimeOfAllShepherd['formattedDateTime'][] = $formattedDateTime;


	return $allBookedTimeOfAllShepherd;
}

/*################################################################################################*/
// function get convert Meeting start And End Time According To Timezone
/*################################################################################################*/

function convertMeetingstartAndEndTimeAccordingToTimezone($shepherdId, $user_Timezone, $shphred_timezones, $default_working_hours_start, $default_working_hours_end)
{

	// Assume 9:00 AM in New York 
	$shphredTimeZone = new DateTimeZone($shphred_timezones);
	$shphredDateTime = new DateTime($default_working_hours_start, $shphredTimeZone);

	// Convert the New York time to Chicago time
	$userTimeZone = new DateTimeZone($user_Timezone);
	$shphredDateTime->setTimezone($userTimeZone);

	// Display the result
	//echo "9:00 AM in Chicago is " . $chicagoDateTime->format('H:i:s') . " in New York.";
	$startTimeString = $shphredDateTime->format('H:i:s');

	// GEtn End Time Convert 
	$shphredTZone = new DateTimeZone($shphred_timezones);
	$shphredDTime = new DateTime($default_working_hours_end, $shphredTZone);

	// Convert the New York time to Chicago time
	$userTZone = new DateTimeZone($user_Timezone);
	$shphredDTime->setTimezone($userTZone);

	$endTimeString = $shphredDTime->format('H:i:s');

	return $startTimeString . '&' . $endTimeString;

}

/*################################################################################################*/
// Get Shephred information on dropdown change
/*################################################################################################*/
function getShephredInfoFunction()
{

	$shepherdId = isset($_POST['shepherdId']) ? intval($_POST['shepherdId']) : 0;

	$defaultUserTimeZone = $_POST['defaultUserTimeZone'];

	if ($shepherdId) {
		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
		$post = get_post($shepherdId);		
		
		$getOnlyFirstName = explode(" ", $post->post_title);
		$result['shepherd'] = $shepherdId;
		$result['title'] = $getOnlyFirstName[0];
		$result['full_name'] = $post->post_title;

		$thumbnail_urll = get_the_post_thumbnail_url($shepherdId, 'full');

		if (empty($thumbnail_urll)) {
			$thumbnail_urll = plugin_dir_url(__FILE__) . 'images/defaultFeatureImage.png';
		}
		// Get the featured image URL
		$result['featured_image_url'] = $thumbnail_urll;
		// Get the custom field time duration
		$result['duration_of_meeting'] = get_option('duration_of_meeting');

		//get DateTime Format Like Meeting Datetime
		$result['todayDateTime'] = getDateTimeFormatLikeMeetingDatetime();


		if (!empty(get_post_meta($shepherdId, 'sub_title', true))) {
			$result['sub_title'] = get_post_meta($shepherdId, 'sub_title', true);
		}
		if (!empty(get_post_meta($shepherdId, 'contact_email', true))) {
			$result['contact_email'] = get_post_meta($shepherdId, 'contact_email', true);
		}

		if (!empty(get_post_meta($shepherdId, 'default_working_days', true))) {
			$result['default_working_days'] = get_post_meta($shepherdId, 'default_working_days', true);
		}

		if (!empty(get_post_meta($shepherdId, 'admin_shephred_timezone', true))) {
			$result['admin_shephred_timezone'] = get_post_meta($shepherdId, 'admin_shephred_timezone', true);
		}

		if (!empty(get_post_meta($shepherdId, 'default_working_hours_start', true))) {
			$result['default_working_hours_start'] = get_post_meta($shepherdId, 'default_working_hours_start', true);
		}
		if (!empty(get_post_meta($shepherdId, 'default_working_hours_end', true))) {
			$result['default_working_hours_end'] = get_post_meta($shepherdId, 'default_working_hours_end', true);
		}


		if (!empty(get_post_meta($shepherdId, 'admin_shephred_timezone', true))) {
			$admin_shephred_timezone = get_post_meta($shepherdId, 'admin_shephred_timezone', true);
		}

		if (!empty(get_post_meta($shepherdId, 'default_working_hours_start', true))) {
			$default_working_hours_start = get_post_meta($shepherdId, 'default_working_hours_start', true);
		}
		if (!empty(get_post_meta($shepherdId, 'default_working_hours_end', true))) {
			$default_working_hours_end = get_post_meta($shepherdId, 'default_working_hours_end', true);
		}
		restore_current_blog(); // <-- ADDED: Restore the context
		// function get convert Meeting start And End Time According To Timezone
		$convertedStartAndEndtime = convertMeetingstartAndEndTimeAccordingToTimezone($shepherdId, $defaultUserTimeZone, $admin_shephred_timezone, $default_working_hours_start, $default_working_hours_end);
		//Get all shephred Booked Meeting List
		$result['allBookedShephredMeeting'] = getTodaysAndUpcomingShepherdMeetingsByShepherdId($shepherdId, $defaultUserTimeZone, $admin_shephred_timezone);
		$result['default_se##--------'] = $admin_shephred_timezone . '&' . $default_working_hours_start . '&' . $default_working_hours_end . '&' . $admin_shephred_timezone . '&' . $defaultUserTimeZone . '&' . $convertedStartAndEndtime;
		$shephredstartAndEndtm = explode('&', $convertedStartAndEndtime);
		$result['admin_shephred_timezone'] = $admin_shephred_timezone;
		$result['default_working_hours_start'] = $shephredstartAndEndtm[0];
		$result['default_working_hours_end'] = $shephredstartAndEndtm[1];
		$result['convertedStartAndEndtime'] = $convertedStartAndEndtime;



		// Create a DateTime object for the current time in 'America/New_York'
		$shphredTimeZone_s = new DateTime('now', new DateTimeZone($defaultUserTimeZone));
		// Get hours and minutes in 24-hour format
		$gethours = $shphredTimeZone_s->format('H'); // Hours in 24-hour format (00-23)
		$getminutes = $shphredTimeZone_s->format('i'); // Minutes (00-59)
		$start_time_current_Day = $gethours . '&' . $getminutes;

		// Format the date to a string that can be parsed by JavaScript
		$formatted_datevvv = $shphredTimeZone_s->format('Y-m-d H:i:s');

		$result['default_user_start_time_current_day'] = $start_time_current_Day;
		$result['defaultUserCurrentDatetime'] = $formatted_datevvv;

		echo json_encode($result);

		//echo $results;
	}

	wp_die(); // Required to terminate script execution
}
add_action('wp_ajax_getShephredInfoFunction', 'getShephredInfoFunction'); // For logged-in users
//add_action('wp_ajax_nopriv_getShephredInfoFunction', 'getShephredInfoFunction'); // For non-logged-in users

/*################################################################################################*/
// Genrate Token Zoom API Token function 
/*################################################################################################*/
function genrateZoomTokenUsingCURL()
{

	$return_respopnce = '';
	// Get the plugin directory URL
	$plugin_url = plugin_dir_url(__FILE__);
	$certificate_location = $plugin_url . 'ca_cert/cacert.pem';
	$zoom_api_account_id = ZOOM_API_ACCOUNT_ID;
	$zoom_api_client_id = ZOOM_API_CLIENT_ID;
	$zoom_api_client_secret_base64_key = BASE64_CLIENT_ID_AND_SECRET;


	$data = array('grant_type' => 'account_credentials', 'account_id' => $zoom_api_account_id);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.zoom.us/oauth/token');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $certificate_location);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $certificate_location);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Authorization: Basic ' . $zoom_api_client_secret_base64_key,
		//'Content-Type: application/x-www-form-urlencoded'
	]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

	$response = curl_exec($ch);

	if ($response === false) {
		$return_respopnce = curl_error($ch);
	} else {
		$decoded_response = json_decode($response, true);
		$access_token = $decoded_response['access_token'];
		// Use the $access_token to make Zoom API requests
		$return_respopnce = $access_token;
	}

	curl_close($ch);
	return $return_respopnce;
}


/*################################################################################################*/
// create zoom meeting function using CURL
/*################################################################################################*/
function createZoomMeetingUsingCURL($duration, $startDateTime, $topic, $tokens, $admin_shephred_timezone)
{
	global $wpdb;

	$curl = curl_init();
	//"start_time": "2024-01-10T20:30:00Z", 
	curl_setopt_array(
		$curl,
		array(
			CURLOPT_URL => 'https://api.zoom.us/v2/users/me/meetings',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '{ 
		"duration": ' . $duration . ',
		"password": "123456",	 
		"start_time": "' . $startDateTime . '", 
		"timezone": "' . $admin_shephred_timezone . '",
		"topic": "' . $topic . '",
		"type": 2
		}',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $tokens

			),
		)
	);

	$response = curl_exec($curl);
	curl_close($curl);
	return $response;
}

/*################################################################################################*/
// send booking details on mail to user and shepherd
/*################################################################################################*/
function sendBookingDetailsOnEmailToUserAndShephred($shepherdId, $zoomResponce, $meetingDateTime, $post_id, $shepherd_mail_datetime)
{
	// Get the current user
	$current_user = wp_get_current_user();

	// Email addresses
	$user_email = $current_user->user_email;
	if (!empty(get_post_meta($shepherdId, 'contact_email', true))) {
		//  shephred's email address
		$shephred_email = get_post_meta($shepherdId, 'contact_email', true);
	}

	//$string = "Meeting Date:- 2025-04-21 and Time:- 09:00 AM - 09:30 AM";
	$parts = explode(" and ", $meetingDateTime);
	$user_date = trim($parts[0]); // "Meeting Date:- 2025-04-21"
	$user_time = trim($parts[1]); // "Time:- 09:00 AM - 09:30 AM"

	$shepherd_parts = explode(" and ", $shepherd_mail_datetime);
	$shepherd_date = trim($shepherd_parts[0]); // "Meeting Date:- 2025-04-21"
	$shepherd_time = trim($shepherd_parts[1]); // "Time:- 09:00 AM - 09:30 AM"

	$c_user_id = $current_user->ID;
	$user_state = get_user_meta($c_user_id, 'user_state', true);

	// Send email to the seller/buyer user	 
	$resd = send_email('sendZoomMeetingNotificationToUserEmail', $user_email, $data = ['meetingDateTime' => $meetingDateTime, 'display_name' => $current_user->display_name, 'join_url' => $zoomResponce['join_url'], 'zoom_id' => $zoomResponce['id'], 'user_date' => $user_date, 'user_time' => $user_time]);

	// Send email to the advisor	
	$resds = send_email('sendZoomMeetingNotificationToShepherdEmail', $shephred_email, $data = ['shepherdTitle' => get_the_title($shepherdId), 'display_name' => $current_user->display_name, 'user_email' => $current_user->user_email, 'shepherd_mail_datetime' => $shepherd_mail_datetime, 'join_url' => $zoomResponce['join_url'], 'zoom_id' => $zoomResponce['id'], 'shepherd_date' => $shepherd_date, 'shepherd_time' => $shepherd_time, 'user_state' => $user_state]);

	// Display the result
	if ($resd) {
		update_post_meta($post_id, 'user_email', 'email sent successfully.');
		update_post_meta($post_id, 'user_meeting_date_time', $meetingDateTime);
		update_post_meta($post_id, 'shepherd_meeting_date_time', $shepherd_mail_datetime);
	} else {
		update_post_meta($post_id, 'user_email', 'Error sending email. Check your server configuration.');
	}

}

/*################################################################################################*/
// update default shepherd in dtabase table 'users_shepherd'
/*################################################################################################*/
 function setDefaultShephredForMrting($shepherd_wp_user_id, $user_id)
{
	global $wpdb;

	$current_user = wp_get_current_user();
	$user_roles = $current_user->roles;
	$current_role = reset($user_roles); // Get the first role (usually the primary role)	

	$common_config = include get_stylesheet_directory() . '/config/config.php';
	$businessLine = $common_config['B4L_CURRENT_DOMAIN']; 

	// Table name
	$table_name = $wpdb->base_prefix . 'users_shepherd';

	// Prepare and run query
	$query = $wpdb->prepare(
		"SELECT * FROM $table_name WHERE user_id = %d AND business_line = %s AND shepherd_user_id = %d",
		$user_id,
		$businessLine,
		$shepherd_wp_user_id
	);

	$results = $wpdb->get_results($query, ARRAY_A);

	// Log query results
	error_log("setDefaultShephredForMrting(): Checking existing rows for user_id={$user_id}, shepherd_id={$shepherd_wp_user_id}, businessLine={$businessLine}");
	error_log("setDefaultShephredForMrting(): Found " . count($results) . " matching rows.");

	if (empty($results)) {
		// Insert data into the table
		$result = $wpdb->insert(
			$table_name,
			array(
				'user_id'          => $user_id,
				'shepherd_user_id' => $shepherd_wp_user_id,
				'business_line'    => $businessLine,
				'user_role'        => $current_role,
				'is_active'        => 1
			),
			array('%d', '%d', '%s', '%s', '%d')
		);

		// Check if the insertion was successful
		if ($result === false) {
			$error_message = "Error inserting data: " . $wpdb->last_error;
			error_log("setDefaultShephredForMrting(): $error_message");
			return $error_message;
		} else {
			error_log("setDefaultShephredForMrting(): Data inserted successfully for user_id={$user_id}");
			return "Data successfully inserted!";
		}
	} else {
		error_log("setDefaultShephredForMrting(): Record already exists, skipping insert for user_id={$user_id}");
	}
}


/*################################################################################################*/
// ajax meeting handler on popup confirm submit buttons
/*################################################################################################*/

function create_meeting_post_ajax_handler()
{
	// Check if the request is coming from a valid source     
	check_ajax_referer('create_zoom_meeting_nonce', 'zoom_security');

	global $wpdb;

	$current_user = wp_get_current_user();

	if (!empty($_POST['meeting_selected_date']) && !empty($_POST['meeting_selected_time']) && !empty($_POST['shaphredId'])) {


		// Get the post data
		$meeting_selected_date = $_POST['meeting_selected_date'];
		$meeting_selected_time = $_POST['meeting_selected_time'];
		$shaphredId = $_POST['shaphredId'];
		$meeting_start_time = $_POST['meeting_start_time'];
		$meeting_end_time = $_POST['meeting_end_time'];
		$defaultShephredTimeZone = $_POST['defaultShephredTimeZone'];
		$defaultUserTimeZone = $_POST['defaultUserTimeZone'];
		$meeting_reason = $_POST['meeting_reason'];
		$currentDomainsName = $_POST['currentDomainsName'];
		



		// Set the original time and time zone
		$originalTime = new DateTime($meeting_start_time, new DateTimeZone($defaultUserTimeZone));
		// Set the target time zone
		$targetTimeZone = new DateTimeZone($defaultShephredTimeZone);
		// Convert the time to the target time zone
		$originalTime->setTimezone($targetTimeZone);
		// Display the converted time
		///echo "Original Time (America/Anchorage): " . $originalTime->format('H:i') . "<br>";
		$meeting_start_time = $originalTime->format('H:i:s');

		$meeting_orignal_start_time_for_shephred = $originalTime->format('H:i A');

		// Set the original time and time zone
		$originalETime = new DateTime($meeting_end_time, new DateTimeZone($defaultUserTimeZone));
		// Set the target time zone
		$targetETimeZone = new DateTimeZone($defaultShephredTimeZone);
		// Convert the time to the target time zone
		$originalETime->setTimezone($targetETimeZone);
		// Display the converted time	  
		$meeting_end_time = $originalETime->format('H:i:s');
		$meeting_orignal_end_time_for_shephred = $originalETime->format('H:i A');

		$currentISTTime = $meeting_selected_date . 'T' . $meeting_start_time;

		if ($current_user->first_name != "" && $current_user->last_name != "") {
			$user_display_name = $current_user->first_name . ' ' . $current_user->last_name;
		} else {
			$user_display_name = $current_user->user_login;
		}

		$post_title = "Meeting With " . $user_display_name . '-' . $meeting_selected_date . ' ' . $meeting_orignal_start_time_for_shephred . ' ' . $meeting_orignal_end_time_for_shephred;

		$shepherd_mail_datetime = 'Date: ' . $meeting_selected_date . ' and Time: ' . $meeting_orignal_start_time_for_shephred . '-' . $meeting_orignal_end_time_for_shephred;

		$meetingDateTime = 'Date: ' . $meeting_selected_date . ' and Time: ' . $meeting_selected_time;


		switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)

		// Create the post
		$post_id = wp_insert_post(
			array(
				'post_title' => $post_title,
				'post_content' => 'Test',
				'post_status' => 'publish',
				'post_type' => 'meeting',
			)
		);

		// Send the response
		if ($post_id) {

			//"10:00 AM - 11:00 AM";		 
			$meeting_selected_start_time = $meeting_selected_date . 'T' . $meeting_start_time;
			$meeting_selected_end_time = $meeting_selected_date . 'T' . $meeting_end_time;
			// Save the datetime value to a custom field
			update_post_meta($post_id, 'shepherd_user_id', $shaphredId);
			update_post_meta($post_id, 'requester_user_id', $current_user->ID);
			update_post_meta($post_id, 'creater_user_id', $current_user->ID);
			update_post_meta($post_id, 'start_date_time', $meeting_selected_start_time);
			update_post_meta($post_id, 'end_date_time', $meeting_selected_end_time);
			update_post_meta($post_id, 'meeting_reason', $meeting_reason);
			update_post_meta($post_id, 'currentDomainsName', $currentDomainsName);

			

			

			//update_post_meta($post_id, 'originalETimeaaaaaa', $originalETime);




			$usrTimeZones = [
				'America/New_York' => 'America/New_York',
				'America/Chicago' => 'America/Chicago',
				'America/Phoenix' => 'America/Phoenix',
				'America/Los_Angeles' => 'America/Los_Angeles',
				'America/Anchorage' => 'America/Anchorage',
				'Pacific/Honolulu' => 'Pacific/Honolulu'
			];

			$usrZone = array_search($defaultUserTimeZone, $usrTimeZones);

			if ($usrZone !== false) {
				//echo "The key for value '{$search_value}' is '{$usrZone}'";
				update_user_meta($current_user->ID, 'defaultUserTimeZone', $usrZone);
			}

			$duration = get_option('duration_of_meeting');// Get the custom field time duration
			$startDateTime = $currentISTTime; //'2024-01-16T14:00:00';
			$topic = 'Broker4Less';
			$admin_shephred_timezone = get_post_meta($shaphredId, 'admin_shephred_timezone', true);
			$tokens = genrateZoomTokenUsingCURL(); // call function for genrate Token.		  

			if (!empty($tokens)) {
				$zoomResponce = createZoomMeetingUsingCURL($duration, $startDateTime, $topic, $tokens, $admin_shephred_timezone);

				// Convert JSON response to PHP array
				$resultArray = json_decode($zoomResponce, true);
				update_post_meta($post_id, 'is_zoom_registered', 1);
				if ($resultArray['join_url']) {
					update_post_meta($post_id, 'zoom_meeting_url', $resultArray['join_url']);
					update_post_meta($post_id, 'zoom_meeting_id', $resultArray['id']);

					sendBookingDetailsOnEmailToUserAndShephred($shaphredId, $resultArray, $meetingDateTime, $post_id, $shepherd_mail_datetime);

					$shepherd_wp_user_id = get_post_meta($shaphredId, 'shepherd_wp_user_id', true);

					$isRecordUpdate = setDefaultShephredForMrting($shepherd_wp_user_id, $current_user->ID);
					

					// hide welcome section on schedule meeting page
					update_user_meta($current_user->ID, 'hide_welcome_section', 1);


				} else {
					update_post_meta($post_id, 'zoom_meeting_url', $resultArray['message']);
				}

				update_post_meta($post_id, 'zoom_json_response', $resultArray);


			} else {
				$tokens = "Token Not Genrated";
			}

			echo json_encode(array('success' => true, '$resultArray_join_url' => $resultArray['join_url'], 'tokens' => $tokens, 'post_id' => $post_id, "zoomResponce" => $zoomResponce, "meeting_selected_start_time" => $meeting_selected_start_time, "meeting_selected_end_time" => $meeting_selected_end_time, 'isRecordUpdate' => $isRecordUpdate, 'admin_shephred_timezone' => $admin_shephred_timezone, 'startDateTimeShephred' => $startDateTime));
					

		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to create post.'));
			 
		}
		restore_current_blog(); // <-- ADDED: Restore the context	
		
	}

	// Always exit to avoid further execution
	wp_die();
}
add_action('wp_ajax_create_meeting_post_ajax_handler', 'create_meeting_post_ajax_handler'); // For logged-in users
//add_action('wp_ajax_nopriv_create_meeting_post_ajax_handler', 'create_meeting_post_ajax_handler'); // For non-logged-in users

/*################################################################################################*/
// get User TimeZone according to display Shephred Ajax Action
/*################################################################################################*/
function getUserTimeZoneShephredAjaxAction()
{
	$selected_user_timezone = sanitize_text_field($_POST['selected_user_timezone']);

	global $wpdb;
	$current_user = wp_get_current_user();
	$userID = $current_user->ID;
	// Table name
	$table_name = $wpdb->base_prefix . 'users_shepherd';
	// Prepare and execute the SQL query
	$query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d LIMIT 1", $userID);
	$result = $wpdb->get_row($query, ARRAY_A);
	switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
	if ($result) {
	
		// The query arguments
		$args = array(
			'post_type' => 'shepherd', // Adjust post type if needed
			'posts_per_page' => 1, // Retrieve all posts
			'meta_query' => array(
				array(
					'key' => 'shepherd_wp_user_id',
					'value' => $result['shepherd_user_id'],
					'compare' => '=',
				),
			),
		);


		// Instantiate the WP_Query
		$query = new WP_Query($args);
		 
		$final_order = $query->get_posts();


	} else {
		 
		$args = array(
			'post_type' => 'shepherd', // Replace 'your_post_type' with your actual post type
			'posts_per_page' => -1, // Retrieve all posts    
			'meta_key' => 'admin_shephred_timezone',
			'orderby' => 'meta_value', // Order by the meta value          
			'order' => 'ASC', // Set to 'DESC' for descending order
		);
		$query = new WP_Query($args);
		$all_posts = $query->get_posts();
		$matching_posts = array();
		$remaining_posts = array();

		foreach ($all_posts as $post) {
			if (get_post_meta($post->ID, 'admin_shephred_timezone', true) == $selected_user_timezone) {
				// Matching record
				$matching_posts[] = $post;
			} else {
				// Remaining records
				$remaining_posts[] = $post;
			}
		}
		
		// Merge the two arrays to get the desired order
		$final_order = array_merge($matching_posts, $remaining_posts);
	}

	$result = [];
	// Display the combined posts
	foreach ($final_order as $post) {
		$thumUrl = get_the_post_thumbnail_url($post->ID, 'full');
		if (empty($thumUrl)) {
			$thumUrl = plugin_dir_url(__FILE__) . 'images/defaultFeatureImage.png';
		}

		$result[] = array(
			'id' => $post->ID,
			'title' => $post->post_title,
			'thumbnail_url' => $thumUrl,
			'admin_shephred_timezone' => get_post_meta($post->ID, 'admin_shephred_timezone', true),
		);
	}

	wp_reset_postdata(); // Restore the global post object
	restore_current_blog(); // <-- ADDED: Restore the context
	$res = json_encode($result);
	echo $res;
	// Always exit to avoid further execution
	wp_die();
}
add_action('wp_ajax_getUserTimeZoneShephredAjaxAction', 'getUserTimeZoneShephredAjaxAction'); // For logged-in users
//add_action('wp_ajax_nopriv_getUserTimeZoneShephredAjaxAction', 'getUserTimeZoneShephredAjaxAction'); // For non-logged-in users

/*################################################################################################*/
// Add Extra field in add_new_user on admin side for shepherd user
/*################################################################################################*/
// Hook function to add custom input field to the Add New User page
 function add_custom_user_fields($operation)
{
	if (!current_user_can('administrator')) {
		return;
	}

	$weekdays = array(
		'Sunday',
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday'
	);

	$usTimeZones = [
		'America/New_York'    => 'Eastern Standard Time',
		'America/Chicago'     => 'Central Standard Time',
		'America/Phoenix'     => 'Mountain Standard Time',
		'America/Los_Angeles' => 'Pacific Standard Time',
		'America/Anchorage'   => 'Alaska Standard Time',
		'Pacific/Honolulu'    => 'Hawaii-Aleutian Standard Time',
	];

	?>

	<style>

		/* Hide by default */
		#shepherd-custom-fields {
			display: none;
		}

		.weekday-checkbox {
			display: inline-block;
			margin-right: 10px;
			margin-bottom: 10px;
		}

	</style>

	<!-- ONLY FOR NEW USER SECTION -->
	<div id="shepherd-custom-fields">

		<h2>
			<?php _e('User Information for Shepherd Role', 'appointment meeting online'); ?>
		</h2>

		<table class="form-table">

			<tr class="form-required">
				<th>
					<label for="sub_title">
						<?php _e('Title (required)', 'appointment meeting online'); ?>
					</label>
				</th>

				<td>

					<input
						type="text"
						name="sub_title"
						id="sub_title"
						class="regular-text"
						value="Web conferencing details provided upon confirmation"
					/>

					<p>
						<i>This field will have a default value if left empty</i>
					</p>

				</td>
			</tr>

			<tr class="form-required">
				<th>
					<label for="default_working_hours_start">
						<?php _e('Default Working Hours Start', 'appointment meeting online'); ?>
					</label>
				</th>

				<td>

					<input
						type="time"
						name="default_working_hours_start"
						id="default_working_hours_start"
						class="regular-text"
					/>

				</td>
			</tr>

			<tr class="form-required">
				<th>
					<label for="default_working_hours_end">
						<?php _e('Default Working Hours End', 'appointment meeting online'); ?>
					</label>
				</th>

				<td>

					<input
						type="time"
						name="default_working_hours_end"
						id="default_working_hours_end"
						class="regular-text"
					/>

				</td>
			</tr>

			<tr class="form-required checkbox-container">

				<th>
					<label>
						<?php _e('Default Working Days', 'appointment meeting online'); ?>
					</label>
				</th>

				<td>

					<?php
					foreach ($weekdays as $day) {
						?>

						<label class="weekday-checkbox">

							<input
								type="checkbox"
								name="default_working_days[]"
								value="<?php echo esc_attr($day); ?>"
							/>

							<?php echo esc_html($day); ?>

						</label>

						<?php
					}
					?>

				</td>

			</tr>

			<tr class="form-required">

				<th>
					<label for="admin_shephred_timezone">
						<?php _e('Default Timezone', 'appointment meeting online'); ?>
					</label>
				</th>

				<td>

					<select
						id="admin_shephred_timezone"
						name="admin_shephred_timezone"
					>

						<?php
						foreach ($usTimeZones as $timezone => $timezoneName) {
							?>

							<option value="<?php echo esc_attr($timezone); ?>">

								<?php
								echo esc_html($timezoneName . ' (' . $timezone . ')');
								?>

							</option>

							<?php
						}
						?>

					</select>

				</td>

			</tr>

		</table>

	</div>

	 <script type="text/javascript">

jQuery(document).ready(function ($) {

    /*
    |--------------------------------------------------------------------------
    | MOVE CUSTOM SECTION TO NEW USER FORM ONLY
    |--------------------------------------------------------------------------
    */
     
	if ($('#createuser').length) {

		// Place fields BEFORE Add User button row
		$('#shepherd-custom-fields').insertBefore(
			$('#createuser').find('p.submit')
		);
	}

    /*
    |--------------------------------------------------------------------------
    | GET ROLE FIELD
    |--------------------------------------------------------------------------
    */
    function getNewUserRoleField() {

        // Multisite new user form
        if ($('#new_role').length) {
            return $('#new_role');
        }

        // Single site fallback
        if ($('#role').length) {
            return $('#role');
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW/HIDE SECTION
    |--------------------------------------------------------------------------
    */
    function toggleShepherdFields() {

        let roleField = getNewUserRoleField();

        if (!roleField || !roleField.length) {
            return;
        }

        let selectedRole = roleField.val();

        console.log('Selected Role:', selectedRole);

        if (selectedRole === 'shepherd') {

            $('#shepherd-custom-fields').show();

        } else {

            $('#shepherd-custom-fields').hide();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */
    setTimeout(function () {

        toggleShepherdFields();

    }, 200);

    /*
    |--------------------------------------------------------------------------
    | ROLE CHANGE
    |--------------------------------------------------------------------------
    */
    $(document).on('change', '#new_role, #role', function () {

        toggleShepherdFields();
    });

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */
    $('#createusersub').on('click', function (event) {

        let roleField = getNewUserRoleField();

        if (roleField && roleField.val() === 'shepherd') {

            if ($('input[name="default_working_days[]"]:checked').length === 0) {

                event.preventDefault();

                alert('Please select at least one working day.');
            }
        }
    });

});

</script>

	<?php
}

add_action('user_new_form', 'add_custom_user_fields');


/*################################################################################################*/
// Hook function to add custom input field to the Edit User page in admin side
/*################################################################################################*/
function edit_custom_user_field($user)
{
	// Check if the user has the 'shepherd' role
	if (in_array('shepherd', $user->roles)) {
		// User has the 'shepherd' role, let's create a post with the 'shepherd_post' type

		$weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
		$usTimeZones = [
			'America/New_York' => 'Eastern Standard Time',
			'America/Chicago' => 'Central Standard Time',
			'America/Phoenix' => 'Mountain Standard Time',
			'America/Los_Angeles' => 'Pacific Standard Time',
			'America/Anchorage' => 'Alaska Standard Time',
			'Pacific/Honolulu' => 'Hawaii-Aleutian Standard Time',
		];

		$default_working_days = get_user_meta($user->ID, 'default_working_days', true);
		$default_working_days = $default_working_days != null ? explode(";", $default_working_days) : null;

		$admin_shephred_timezone = get_user_meta($user->ID, 'admin_shephred_timezone', true);
		$admin_shephred_timezone = $admin_shephred_timezone != null ? $admin_shephred_timezone : '';
		$sub_title = get_user_meta($user->ID, 'sub_title', true);

		?>
		<h2><?php _e('User Infromation for Shepherd Role', 'appointment meeting online'); ?></h2>
		<table class="form-table">

			<tr class="form-required">
				<th><label for="custom_field"><?php _e('Title:', 'appointment meeting online'); ?></label></th>
				<td>
					<input type="text" name="sub_title" id="sub_title" aria-required="true"
						value="<?php echo esc_attr($sub_title? $sub_title :"Web conferencing details provided upon confirmation"); ?>" class="regular-text" />
						<i>This field will have a default value if left empty</l>
					<!-- <p class="description">< ?php _e('Enter your custom information here.', 'appointment meeting online'); ?></p> -->
				</td>
			</tr>
			<tr class="form-required">
				<th><label for="custom_field"><?php _e('Default Working Hours Start:', 'appointment meeting online'); ?></label>
				</th>
				<td>
					<input type="time" name="default_working_hours_start" id="default_working_hours_start" aria-required="true"
						value="<?php echo esc_attr(get_user_meta($user->ID, 'default_working_hours_start', true)); ?>"
						class="regular-text" />
				</td>
			</tr>

			<tr class="form-required">
				<th><label for="custom_field"><?php _e('Default Working Hours End:', 'appointment meeting online'); ?></label>
				</th>
				<td>
					<input type="time" name="default_working_hours_end" id="default_working_hours_end" aria-required="true"
						value="<?php echo esc_attr(get_user_meta($user->ID, 'default_working_hours_end', true)); ?>"
						class="regular-text" />
				</td>
			</tr>
			<tr class="form-required">
				<th><label for="custom_field"><?php _e('Default Working Days:', 'appointment meeting online'); ?></label></th>
				<td>
					<?php
					foreach ($weekdays as $day) {
						// /default_working_days
						$checked = $default_working_days != null ? in_array($day, $default_working_days) ? 'checked' : '' : '';

						echo '<label class="weekday-checkbox">
						            <input type="checkbox" name="default_working_days[]" class="form-control" value="' . esc_attr($day) . '" id="' . esc_attr($day) . '" ' . $checked . '>
						            ' . esc_html($day) . '
						        </label> &emsp;';
					} ?>

				</td>
			</tr>
			<tr class="form-required">
				<th><label for="custom_field"><?php _e('Default Timezone:', 'appointment meeting online'); ?></label>
				</th>
				<td> <?php
				echo '<select id="admin_shephred_timezone" name="admin_shephred_timezone" aria-required="true">';
				// Display current time in each USA time zone
				foreach ($usTimeZones as $timezone => $timezoneName) {

					echo '<option value="' . $timezone . '"' . ($admin_shephred_timezone == $timezone ? ' selected="selected"' : '') . '>' . $timezone . '</option>';

				}
				echo '</select>';
				?>
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('#createusersub').click(function (event) {
					var isValid = true;
					$(".checkbox-container").removeClass("form-invalid");
					// Check if at least one working day is selected
					if ($('input[name="default_working_days[]"]:checked').length == 0) {
						$(".checkbox-container").addClass("form-invalid");
						isValid = false;
					}

					// Prevent form submission if validation fails
					if (!isValid) {
						event.preventDefault();
					}
				});
			});
		</script>
		<?php
	}
}
// Hook the edit_custom_user_field function to the show_user_profile hook
add_action('edit_user_profile', 'edit_custom_user_field');

/*################################################################################################*/
// Hook function to save the custom field when a new user is created or an existing user is updated
/*################################################################################################*/

// Function to get post ID by email from a custom post type
function get_post_id_by_email($email)
{
	$post_id = 0; // Initialize post_id
    switch_to_blog(1); // Switch to the main site (ID 1)
    
    $args = array(
        'post_type' => 'shepherd',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'contact_email',
                'value' => $email,
            ),
        ),
    );
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $post_id = $query->posts[0]->ID;
    }
    
    restore_current_blog(); // Restore the context before returning
    
    return $post_id;
}

/*################################################################################################*/
// create or update shepherd post on user_register
/*################################################################################################*/
function create_or_update_shepherd_post_on_user_register($action, $user)
{

	$user_id = $user->ID;
	if (empty($user_id)) {
		return;
	}
	// Only allow admin.
	if (!current_user_can('manage_options')) {
		return;
	}
	if (isset($user->first_name)) {
		update_user_meta($user_id, 'first_name', $user->first_name);
	}
	if (isset($user->last_name)) {
		update_user_meta($user_id, 'last_name', $user->last_name);
	}

	if (isset($_POST['sub_title'])) {
		update_user_meta($user_id, 'sub_title', sanitize_text_field($_POST['sub_title']));
	}
	if (isset($_POST['default_working_hours_start'])) {
		update_user_meta($user_id, 'default_working_hours_start', sanitize_text_field($_POST['default_working_hours_start']));
	}
	if (isset($_POST['default_working_hours_end'])) {
		update_user_meta($user_id, 'default_working_hours_end', sanitize_text_field($_POST['default_working_hours_end']));
	}
	if (isset($_POST['default_working_days'])) {
		$default_working_days = isset($_POST['default_working_days']) ? implode(';', $_POST['default_working_days']) : '';

		update_user_meta($user_id, 'default_working_days', sanitize_text_field($default_working_days));
	}

	if (isset($_POST['admin_shephred_timezone'])) {
		update_user_meta($user_id, 'admin_shephred_timezone', sanitize_text_field($_POST['admin_shephred_timezone']));
	}

	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Start Add User Role in all sites*/  
	// 1. Get the custom role chosen during registration
	// This part depends on how you saved the field data in your custom registration
	// If you saved it as user meta: 
	$red_role = 'shepherd';
	// 2. Get all sites in the network
	$sites = get_sites( array( 
		'fields' => 'ids', 
		'public' => 1, 
		'deleted' => 0, 
		'archived' => 0 
	) );            
	// 3. Loop through all sites and add the user with the chosen role
	foreach ( $sites as $site_id ) {
		// Only add the user if they are not already a member of that site
		if ( ! is_user_member_of_blog( $user_id, $site_id ) ) {
			add_user_to_blog( $site_id, $user_id, $red_role );
		}
	}
	/*@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ End Add User Role in all sites*/ 
	// Switch to the main site for CPT operations
    switch_to_blog(1);
	if ($action === 'update') {
		// Usage example
		$email_to_search = $user->user_email;
		$post_id = get_post_id_by_email($email_to_search);
	} else {
		// Post doesn't exist, create a new post
		$post_data = array(
			'post_title' => $user->first_name . ' ' . $user->last_name,
			'post_content' => 'This is the content of the Shepherd post for ' . $user->first_name . ' ' . $user->last_name,
			'post_type' => 'shepherd',
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post($post_data);
	}


	update_post_meta($post_id, 'shepherd_wp_user_id', $user_id);

	if (isset($_POST['sub_title'])) {
		update_post_meta($post_id, 'sub_title', sanitize_text_field($_POST['sub_title']));
	}
	if (!empty($user->first_name)) {
		update_post_meta($post_id, 'first_name', $user->first_name);
	}
	if (!empty($user->last_name)) {
		update_post_meta($post_id, 'last_name', $user->last_name);
	}

	if (!empty($user->user_email)) {
		update_post_meta($post_id, 'contact_email', $user->user_email);
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
	restore_current_blog(); // Restore the context after CPT operations
}


/*################################################################################################*/
// function for saved custom usermeta field on user register
/*################################################################################################*/

function save_custom_users_field($user_id)
{
	// Get the user data
	$user = get_user_by('ID', $user_id);
	// Check if the user has the 'shepherd' role
	if (in_array('shepherd', $user->roles)) {
		create_or_update_shepherd_post_on_user_register('create', $user);
	}
}
// Hook the save_custom_user_field function to the user_register and profile_update hooks
add_action('user_register', 'save_custom_users_field');

/*################################################################################################*/
// function for update custom usermeta field on user register
/*################################################################################################*/

function update_custom_users_field($user_id)
{
	// Get the user data
	$user = get_user_by('ID', $user_id);
	// Check if the user has the 'shepherd' role
	if (in_array('shepherd', $user->roles)) {
		create_or_update_shepherd_post_on_user_register('update', $user);
		// for update shapherd post title
		$email_to_search = $user->user_email;
		$post_id = get_post_id_by_email($email_to_search);

		if ($post_id) {
			switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
			// Post already exists, update the existing post		       
			$post_data = array(
				'ID' => $post_id,
				'post_title' => $user->first_name . ' ' . $user->last_name,
				'post_content' => 'This is the content of the Shepherd post for ' . $user->first_name . ' ' . $user->last_name,
				'post_type' => 'shepherd',
				'post_status' => 'publish',
			);
			
			wp_update_post($post_data);
			restore_current_blog(); // <-- ADDED: Restore the context
		}
	}
}
add_action('profile_update', 'update_custom_users_field');



/*################################################################################################*/
// function for hide welcome section on clicked cross button or user have alredy scheduled meeting
/*################################################################################################*/
add_action('wp_ajax_hide_welcome_section_update_user_meta_ajax', 'hide_welcome_section_update_user_meta_ajax');
function hide_welcome_section_update_user_meta_ajax()
{
	if (is_user_logged_in()) {
		$user_id = get_current_user_id();
		$meta_key = $_POST['hide_welcome_section'];
		$meta_value = 1;

		// Update user meta
		update_user_meta($user_id, $meta_key, $meta_value);

		// Send response
		wp_send_json_success('User meta updated successfully.');
	} else {
		wp_send_json_error('User is not logged in.');
	}
}



/************************************************************************************************************/
//Check meeting booking time is alredy booked or not 
/************************************************************************************************************/
add_action("wp_ajax_check_meeting_slot", "check_meeting_slot");
add_action("wp_ajax_nopriv_check_meeting_slot", "check_meeting_slot"); // if public

function check_meeting_slot() {
    global $wpdb;

    $slot_start = sanitize_text_field($_POST['slot_start']); // e.g. 2025-08-26T09:45:00
    $slot_end   = sanitize_text_field($_POST['slot_end']);   // e.g. 2025-08-26T10:15:00	 
	$shepherd_id   = intval($_POST['shepherd_id']);
	switch_to_blog(1); // <-- ADDED: Switch to the main site (ID 1)
    // Find if any meeting overlaps
	$args = [
		'post_type'      => 'meeting',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'key'     => 'start_date_time',
				'value'   => $slot_end,
				'compare' => '<',
                'type'    => 'DATETIME'
			],
			[
				'key'     => 'end_date_time',
				'value'   => $slot_start,
				'compare' => '>',
                'type'    => 'DATETIME'
			],
			[
				'key'     => 'shepherd_user_id',
				'value'   => $shepherd_id,  // variable holding shepherd’s user ID
				'compare' => '=',
				'type'    => 'NUMERIC'
			]
		]
	];


    $query = new WP_Query($args);

	

    if ($query->have_posts()) {
        wp_send_json_success(['isBooked' => true]);
    } else {
        wp_send_json_success(['isBooked' => false]);
    }
	restore_current_blog(); // <-- ADDED: Restore the context
	die();
}

?>