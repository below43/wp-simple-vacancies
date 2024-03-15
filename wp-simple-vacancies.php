<?php
/*
Plugin Name: WP Simple Vacancies
Description: This plugin creates a custom post type named 'Vacancy'.
Version: 1.0
Author: Andrew Drake <andrew@drake.nz>
License: MIT License
*/


function create_vacancy_post_type()
{

	$labels = array(
		'name' => __('Vacancies'),
		'singular_name' => __('Vacancy')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'rewrite' => array('slug' => 'vacancies'),
		'show_in_rest' => true,
		'supports' => array('title', 'thumbnail', 'categories'),
		'taxonomies' => array('category'),

		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 8,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type(
		'vacancy',
		$args
	);
}

add_action('init', 'create_vacancy_post_type');

function include_vacancies_in_category_pages($query)
{
	if ($query->is_category() && $query->is_main_query()) {
		$post_types = $query->get('post_type');
		if (is_array($post_types)) {
			$post_types[] = 'vacancy';
			print_r($post_types);
		} else {
			$post_types = array('post', 'vacancy');
		}
		$query->set('post_type', $post_types);
	}
}
add_action('pre_get_posts', 'include_vacancies_in_category_pages');


function add_vacancy_meta_boxes()
{
	add_meta_box('vacancy_meta_box', 'Vacancy Details', 'display_vacancy_meta_box', 'vacancy', 'normal', 'high');
}

add_action('add_meta_boxes', 'add_vacancy_meta_boxes');

function display_vacancy_meta_box($vacancy)
{
	$vacancy_text = get_post_meta($vacancy->ID, 'vacancy_text', true);
	$contact_name = get_post_meta($vacancy->ID, 'contact_name', true);
	$contact_email = get_post_meta($vacancy->ID, 'contact_email', true);
	$contact_phone = get_post_meta($vacancy->ID, 'contact_phone', true);
	$date_from = get_post_meta($vacancy->ID, 'date_from', true);
	$date_to = get_post_meta($vacancy->ID, 'date_to', true);
?>
	<table>
		<tr>
			<td style="width: 100%">Vacancy description</td>
			<td><textarea rows="4" cols="50" name="vacancy_text" id="vacancy_text"><?php echo $vacancy_text; ?></textarea></td>
		</tr>
		<tr>
			<td style="width: 100%">Contact name</td>
			<td><input type="text" size="80" name="contact_name" id="contact_name" value="<?php echo $contact_name; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%">Contact email</td>
			<td><input type="text" size="80" name="contact_email" id="contact_email" value="<?php echo $contact_email; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%">Contact phone</td>
			<td><input type="text" size="80" name="contact_phone" id="contact_phone" value="<?php echo $contact_phone; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%">Vacancy Display Date From</td>
			<td><input type="date" name="vacancy_date_from" id="vacancy_date_from" value="<?php echo $date_from; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%">Vacancy Display Date To</td>
			<td><input type="date" name="vacancy_date_to" id="vacancy_date_to" value="<?php echo $date_to; ?>" /></td>
		</tr>
	</table>
	<?php
}

function save_vacancy_meta_fields($post_id)
{
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (get_post_type($post_id) == 'vacancy') {
		if (isset($_POST['vacancy_text'])) {
			update_post_meta($post_id, 'vacancy_text', $_POST['vacancy_text']);
		}
		if (isset($_POST['contact_name'])) {
			update_post_meta($post_id, 'contact_name', $_POST['contact_name']);
		}
		if (isset($_POST['contact_email'])) {
			update_post_meta($post_id, 'contact_email', $_POST['contact_email']);
		}
		if (isset($_POST['contact_phone'])) {
			update_post_meta($post_id, 'contact_phone', $_POST['contact_phone']);
		}
		if (isset($_POST['vacancy_date_from'])) {
			update_post_meta($post_id, 'date_from', $_POST['vacancy_date_from']);
		}
		if (isset($_POST['vacancy_date_to'])) {
			update_post_meta($post_id, 'date_to', $_POST['vacancy_date_to']);
		}
		if (isset($_POST['job_description_url'])) {
			update_post_meta($post_id, 'job_description_url', $_POST['job_description_url']);
		}
		if (isset($_POST['employer_profile_url'])) {
			update_post_meta($post_id, 'employer_profile_url', $_POST['employer_profile_url']);
		}
	}
}

add_action('save_post', 'save_vacancy_meta_fields');

add_action('add_meta_boxes', 'add_vacancy_attachments_meta_box');

function add_vacancy_attachments_meta_box() {
    add_meta_box('vacancy_attachments_meta_box', 'Attachments', 'display_vacancy_attachments_meta_box', 'vacancy', 'normal', 'high');
}

function display_vacancy_attachments_meta_box($vacancy) {
    $job_description_url = get_post_meta($vacancy->ID, 'job_description_url', true);
    $employer_profile_url = get_post_meta($vacancy->ID, 'employer_profile_url', true);
    ?>
    <table>
        <tr>
            <td colspan="2"><i>Files can be locally uploaded media, or a link to another web page or file</i><br /><br /></td>
        </tr>
        <tr>
            <td style="width: 100%">Job description URL (optional)</td>
            <td>
				<br />
				<input type="text" size="80" name="job_description_url" id="job_description_url" value="<?php echo $job_description_url; ?>" />
				<br /><br />
				<button type="button" name="job_description_file" id="job_description_file">Select / Upload Media</button>
			</td>
        </tr>
        <tr>
            <td style="width: 100%">Employer Profile URL (optional)<br /><br /></td>
            <td>
				<br />
				<input type="text" size="80" name="employer_profile_url" id="employer_profile_url" value="<?php echo $employer_profile_url; ?>" />
				<br /><br />
				<button type="button" name="employer_profile_file" id="employer_profile_file">Select / Upload Media</button>
			</td>
        </tr>
    </table>




	<script>
		jQuery(document).ready(function($) {
			$('#employer_profile_file').click(function(e) {
				e.preventDefault();
				var custom_uploader = wp.media({
						title: 'Select File',
						button: {
							text: 'Use this File'
						},
						multiple: false // Set this to true to allow multiple files to be selected
					})
					.on('select', function() {
						var attachment = custom_uploader.state().get('selection').first().toJSON();
						$('#employer_profile_url').val(attachment.url);
					})
					.open();
			});
		});
		jQuery(document).ready(function($) {
			$('#job_description_file').click(function(e) {
				e.preventDefault();
				var custom_uploader = wp.media({
						title: 'Select File',
						button: {
							text: 'Use this File'
						},
						multiple: false // Set this to true to allow multiple files to be selected
					})
					.on('select', function() {
						var attachment = custom_uploader.state().get('selection').first().toJSON();
						$('#job_description_url').val(attachment.url);
					})
					.open();
			});
		});
	</script>


    <?php
}


add_action('admin_print_footer_scripts', 'vacancy_admin_script', 99);

function vacancy_admin_script()
{
	global $post_type;
	if ('vacancy' == $post_type) {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#publish').click(function() {
					if ($('#title').val() == '') {
						alert('Title is required');
						$('#title').focus();
						return false;
					}
					if ($('#vacancy_text').val() == '') {
						alert('Vacancy Description is required');
						$('#vacancy_text').focus();
						return false;
					}
					if ($('#vacancy_date_to').val() == '') {
						alert('Date To is required');
						$('#vacancy_date_to').focus();
						return false;
					}
					if ($('#vacancy_date_from').val() == '') {
						alert('Date From is required');
						$('#vacancy_date_from').focus();
						return false;
					}
					if ($('#contact_name').val() == '') {
						alert('Contact Name is required');
						$('#contact_name').focus();
						return false;
					}
					if ($('#contact_email').val() == '' || $('#contact_phone').val() == '') {
						alert('Contact Email or Phone is required');
						$('#contact_email').focus();
						return false;
					}
				});
			});
		</script>
<?php
	}
}

// Shortcode for displaying a single vacancy
add_shortcode('display_vacancy_item', 'display_vacancyboard_func');

function display_vacancyboard_func($atts)
{
	$atts = shortcode_atts(array('id' => '', 'job_description_label' => 'View Job Description', 'employer_profile_label' => 'View Employer Profile'), $atts, 'display_vacancyboard');

	$vacancy_id = $atts['id'];
	$vacancy = get_post($vacancy_id);
	$job_description_url = get_post_meta($vacancy_id, 'job_description_url', true);
	$employer_profile_url = get_post_meta($vacancy_id, 'employer_profile_url', true);
	$date_from = get_post_meta($vacancy_id, 'date_from', true);
	$date_to = get_post_meta($vacancy_id, 'date_to', true);
	$vacancy_text = get_post_meta($vacancy_id, 'vacancy_text', true);
	$thumbnail = get_the_post_thumbnail($vacancy_id);
	$output = "<h1>{$vacancy->post_title}</h1>
               {$thumbnail}
               <p>{$vacancy_text}</p>
			   ";
	$output .= "<p>Contact: " . get_post_meta($vacancy_id, 'contact_name', true) . "</p>
			   <p>Email: " . get_post_meta($vacancy_id, 'contact_email', true) . "</p>
			   <p>Phone: " . get_post_meta($vacancy_id, 'contact_phone', true) . "</p>
			   <p>Category: " . get_the_category_list(', ', '', $vacancy_id) . "</p>
               <p>Date From: {$date_from}</p>
               <p>Date To: {$date_to}</p>";
	if (!empty($job_description_url)) {
		$output .= "<a href='{$job_description_url}' target='_blank'>{$job_description_label}</a>";
	}
	if (!empty($employer_profile_url)) {
		$output .= "<a href='{$employer_profile_url}' target='_blank'>{$employer_profile_label}</a>";
	}
	return $output;
}

// Shortcode for displaying a list of vacancies
add_shortcode('display_vacancies_list', 'display_vacancyboard_list_func');

function display_vacancyboard_list_func($atts)
{
	// Extract the attributes
	$atts = shortcode_atts(
		array(
			'category' => '', // Default value
		),
		$atts,
		'display_vacancyboard_list_func'
	);

	// If a category is specified in the shortcode, or GET params, add it to the query args
	$category_name = (!empty($atts['category'])) ? $atts['category'] : sanitize_text_field($_GET['category']);

	// If a search term is submitted, add it to the query args
	$search_term = isset($_GET['resource_search_term']) ? sanitize_text_field($_GET['resource_search_term']) : '';

	$args = array(
		'post_type' => 'vacancy',
		'posts_per_page' => -1, // Get all posts
		'orderby'   => 'date',
		'order'     => 'DESC',
		'category_name' => $category_name, // 'category_name' is a query arg for 'post' type, not 'vacancy' type
		's' => $search_term,

		// 'meta_query' => array(
		// 	'relation' => 'AND',
		// 	array(
		// 		'key' => 'date_from',
		// 		'value' => date('Y-m-d'),
		// 		'compare' => '<=',
		// 		'type' => 'CHAR'
		// 	),
		// 	array(
		// 		'key' => 'date_to',
		// 		'value' => date('Y-m-d'),
		// 		'compare' => '>=',
		// 		'type' => 'CHAR'
		// 	)
		// )
	);

	$vacancies = new WP_Query($args);
	$output = '<br/><br/><ul class="simple-vacancyboard-list">';
	$count = 0;
	if ($vacancies->have_posts()) {
		while ($vacancies->have_posts()) {
			$vacancies->the_post();
			$vacancy_id = get_the_ID();
			$vacancy_text = get_post_meta($vacancy_id, 'vacancy_text', true);
			$vacancy_text = wp_trim_words($vacancy_text, 40);
			$thumbnail = get_the_post_thumbnail($vacancy_id);
			$categories = get_the_category_list(', ');

			$date_from = get_post_meta($vacancy_id, 'date_from', true);
			$date_to = get_post_meta($vacancy_id, 'date_to', true);
			$current_date = date('Y-m-d');

			// Convert the dates to Unix timestamps
			$date_from = strtotime($date_from);
			$date_to = strtotime($date_to);
			$current_date = strtotime($current_date);

			// Skip the post if the current date is not within the date range
			if ($current_date < $date_from || $current_date > $date_to) {
				$dodgy = 'true';
			}

			$vacancy_text .= "<p class=\"view-more-link\" ><a href='" . get_permalink() . "'>View More</a></p>";
			$output .= "<li>
                        {$thumbnail}
                        <h2>" . get_the_title() . "</h2>
                        <p>{$vacancy_text}</p>
                        </li>";

			$count++;
		}
	}
	if (!$count) {
		$output .= '<li>There are currently no vacancies to be displayed.</li>';
	}
	wp_reset_postdata();
	$output .= '</ul>';
	return $output;
}
