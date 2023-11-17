<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}




/**
 * Generates the default search form layout and displays a search form for retrieving student results using the default search form template.
 *
 * This function is responsible for generating and displaying the default search form layout for retrieving student results. It takes
 * various parameters, including a student data object, template details, and settings. If template details are provided, it returns
 * the default template information. If a student data object is provided, it processes the form data, displays the search form
 * layout with input fields and controls, and handles form submission for searching student results or generating certificates. The
 * function also handles reCAPTCHA integration if enabled. The generated layout is based on the default search form template and can
 * be customized using the provided template information and settings.
 *
 * @since 1.4.2
 * @last-update 1.4.2
 * 
 * @param object|null $print The student data object containing details like marks, subjects, and remarks. Not used in this function.
 * @param bool $template_details Optional. If true, returns the default template information. Default is false.
 * @param array $settings Optional. An array of settings for controlling the behavior of the search form. Default is false.
 * @return array|null The default template information if $template_details is true, otherwise null.
 */
function educare_default_search_form($print = null, $template_details = false, $settings = false) {
  // Define customizable fields (default value).
  $fields = array(
    'results_button' => [                     // Fields name
      'title' => 'Results Button',            // Fields title
      'subtitle' => 'Results button title',   // Messages || guide for this fields
      'value' => 'View Results'               // Fields value
    ],
    'certificate_button' => [
      'title' => 'Certificate Button',
      'subtitle' => 'Certificate button title',
      'value' => 'Get Certificate'
    ]
  );

  // Get cutomized fields
  $fields = educare_get_template_settings($fields, __FUNCTION__);

  // Set template details (title or thumbnail)
	if ($template_details) {
    $requred_fields = array(
      // 'Roll_No',
      // 'Regi_No',
      // 'Class',
      // 'Exam',
      // 'Year',
    );

		$template_info = array(
			'title' => 'Default Search Form',
			'thumbnail' => EDUCARE_URL . 'assets/img/default-search-form.jpg', // for default use EDUCARE_TEMPLATE_THUMBNAIL
      'fields' => $fields,                                // Default text value
      // 'requred_fields' => $requred_fields,             // Set custom fields
			'control_data' => false,                            // Ingnore results template
			'control_msgs' => true,                             // To show custome error||succse mesgs
		);
		// return info
		return $template_info;
	} else {
		// Show spinner when ajax request
		echo '<div id="educare-loading"><div class="educare-spinner"></div></div>';
		// Use id="educareForm" for Ajax request.
		$banner = educare_check_status('banner');
		
		?>
		<div class="result_body">
			<div class="results-container">
				<?php 
				if (educare_check_status('show_banner') == 'checked') {
					?>
					<div class="fixbd-flex banner">
						<div>
							<img src="<?php echo esc_url($banner->logo1)?>">
						</div>
						<div class="title">
							<h1><?php echo esc_html($banner->title)?></h1>
							<p class="sub-title"><?php echo esc_html($banner->subtitle1)?></p>
							<p class="sub-title"><?php echo esc_html($banner->subtitle2)?></p>
						</div>
						<div>
							<img src="<?php echo esc_url($banner->logo2)?>">
						</div>
					</div>
					<?php

					if (isset($print['msgs'])) {
						$status = 'error ';
						if ($print['status']) {
							$status = 'success ';
						}
						
						echo '<div class="notice notice-'.esc_attr($status).' is-dismissible"><p>'.wp_kses_post( $print['msgs'] ).'</p></div>';
					}

				}

				echo '<form class="educare-form box content bg-light educare-search-form" method="post" id="educareForm">';
					// Check reCAPTCHA status
					$re_captcha = educare_check_status('re_captcha');
					// Check requred fields data
					$requred = educare_check_status('display');
					// Getting all requered field key and title
					$requred_title = educare_requred_data($requred, true);
					// Remove name field from $requred_title. because, we don't need to name field to find/search the results
					unset($requred_title['Name']);
					unset($requred_title['Group']);
					
					foreach ($requred_title as $key => $title) {
						// Define default value for handle php error
						$value = '';
						// Getting fields value
						if (isset($_POST[$key])) {
							$value  = sanitize_text_field( $_POST[$key] );
						}

						echo '<div class="row">';
						echo '<div class="col-25">';
						echo '<label for="'.esc_attr($key).'">'.esc_html($title).'</label>';
						echo '</div>';
						echo '<div class="col-75">';

							// for input fields
							if ($key == 'Roll_No' || $key == 'Regi_No' or $key == 'user_pin') {
								$type = 'number';

								if ($key == 'user_pin') {
									$type = 'password';
								}

								echo '<input type="'.esc_attr($type).'" id="'.esc_attr($key).'" name="'.esc_attr($key).'" value="'.esc_attr($value).'" placeholder="'.esc_attr($title).'">';
							} else {
								// for select fields
								echo '<select id="'.esc_attr($key).'" name="'.esc_attr($key).'">';
								educare_get_option($key);
								echo '</select>';
							}
							
							// close div
						echo '</div>';
						echo '</div>';
					}

          // Button and Recaptcha
					echo '<input type="hidden" name="educare_results">';
					echo '<div class="row">
						<div class="col-25"></div>
						<div class="col-75">';
              if ($re_captcha == 'checked') {
                $site_key = educare_check_status('site_key');
                
                if ( current_user_can( 'manage_options' ) and empty($site_key)) {
                  echo educare_show_msg('<p>The Google Recaptcha checkbox field is hidden. Please enter/paste your google recaptcha v2 site key at </p><p><a href="'.esc_url( admin_url() ).'/admin.php?page=educare-settings&menu=Security" target="_blank"><code>Educare > Settings > Security > Site Key</code></a></p><p>Notes: Only admin can view these messages</p>', false);
                }
    
                echo '<div class="g-recaptcha" data-sitekey="'.esc_attr($site_key).'"></div>';
              }

              if ($settings['results'] == 'true') {
                echo '<button id="educare_results" class="results_button button" name="educare_results" type="submit">'.esc_html($fields['results_button']['value']).'</button>';
              }
              if ($settings['certificate'] == 'true') {
                echo '<button id="educare_certificate" class="results_button button" name="educare_certificate" type="submit">'.esc_html($fields['certificate_button']['value']).'</button>';
              }
						echo '</div>
					</div>';

					// Close all section
				echo '</form>';
			echo '</div>';
		echo '</div>';
	}
}

// Apply or Install template
// Hook the function to the educare_search_form_template action
add_action( 'educare_search_form_template', 'educare_default_search_form' );



/**
 * function for modern search form
 *
 * @since 1.4.2
 * @last-update 1.4.2
 * 
 * @param object|array $print 							Students data
 * @param object|array $template_details 		Template details
 * @return mixed
 */

 function educare_modern_search_form($print = null, $template_details = false, $settings = false) {
  // Define customizable fields (default value).
  $fields = array(
    'welcome' => [                    // Fields name
      'title' => 'Welcome Text',      // Fields title
      'subtitle' => 'Welcome text',   // Messages || guide for this fields
      'value' => 'Welcome'            // Fields value
    ],
    'guideline' => [
      'title' => 'Guideline',
      'subtitle' => 'Show guidelines to users',
      'value' => 'Please enter student details and click on View Result button. If you want to find the certificate, click on the Certificate tab in the upper right corner ↗'
    ],
    'results_head' => [
      'title' => 'Results Menu Header',
      'subtitle' => 'Results forms header text',
      'value' => 'View Results'
    ],
    'results_menu_message' => [
      'title' => 'Messages For Results Menu',
      'subtitle' => 'Show messeges or guidline form results menu',
      'value' => ''
    ],
    'certificate_head' => [
      'title' => 'Certificate Menu Header',
      'subtitle' => 'Certificate forms header text',
      'value' => 'Get Certificate'
    ],
    'certificate_menu_message' => [
      'title' => 'Messages For Certificate Menu',
      'subtitle' => 'Show messeges or guidline form certificate menu',
      'value' => ''
    ],
    'results_tab' => [
      'title' => 'Results Tab Button',
      'subtitle' => 'Results tab button title',
      'value' => 'Results'
    ],
    'certificate_tab' => [
      'title' => 'Certificate Tab Button',
      'subtitle' => 'Certificate tab button title',
      'value' => 'Certificate'
    ],
    'results_button' => [
      'title' => 'Results Button',
      'subtitle' => 'Results button title',
      'value' => 'View Results'
    ],
    'certificate_button' => [
      'title' => 'Certificate Button',
      'subtitle' => 'Certificate button title',
      'value' => 'Get Certificate'
    ]
  );

  // Get cutomized fields
  $fields = educare_get_template_settings($fields, __FUNCTION__);

	// Set template information (title or thumbnail)
	if ($template_details) {
		$template_info = array(
			'title' => 'Modern Search Form',
			'thumbnail' => EDUCARE_URL . 'assets/img/modern-search-form.jpg', // for default use EDUCARE_TEMPLATE_THUMBNAIL
      'fields' => $fields,
			'control_data' => false,
			'control_msgs' => true,
		);
		// return info
		return $template_info;
	} else {
		?>

    <?php
    // Show spinner when ajax request
    echo '<div id="educare-loading"><div class="educare-spinner"></div></div>';
    // Use id="educareForm" for Ajax request.
    $banner = educare_check_status('banner');

    ?>
    <div class="result_body">
      <div class="results-container">
        <?php 
        if (educare_check_status('show_banner') == 'checked') {
          ?>
          <div class="fixbd-flex banner">
            <div>
              <img src="<?php echo esc_url($banner->logo1)?>">
            </div>
            <div class="title">
              <h1><?php echo esc_html($banner->title)?></h1>
              <p class="sub-title"><?php echo esc_html($banner->subtitle1)?></p>
              <p class="sub-title"><?php echo esc_html($banner->subtitle2)?></p>
            </div>
            <div>
              <img src="<?php echo esc_url($banner->logo2)?>">
            </div>
          </div>
          <?php

          if (isset($print['msgs'])) {
            $status = 'error ';
            if ($print['status']) {
              $status = 'success ';
            }
            
            echo '<div class="notice notice-'.esc_attr($status).' is-dismissible"><p>'.wp_kses_post( $print['msgs'] ).'</p></div>';
          }

        }
        ?>

        <div class="register">
          <div class="row">
            <div class="col-md-3 register-left h-100 custom-register-left">
              <img src="<?php echo esc_url($banner->logo2)?>" alt="logo" />
              <h3><?php echo esc_html($fields['welcome']['value'])?></h3>
              <p><?php echo esc_html($fields['guideline']['value'])?></p>
            </div>
            <div class="col-md-9 register-right custom-register-right">

              <ul class="nav nav-tabs nav-justified" id="myTab" role="tablist">
                <?php
                // keep active tab
                $active_result = $active_certificate = ''; 
                if ($settings['results'] === 'true' && isset($_POST['educare_results'])) {
                  $active_result = 'active show';
                } elseif ($settings['certificate'] === 'true' && isset($_POST['educare_certificate'])) {
                  $active_certificate = 'active show';
                } elseif ($settings['results'] === 'false' && $settings['certificate'] === 'true') {
                  $active_certificate = 'active show';
                }  else {
                  $active_result = 'active show';
                }

                if ($settings['results'] == 'true') {
                  ?>
                  <li class="nav-item">
                    <a class="nav-link <?php echo esc_attr($active_result);?>" id="educareResultsTab-tab" data-bs-toggle="tab" href="#educareResultsTab" role="tab" aria-controls="educareResultsTab" aria-selected="true"><?php echo esc_html($fields['results_tab']['value'])?></a>
                  </li>
                  <?php
                }

                if ($settings['certificate'] == 'true') {
                  ?>
                  <li class="nav-item">
                    <a class="nav-link <?php echo esc_attr($active_certificate);?>" id="educareCertificateTab-tab" data-bs-toggle="tab" href="#educareCertificateTab" role="tab" aria-controls="educareCertificateTab" aria-selected="false"><?php echo esc_html($fields['certificate_tab']['value'])?></a>
                  </li>
                  <?php
                }
                ?>
              </ul>

              <div class="tab-content" id="myTabContent">
              <?php
                if ($settings['results'] == 'true') {
                  ?>
                  <div class="tab-pane fade <?php echo esc_attr($active_result);?>" id="educareResultsTab" role="tabpanel" aria-labelledby="educareResultsTab-tab">
                    <div class="tab-menu-head">
                      <h3 class="register-heading mt-5"><?php echo esc_html($fields['results_head']['value'])?></h3>

                      <?php
                      if ($fields['results_menu_message']['value']) {
                        echo '<p class="tab-menu-message text-center results-menu-message">'.esc_html($fields['results_menu_message']['value']).'</p>';
                      }
                      ?>
                    </div>

                    <div class="register-form">
                      <?php
                        echo '<form class="educare-form box content bg-light educare-search-form" method="post" id="educareForm">';
                          // Check reCAPTCHA status
                          $re_captcha = educare_check_status('re_captcha');
                          // Check required fields data
                          $required = educare_check_status('display');
                          // Getting all required field key and title
                          $required_title = educare_requred_data($required, true);
                          // Remove name field from $required_title because we don't need to name field to find/search the results
                          unset($required_title['Name']);
                          unset($required_title['Group']);
                          
                          foreach ($required_title as $key => $title) {
                            // Define default value to handle php error
                            $value = '';
                            // Getting field value
                            if (isset($_POST[$key])) {
                              $value  = sanitize_text_field($_POST[$key]);
                            }

                            echo '<div class="row">';
                            echo '<div class="col-25">';
                            echo '<label for="'.esc_attr($key).'">'.esc_html($title).'</label>';
                            echo '</div>';
                            echo '<div class="col-75">';

                              // for input fields
                              if ($key == 'Roll_No' || $key == 'Regi_No' or $key == 'user_pin') {
                                $type = 'number';

                                if ($key == 'user_pin') {
                                  $type = 'password';
                                }

                                echo '<input type="'.esc_attr($type).'" id="'.esc_attr($key).'" name="'.esc_attr($key).'" value="'.esc_attr($value).'" placeholder="'.esc_attr($title).'">';
                              } else {
                                // for select fields
                                echo '<select id="'.esc_attr($key).'" name="'.esc_attr($key).'">';
                                educare_get_option($key);
                                echo '</select>';
                              }
                              
                              // close div
                            echo '</div>';
                            echo '</div>';
                          }

                          // Button and Recaptcha
                          echo '<div class="row">
                            <div class="col-25"></div>
                            <div class="col-75">';

                              if ($re_captcha == 'checked') {
                                $site_key = educare_check_status('site_key');
                                
                                if (current_user_can('manage_options') && empty($site_key)) {
                                  echo educare_show_msg('<p>The Google Recaptcha checkbox field is hidden. Please enter/paste your Google Recaptcha v2 site key at </p><p><a href="'.esc_url(admin_url()).'/admin.php?page=educare-settings&menu=Security" target="_blank"><code>Educare > Settings > Security > Site Key</code></a></p><p>Notes: Only admin can view these messages</p>', false);
                                }
    
                                echo '<div class="g-recaptcha" data-sitekey="'.esc_attr($site_key).'"></div>';
                              }

                              echo '<button id="educare_results" class="results_button button" name="educare_results" type="submit">'.esc_html($fields['results_button']['value']).'</button>';

                            echo '</div>
                          </div>';

                          // Close all section
                        echo '</form>';
                      ?>
                    </div>

                  </div>
                  <?php
                }

                if ($settings['certificate'] == 'true') {
                  ?>
                  <div class="tab-pane fade <?php echo esc_attr($active_certificate);?>" id="educareCertificateTab" role="tabpanel" aria-labelledby="educareCertificateTab-tab">
                    <div class="tab-menu-head">
                      <h3 class="register-heading mt-5"><?php echo esc_html($fields['certificate_head']['value'])?></h3>

                      <?php
                      if ($fields['certificate_menu_message']['value']) {
                        echo '<p class="tab-menu-message text-center certificate-menu-message">'.esc_html($fields['certificate_menu_message']['value']).'</p>';
                      }
                      ?>
                    </div>

                    <div class="register-form">
                    <?php
                        echo '<form class="educare-form box content bg-light educare-search-form" method="post" id="educareForm">';
                          // Check reCAPTCHA status
                          $re_captcha = educare_check_status('re_captcha');
                          // Check required fields data
                          $required = educare_check_status('display');
                          // Getting all required field key and title
                          $required_title = educare_requred_data($required, true);
                          // Remove name field from $required_title because we don't need to name field to find/search the results
                          unset($required_title['Name']);
                          unset($required_title['Group']);
                          
                          foreach ($required_title as $key => $title) {
                            // Define default value to handle php error
                            $value = '';
                            // Getting field value
                            if (isset($_POST[$key])) {
                              $value  = sanitize_text_field($_POST[$key]);
                            }

                            echo '<div class="row">';
                            echo '<div class="col-25">';
                            echo '<label for="'.esc_attr($key).'">'.esc_html($title).'</label>';
                            echo '</div>';
                            echo '<div class="col-75">';

                              // for input fields
                              if ($key == 'Roll_No' || $key == 'Regi_No' or $key == 'user_pin') {
                                $type = 'number';

                                if ($key == 'user_pin') {
                                  $type = 'password';
                                }

                                echo '<input type="'.esc_attr($type).'" id="'.esc_attr($key).'" name="'.esc_attr($key).'" value="'.esc_attr($value).'" placeholder="'.esc_attr($title).'">';
                              } else {
                                // for select fields
                                echo '<select id="'.esc_attr($key).'" name="'.esc_attr($key).'">';
                                educare_get_option($key);
                                echo '</select>';
                              }
                              
                              // close div
                            echo '</div>';
                            echo '</div>';
                          }

                          // Button and Recaptcha
                          echo '<div class="row">
                            <div class="col-25"></div>
                            <div class="col-75">';

                              if ($re_captcha == 'checked') {
                                $site_key = educare_check_status('site_key');
                                
                                if (current_user_can('manage_options') && empty($site_key)) {
                                  echo educare_show_msg('<p>The Google Recaptcha checkbox field is hidden. Please enter/paste your Google Recaptcha v2 site key at </p><p><a href="'.esc_url(admin_url()).'/admin.php?page=educare-settings&menu=Security" target="_blank"><code>Educare > Settings > Security > Site Key</code></a></p><p>Notes: Only admin can view these messages</p>', false);
                                }
    
                                echo '<div class="g-recaptcha" data-sitekey="'.esc_attr($site_key).'"></div>';
                              }
                              
                              echo '<button id="educare_certificate" class="results_button button" name="educare_certificate" type="submit">'.esc_html($fields['certificate_button']['value']).'</button>';

                            echo '</div>
                          </div>';

                          // Close all section
                        echo '</form>';
                      ?>
                    </div>

                  </div>
                  <?php
                }
                ?>

              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
    <?php
	}
}

// Apply or Install template
// Hook the function to the educare_search_form_template action
add_action( 'educare_search_form_template', 'educare_modern_search_form' );

?>