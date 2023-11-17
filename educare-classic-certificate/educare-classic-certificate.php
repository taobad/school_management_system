<?php
/**
 * Plugin Name: Educare Classic Certificate
 * Plugin URI: https://fixbd.net/educare/add-ons/certificate/classic-certificate-template
 * Description: Introducing our Classic Certificate Template! Add a touch of timeless elegance to your certificates with this versatile and refined design.
 * Version: 1.0.0
 * Author: FixBD
 * Author URI: http://fixbd.net
 * License: Commercial License (Regular and Extended)
 * License URI: https://codecanyon.net/licenses/standard
 * Text Domain: educare-classic-certificate
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Check if the main plugin is active
if (!function_exists('educare_is_active')) {
  function educare_is_active() {
    // Load the necessary WordPress file
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');

    // Replace 'myplugin/myplugin.php' with the main plugin's plugin folder and main plugin file name
    if (is_plugin_active('educare/Educare.php')) {
      // Main plugin is active
      return true;
    } else {
      // Main plugin is not active
      return false;
    }
  }
}

// If main plugin is active
if (educare_is_active()) {
  require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');
  require_once(plugin_dir_path(__FILE__) . 'includes/template.php');

  if (!function_exists('educare_classic_certificate_action_links')) {

    /**
     * Adds custom action links to the plugin entry in the WordPress admin dashboard.
     *
     * This function is used to modify the action links displayed for the plugin in the
     * list of installed plugins in the WordPress admin dashboard. The action links provide
     * quick access to specific pages or actions related to the plugin.
     * 
     * @since 1.0.0
     * @last-update 1.0.0
     *
     * @param array $links An array of existing action links for the plugin.
     * @param string $file The main file of the current plugin.
     * @return array Modified array of action links.
     */
    function educare_classic_certificate_action_links($links, $file) {
      $plugin_basename = plugin_basename(__FILE__);

      $action_links = array(
        // 'link || lug' => 'titile',
        'settings&menu=Certificate_Template' => 'Settings',
      );

      foreach ($action_links as $url => $title) {
        if ($file == $plugin_basename) {
          $url = 'admin.php?page=educare-' . esc_attr($url);
          $in = '<a href="'. esc_url($url) .'">'. esc_html($title) .'</a>';

          // Add action link
          array_unshift($links, $in);
        }
      }

      return $links;
    }

    // add options after plugin activation
    add_filter('plugin_action_links', 'educare_classic_certificate_action_links', 10, 2);
  }
  
}
