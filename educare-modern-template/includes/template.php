<?php
/**
 * function for modern results card
 * 
 * This function adds a custom field to the results card and this functinality
 * How to customize educare results card?
 * For more info about custom results card (PREMIUM):
 * @link FixBD: https://fixbd.net/docs/educare/How-to-customize-educare-results-card
 * @see Plugin Dir: educare/includes/support/educare-custom-results-card.php
 *
 * @since 1.0.0
 * @last-update 1.0.0
 * 
 * @param object|array $print 							Students data
 * @param object|array $template_details 		Template details
 * @param bool        $settings        			Whether to use custom settings for the template. this is for shortcode attr
 * @return mixed
 */
function educare_modern_template($print = null, $template_details = false, $sttings = false) {
	// Set template information (title or thumbnail)
	if (!$print && $template_details) {
    // Define all terms/fields
    $fields = array(
      "term1" => array(
				'title' => 'CA 1',
				'subtitle' => '10%',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'off',
				'hide' => 'off'
      ),
      "term2" => array(
        'title' => 'CA 2',
				'subtitle' => '10%',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'off',
				'hide' => 'off'
      ),
      "term3" => array(
        'title' => 'CA 3',
				'subtitle' => '10%',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'off',
				'hide' => 'off'
      ),
			"marks" => array(
        'title' => 'Marks',
				'subtitle' => '70%',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'off',
				'hide' => 'off'
      ),
      "combine" => array(
        'title' => 'Combine',
				'subtitle' => '100%',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'off'
      ),
			"class_average" => array(
        'title' => 'Class',
				'subtitle' => 'Average',
				'status' => 'checked',
				'default_value' => '80',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'on'
      ),
			"highest_in_class" => array(
        'title' => 'Highest',
				'subtitle' => 'In Class',
				'status' => 'checked',
				'default_value' => '90',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'off'
      ),
			"lowest_in_class" => array(
        'title' => 'Lowest',
				'subtitle' => 'In Class',
				'status' => 'checked',
				'default_value' => '40',
				'placeholder' => '00',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'off'
      ),
			"position" => array(
        'title' => 'Position',
				'subtitle' => 'Out of ',
				'status' => 'checked',
				'default_value' => '1',
				'placeholder' => '1',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'off'
      ),
			'gpa' => array(
				'title' => 'GPA',
				'subtitle' => 'Number Points',
				'status' => 'checked',
				'default_value' => '5',
				'placeholder' => '5',
				'type' => 'number',
				'auto' => 'on',
				'hide' => 'off'
			),
			'grade' => array(
				'title' => 'Grade',
				'subtitle' => 'Letter Grade',
				'status' => 'checked',
				'default_value' => 'A+',
				'placeholder' => 'A+',
				'type' => 'text',
				'auto' => 'on',
				'hide' => 'off'
			),
			// for select field
			// "term5" => array(
			// 	"type" => "select",
			// 	"value" => ['Value1', 'Value2', 'Value3'],
			// 	"title" => "Term Title 5",
			// 	"subtitle" => "sub1",
			// 	'auto' => 'off',
			// 	"disabled" => false,
      // ),
    );

		$template_info = array(
			'title' => 'Modern Template',
			'thumbnail' => dirname( plugin_dir_url( __FILE__ ) ).'/assets/img/preview.png', // for default use EDUCARE_TEMPLATE_THUMBNAIL
      'fields' => $fields,
			'import' => true,
		);

		// return info
		return $template_info;
	}

	$student_data = educare_get_modern_student_data($print);

	// For certificate template
	if ($print && $template_details) {
		return $student_data;
	}

	$details = $student_data->Details;
	$others = $student_data->Others;
	$quick_overview =$others->quick_overview;

	$info = educare_check_status('details');
	$banner = educare_check_status('banner');

	// Check requred fields data
	$requred = educare_check_status('display');
	// Getting all requered field key and title
	$requred_title = educare_requred_data($requred, true);
	
	$results_card = educare_check_status('results_card');
	// getting grade_sheet checked data
	$card_details = educare_checked_data($results_card->details, false);
	$card_details = json_decode(json_encode($card_details), true);
	
	?>
	<!-- Begin (Front-End) Results Body -->
	<div class="result_body">
		<div class="results-container">
			<?php if (educare_check_status('show_banner') == 'checked') {?>

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
			}

			if (educare_check_status('student_info') == 'checked') {
				$name_class = '';
				
				if ($info != 'checked') {
					$name_class = 'students_name';
				}

				// Admin can hide (students) name from result card
				if (key_exists('Name', $requred_title)) {
					echo "<h2 class='".$name_class." ".esc_attr( $requred_title['Name'] )."'> ".esc_html($print->Name)."</h2>";
				}
				
				?>
				<div class="fixbd-flex student-photos">
					<?php
					if (educare_check_status('photos') == 'checked') {
						$Photos = educare_get_attachment($others->Photos);
						echo "<div class='img'><img src='".esc_url($Photos)."' alt='".esc_attr($print->Name)."' width='100%'/></div>";
					}
					?>
					
					<div class="student-details">
						<table style="display: block;">
							<?php
							$field = array (
								'Name',
								'Class',
								'Roll_No',
								'Regi_No',
								'Exam'
							);

							foreach ($field as $field_name) {
								if (key_exists($field_name, $requred_title)) {
									echo '
									<tr>
									<td>'.esc_html( $requred_title[$field_name] ).'</td>
									<td>:</td>
									<td>'.esc_html($print->$field_name).'</td>
									<tr>
									';
								}
							}
							?>
						</table>
					</div>
				</div>
				<?php
			}

			// Admin can hide students details from result card
			if ($info == 'checked') {
				// Admin can hide (students) name from result card
				if (key_exists('details', $card_details)) {
					$subtitle = '';
					
					if (key_exists('subtitle', $card_details['details'])) {
						$subtitle = $card_details['details']['subtitle'];
						if ($subtitle) {
							$subtitle = '<small clss="subtitle">'.esc_html($subtitle).'</small>';
						}
					}
			
					echo '<h2 class="details">'. esc_html($card_details['details']['title']) . wp_kses_post( $subtitle ).'</h2>';
				}
				?>

				<div class="table_body"><table class="result_details">
					<tr>
						<td>Name of Student</td>
						<td><?php echo esc_html($print->Name);?></td>
						<td>Admission No</td>
						<td><?php echo esc_html($print->Regi_No);?></td>
					</tr>

					<?php
					// Extra field
					if (educare_check_status('details') == 'checked') {
						if ('auto_positions_in_class' == 'auto_positions_in_class') {
							$details->Position_in_Class = $quick_overview->position_in_class;
						}
						
						$count = 1; // for add specific tags (div/tr/ul) in every 4 foreach loop
			
						foreach ($details as $key => $value) {
							// Ignore rattings data
							// if ($key == 'Rattings' || $key == 'Photos') {
							// 	continue;
							// }
							
							if ($count%2 == 1) {  
								echo "<tr>";
							}
								
							echo "<td>".esc_html(str_replace('_', ' ', $key))."</td><td>".esc_html($value)."</td>"; 
							
							if ($count%2 == 0) {
								echo "</tr>";
							}
						
							$count++;
						
						}
					}
					?>

					<tr>
						<td>Class</td>
						<td><?php echo esc_html($print->Class);?></td>
						<td>Year</td>
						<td><?php echo esc_html($print->Year);?></td>
					</tr>
					<?php
				echo '</table></div>';
			}
			
			// this function convert subject or rattings object||array to html table
			educare_get_modern_marks_terms($student_data);

			if (educare_check_status('quick_overview') == 'checked') {
				?>
				<div class="table-responsive">
					<div class="table_body educare_overview">
						<table class="grade_sheet">
							<thead>
								<tr>
									<th colspan="6" style="padding: 8px;">Quick Overview</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Nummber of Subject</td>
									<td><?php echo esc_html( $quick_overview->total_subject )?></td>
									<td>Total Obtainable Marks</td>
									<td><?php echo esc_html( $quick_overview->total_obtainable_marks )?></td>
									<td>Marks Obtainable</td>
									<td><?php echo esc_html( $quick_overview->marks_obtainable )?></td>
								</tr>

								<tr>
									<td>Average</td>
									<td><?php echo esc_html( $quick_overview->average )?>%</td>
									<td>Position in Class</td>
									<td>
										<?php
										echo esc_html( $quick_overview->position_in_class );
										
										echo ' Out Of ' . esc_html($quick_overview->out_of);
										?>
									</td>
									<td>Passed/Faield</td>
									<td><?php echo $student_data->Result;?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php
			}

			if (educare_check_status('remarks') == 'checked') {
				$remarks = array (
					'teacher' => [
						'comments' => '',
						'name' => '',
						'date' => '',
						'signature' => '',
					],
					'principal' => [
						'comments' => '',
						'name' => '',
						'date' => '',
						'signature' => '',
					]
				);

				$print_only_remarks = '';
				$remarks = json_decode(json_encode($remarks));

				if (isset($others->remarks)) {
					$remarks = $others->remarks;
				}

				if (educare_check_status('print_only_remarks') == 'checked') {
					$print_only_remarks = 'print_only_remarks';
				}
				
				?>
				<div class="table_body educare_remarks <?php echo esc_attr($print_only_remarks);?>">
					<div class="table-responsive">
						<table class="remarks">
							<thead>
								<tr>
									<th colspan="6" class="remarks_head">Remarks</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Master/Mistress Remarks:</td>
									<td colspan="5"><?php echo esc_html($remarks->teacher->comments);?></td>
								</tr>

								<tr>
									<td>Name:</td>
									<td><?php echo esc_html($remarks->teacher->name);?></td>
									<td class="remarks-sign">Signature:</td>
									<td class="remarks-sign-field">
										<?php
										$signature = educare_get_attachment($remarks->teacher->signature, true);
										
										if ($signature) {
											echo '<img src="'.esc_url($signature).'" alt="Teacher Sign" width="100%"/>';
										}
										?>
									</td>
									<td class="remarks-date">Date:</td>
									<td class="remarks-date-field"><?php echo esc_html($remarks->teacher->date);?></td>
								</tr>

								<tr>
									<td>Principal's Remarks:</td>
									<td colspan="5"><?php echo esc_html($remarks->principal->comments);?></td>
								</tr>

								<tr>
									<td>Name of Principal:</td>
									<td><?php echo esc_html($remarks->principal->name);?></td>
									<td class="remarks_sign">Signature:</td>
									<td>
										<?php
										$signature = educare_get_attachment($remarks->principal->signature, true);
										
										if ($signature) {
											echo '<img src="'.esc_url($signature).'" alt="Principal Sign" width="100%"/>';
										}
										?>
									</td>
									<td class="remarks_sign">Date:</td>
									<td class="remarks_sign"><?php echo esc_html($remarks->principal->date);?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php
			}
			?>

		</div>

		<div class="no_print">
			<button onClick="<?php echo esc_js('window.print()');?>" class="print_button"><i class="fa fa-print"></i> Print</button>
			<button id="educare-undo" class="undo-button" onClick="window.location.href = window.location.href;"><i class="fa fa-undo"></i> Search Again</button>
		</div>
		<?php
	// .result_body
	echo '</div>';
}

// Apply or Install template
// Hook the function to the educare_results_card_template card action
add_action( 'educare_results_card_template', 'educare_modern_template' );
?>