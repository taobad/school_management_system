<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/**
 * Educare profiles function
 *
 * @since 1.2.5
 * @last-update 1.2.5
 * @return void
 */
function educare_get_profiles() {
  // $current_user_id = $current_user->ID;
  $current_user = wp_get_current_user();
  $user_id = get_current_user_id();
  $educare_user_id = get_user_meta($user_id, 'user_id', true);

  $biography = get_the_author_meta('description', $user_id);

  if (!$biography) {
    $biography = 'No biographical information available.';
  }

  ob_start();

  if (!$educare_user_id && !current_user_can( 'manage_options' ) && !current_user_can( 'educare_admin' )) {
    ?>
    <section>
      <div class="container">
        <div class="card h-100">
          <div class="card-body">
            Sorry error to load user!
          </div>
        </div>
      </div>
    </section>
    <?php
    
    return;
  }

  // educare_show_student_profiles($educare_user_id, true);
  // $user_data = educare_get_users_data($educare_user_id);
  // $user_details = json_decode($user_data->Details);

  // echo '<pre>';
  // print_r($user_data);
  // echo '</pre>';

  // the_author_meta('description');
  // the_author_meta( 'first_name', $user );
  // the_author_meta( 'last_name', $user );
  // echo do_shortcode("[wpforms_display_user_entries]");

  if (current_user_can('educare_students')) {
    // Student profiles
    ?>
    <section>

      <?php
      $user_data = educare_get_users_data($educare_user_id);
      $user_details = json_decode($user_data->Details);
      $user_others = json_decode($user_data->Others);
      $student = new StudentResult($user_data);
      $results = $student->getStudents();
      ?>

      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_url(educare_get_attachment($user_others->Photos));?>" alt="<?php echo esc_attr($user_data->Name);?>" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($user_data->Name);?></h4>
                    <p class="text-secondary mb-1">Students Profiles</p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary">Edit Profiles</button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary">Logout</button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <?php
                  // Check requred fields data
                  $roles = 'students';
                  $requred = educare_check_status('display');
                  $requred_title = educare_requred_data($requred, true);
                  $requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
                  $requred_count = count($requred_title);
                  $i = 1;

                  foreach ($requred_title as $key => $value) {
                    if (property_exists($user_data, $key)) {
                      $val = sanitize_text_field( $user_data->$key );

                      if ($key == 'user_pin') {
                        $val = educare_decrypt_data($val);

                        if ($user_data->pin_status == 'valid') {
                          $val .= ' <small class="text-success">('.esc_html(ucfirst($user_data->pin_status)).')</small></span>';
                        } else {
                          $val .= ' <small class="text-danger">('.esc_html(ucfirst($user_data->pin_status)).')</small></span>';
                        }
                        
                      }

                      ?>
                      <div class="row">
                        <div class="col-sm-3">
                          <h6 class="mb-0"><?php echo esc_html( $value );?></h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                          <?php echo wp_kses_post($val);?>
                        </div>
                      </div>
                      <?php

                      if ($i < $requred_count) {
                        echo '<hr>';
                      }

                      $i++;
                    }
                  }
                  ?>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#Attendance">Attendance</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Results">Results</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Details">Details</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Subject">Subject</a></li>
          
          <?php
          if (function_exists('educare_get_analysis')) {
            echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#Analysis">Analysis</a></li>';
          }
          if (function_exists('educare_get_payment')) {
            echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Payment</a></li>';
          }
          ?>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">

          <div id="Attendance" class="container tab-pane active"><br>

            <?php
            $getStatus = educare_show_attendance($educare_user_id, true);
            
            $currentMonth = date('m'); // Get the current month as a two-digit number
            $currentYear = date('Y'); // Get the current year
            $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

            foreach ($getStatus as $key => $value) {
              $$key = ($value / $numberOfDays) * 100;
            }

            ?>

            <div class="row">
              <div class="col-md-6 p-2">
                <div class="bg-dark text-light p-5 h-100 rounded">
                  <h2>Attendance Details</h2><hr>
                  <small>Present: <?php echo esc_html($getStatus['present']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo esc_attr($present);?>%" aria-valuenow="<?php echo esc_attr($present);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                  <small>Late: <?php echo esc_html($getStatus['late']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo esc_attr($late);?>%" aria-valuenow="<?php echo esc_attr($late);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                  <small>Absent: <?php echo esc_html($getStatus['absent']);?></small>
                  <div class="progress mb-3" style="height:14px">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo esc_attr($absent);?>%" aria-valuenow="<?php echo esc_attr($absent);?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($numberOfDays);?>"></div>
                  </div>
                </div>
              </div>

              <div class="col-md-6 p-2">
                <div class="bg-light rounded">
                  <?php echo educare_show_attendance($educare_user_id);?>
                </div>
              </div>
            </div>
            
          </div>

          <div id="Results" class="container tab-pane fade"><br>
            <div class="row">
              <?php
              echo '<table class="table w-100">';
              echo '<thead class="bg-light">';
              echo '<tr>';
              echo '<th>No</th><th>Class</th><th>Exam</th><th>Year</th><th>View</th>';
              echo '</tr>';
              echo '</thead>';
              echo '</tbody>';
              if ( $results) {
                $no = 1;
                // Check requred fields data
                $roles = 'results';
                $requred = educare_check_status('display');
                $requred_title = educare_requred_data($requred, true);
                $requred_title = educare_roles_wise_filed(array('roles' => $roles, 'fields' => $requred_title));
                unset($requred_title['Name']);

                foreach ($results as  $result) {
                  echo '<tr>
                  <td>'.esc_html($no++).'</td>
                  <td>'.esc_html($result->Class).'</td>
                  <td>'.esc_html($result->Exam).'</td>
                  <td>'.esc_html($result->Year).'</td>
                  <td>';
                  echo '<form action="/'.esc_attr(educare_check_status("results_page")).'" method="post">';
                  $nonce = wp_create_nonce( 'educare_form_nonce' );
									echo '<input type="hidden" name="nonce" value="'.esc_attr($nonce).'">';
                  $valid = true;
                  
                  foreach ($requred_title as $field => $title) {
                    if (property_exists($result, $field)) {
                      $result_key = sanitize_text_field( $result->$field );

                      if ($field == 'user_pin') {
                        if ($user_data->pin_status == 'valid') {
                          $result_key = educare_decrypt_data($user_data->$field);
                        } else {
                          $result_key = '';
                          $valid = false;
                        }
                      }

                      echo '<input type="hidden" name="'.esc_attr($field).'" value="'.esc_attr($result_key).'">';
                    }
                  }

                  if ($valid) {
                    echo '<button id="results_btn" class="results_button button" name="educare_results" type="submit" formtarget="_blank">View Results</button>';
                  } else {
                    echo '<button id="results_btn" class="results_button bg-danger button" name="educare_results" type="submit" title="Expire Pin" formtarget="_blank" disabled>View Results</button>';
                  }
                  
                  echo'</form>
                  </td>
                  </tr>';
                }
              } else {
                echo '<tr><td colspan="5"><h3 class="p-5 text-center text-danger">Results not available or published yet.</h3></td></tr>';
              }
              echo '</tbody>';
              echo '</table>';
              ?>
            </div>
          </div>

          <div id="Details" class="container tab-pane fade"><br>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th colspan="4">Student Info</th>
                  </tr>
                </thead>
                <?php
                if ($user_data->Details) {
                  $count = 1; // for add specific tags (div/tr/ul) in every 4 foreach loop
                  $details = json_decode($user_data->Details);

                  foreach ($details as $key => $value) {
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
              </table>
            </div>
          </div>

          <div id="Subject" class="container tab-pane fade"><br>
            <div class="bg-dark text-light p-5 h-100 rounded">
              <h2>Subject List</h2><hr>
              
              <?php
              if ($user_data->Subject) {
                $subject = json_decode($user_data->Subject);
                $optional_sub = educare_check_status('optional_sybmbol');
                $optional_sybmbol = '';
                echo '<ol>';
                
                foreach ($subject as $sub => $optional) {
                  if ($optional->optional) {
                    $optional_sybmbol = $optional_sub;
                  }
                  echo '<li>'.esc_html($sub).' '.esc_html($optional_sybmbol).'</li>';
                }

                echo '</ol>';
              }
              ?>
            </div>
          </div>

          <?php
          if (function_exists('educare_get_analysis')) {
            ?>
            <div id="Analysis" class="container tab-pane fade"><br>
              <?php echo do_shortcode( '[educare_analysis_system]' );?>
            </div>
            <?php
          }
          ?>

          <?php
          if (function_exists('educare_get_payment')) {
            ?>
            <div id="payment" class="container tab-pane fade"><br>
              <?php echo do_shortcode( '[educare_payment_system]' );?>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </section>
    <?php
  } elseif (current_user_can('educare_teachers')) {
    // Teachers profiles
    // add_filter('show_admin_bar', '__return_false');

    $user_data = educare_get_users_data($educare_user_id, 'teachers');
    $user_details = json_decode($user_data->Details);
    $user_others = json_decode($user_data->Others);
    $user_subject = json_decode($user_data->Subject);
    ?>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_url(educare_get_attachment($user_others->Photos));?>" alt="Admin" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($user_data->Name);?></h4>
                    <p class="text-secondary mb-1">Teachers Profiles</p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="/admin"><button class="btn btn-primary">Dashboard</button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary">Logout</button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Full Name</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_data->Name);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Institute</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Institute);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Type</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Type);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Mobile No</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Mobile_No);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Email</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($user_details->Email);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-12">
                        <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary">Edit Profiles</button></a>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <div class="bg-dark text-light p-5 h-100 rounded">
              <h2>Subject List (can access)</h2><hr>
              
              <?php
              if ($user_subject) {
                foreach ($user_subject as $class => $sub) {
                  // echo '<h5>Class - '.esc_html($class).'</h5>';
                  if ($sub) {
                    echo '<ol>';
                    foreach ($sub as $subject) {
                      echo '<li>'.esc_html($subject).'</li>';
                    }
                    echo '</ol>';
                  }
                }
              }
              ?>
            </div>
          </div>
        </div>

      </div>
    </section>
    <?php
  } else {
    // Admin profiles
    ?>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                  <img src="<?php echo esc_attr(get_avatar_url($user_id));?>" alt="Admin" class="rounded-circle" width="150">
                  <div class="mt-3">
                    <h4><?php echo esc_html($current_user->display_name);?></h4>
                    <p class="text-secondary mb-1">Admin Profiles</p>
                    <p class="text-muted font-size-sm"><?php echo wp_kses_post( $biography );?></p>
                    <a href="/admin"><button class="btn btn-primary">Dashboard</button></a>
                    <a href="<?php echo wp_logout_url(); ?>"><button class="btn btn-outline-primary">Logout</button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Full Name</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->display_name);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Roles</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php
                        $user_roles = $current_user->roles;
                        if (!empty($user_roles)) {
                          $role_titles = array_map('ucwords', $user_roles); // Retrieves translated role titles if necessary
                          $roles_string = implode(', ', $role_titles);
                          echo $roles_string;
                        } else {
                          echo 'No roles found for the user.';
                        }
                        ?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Email</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->user_email);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Others info</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        Not Found
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-3">
                        <h6 class="mb-0">Registered</h6>
                      </div>
                      <div class="col-sm-9 text-secondary">
                        <?php echo esc_html($current_user->user_registered);?>
                      </div>
                  </div>
                  <hr>
                  <div class="row">
                      <div class="col-sm-12">
                        <a href="<?php bloginfo( 'url' ); ?>/edit-profiles"><button class="btn btn-primary">Edit Profiles</button></a>
                      </div>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
  }

  return ob_get_clean();

}

// Create shortcode for educare profile system
add_shortcode('educare_profiles', 'educare_get_profiles' );

?>