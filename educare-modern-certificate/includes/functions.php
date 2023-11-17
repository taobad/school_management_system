<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Add custome sysle for this template
function educare_modern_certificate_style() {
  wp_enqueue_style('educare_modern_certificate_style', dirname( plugin_dir_url( __FILE__ ) ).'/assets/css/style.css', array('educare-results'), '1.0', 'all');
}

add_action('wp_enqueue_scripts', 'educare_modern_certificate_style');
?>