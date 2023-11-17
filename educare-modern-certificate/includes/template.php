<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Generates a modern certificate using the provided information and template settings.
 * 
 * @since 1.0.0
 * @last-update 1.0.0
 *
 * @param object|null $print          	The data to be printed on the certificate.
 * @param bool        $template_details Whether to return template information or render the certificate.
 * @param bool        $settings        	Whether to use custom settings for the template. this is for shortcode attr
 *
 * @return array|string Depending on the $template_details parameter, either template information or rendered certificate HTML.
 */
function educare_modern_certificate($print = null, $template_details = false, $sttings = false) {
	// Define customizable fields (default value).
  $fields = array(
    'header' => [														// Fields name
      'title' => 'Certificate Header',  		// Fields value
      'subtitle' => 'Header of the certificate. Aslo you can use embed variable to show specific data. (Supported embed var: {name}, {exam}, {year}, {gpa} and {dob}.)', 					// Messages || guide for this fields
			'value' => 'Certificate'							// Fields value
    ],
    'sub_header' => [
      'title' => 'Sub Header',
      'subtitle' => 'Sub header for certificate',
			'value' => 'of achivement'
		],
		'certify' => [
      'title' => 'Certify Text',
      'subtitle' => 'Sub header for certificate',
			'value' => 'this cetificate is proudly presented to'
    ],
		'name' => [
      'title' => 'Name',
      'subtitle' => 'Name of the students',
			'value' => '{name}'
    ],
		'details' => [
      'title' => 'Details',
      'subtitle' => 'More information about the students',
			'value' => 'Duly passed the {exam} Examination in the year of {year}, secured G.P.A {gpa} on a scale of 5.00. His/Her date of birth is {dob}'
    ],
		'bottom_left' => [
      'title' => 'Bottom Left',
      'subtitle' => 'Certificate bottom (left site) text',
			'value' => 'Completed on: {year}'
    ],
		'bottom_right' => [
      'title' => 'Buttom Right',
      'subtitle' => 'Certificate bottom (right site) text',
			'value' => 'Controller of Examinations'
    ],
  );

	// Get cutomized fields
  $fields = educare_get_template_settings($fields, __FUNCTION__, 'certificate_template');

	// Set template information (title or thumbnail)
	if ($template_details) {
		$template_info = array(
			'title' => 'Modern Certificate',
			'thumbnail' => dirname( plugin_dir_url( __FILE__ ) ).'/assets/img/preview.jpg', // Default thumbnail
			'fields' => $fields,
			'prepare_data' => true // Automatically prepare data for the result template
		);

		// Return template information
		return $template_info;
	} else {
		// Retrieve necessary data
		$banner = educare_check_status('banner');
		$details = $print->Details;
		$certificate_bg = educare_get_attachment(educare_check_status('custom_certificate_bg'), true);

		// Use a default background if not provided
		if (!$certificate_bg) {
			$certificate_bg = dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/cover.png';
		}
		
		// Extract Date of Birth
		if (property_exists($details, 'Date_of_Birth')) {
			$dob = $details->Date_of_Birth;
		} else {
			$dob = '';
		}

		// Define embed variables for text replacement
		$embed_vars = array(
			'{name}' => sanitize_text_field( $print->Name ),
			'{exam}' => sanitize_text_field( $print->Exam ),
			'{year}' => sanitize_text_field( $print->Year ),
			'{gpa}' => sanitize_text_field( $print->GPA ),
			'{dob}' => sanitize_text_field( $dob ),
		);

		// Render the certificate HTML
		?>
		<div class="educare-certificate modern-certificate">
			<div class="cert-container">
				<div id="cert-body">
					<div class="cert-border">
						<img src="<?php echo esc_url($certificate_bg);?>" class="cert-bg" alt="Certificate"/>
						
						<div class="cert-content">
							<h1 class="cert-header">
								<img src="<?php echo esc_url( $banner->logo1 )?>" alt="logo1">
								<?php echo strtr($fields['header']['value'], array_map('esc_html', $embed_vars));?>
								<img src="<?php echo esc_url( $banner->logo2 )?>" alt="logo2">
							</h1>

							<h3 class="exam-name">of achivement</h3>
							
							<div class="cert-info">
								<div>
									<p>Serial No: 12<?php echo esc_html( $print->id )?></p>
									<p>CSC: 049<?php echo esc_html( $print->id )?></p>
								</div>
								
								<div class="cert-right">
									<p>Roll No: <?php echo esc_html( $print->Roll_No )?></p>
									<p>Registration No: <?php echo esc_html( $print->Regi_No )?></p>
								</div>
							</div>

							<h3 class="certify"><?php echo strtr($fields['certify']['value'], array_map('esc_html', $embed_vars));?></h3>
							<h2 class="student-name"><?php echo strtr($fields['name']['value'], array_map('esc_html', $embed_vars));?></h2>
							

							<p class="details"><?php echo strtr($fields['details']['value'], array_map('esc_html', $embed_vars));?></p>

							<div class="cert-bottom">
								<div class="cert-flex">
									<div><?php echo strtr($fields['bottom_left']['value'], array_map('esc_html', $embed_vars));?></div>
									<div><?php echo strtr($fields['bottom_right']['value'], array_map('esc_html', $embed_vars));?></div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="no_print text-center">
			<button onClick="<?php echo esc_js('window.print()');?>" class="print_button"><i class="fa fa-print"></i> Print</button>
			<button id="educare-undo" class="undo-button" onClick="window.location.href = window.location.href;"><i class="fa fa-undo"></i> Undo</button>
		</div>
		<?php
	}
}

// Apply or Install template
// Hook the function to the educare_certificate_template action
add_action( 'educare_certificate_template', 'educare_modern_certificate' );
?>