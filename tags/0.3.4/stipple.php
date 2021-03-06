<?php
  /*
    Plugin Name: Stipple
    Plugin URI: http://stippleit.com/s/wordpress
    Description: Stipple is the fastest way to label your pictures.
    Version: 0.3.4
    Author: Stipple Team - stippletech@stippleit.com
    Author URI: http://stippleit.com
    License: GPL2
  */

  /*
    Copyright 2010-2011  Stipple  (email : stippletech@stippleit.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */

  include 'stipple_config_html_page.php';

  register_deactivation_hook(__FILE__, 'stipple_deactivate' );
  add_action('wp_print_scripts', 'stipple_enable');

  /* admin settings setup */
  if ( is_admin() ) {
    add_action('admin_menu', 'stipple_admin_menu');
    add_action('admin_init', 'stipple_register_options');
  }

  function stipple_deactivate() {
    delete_option('stipple_options');
  }

  function stipple_enable() {
    $plugin_path = WP_PLUGIN_URL . '/' .
      str_replace(basename(__FILE__), "", plugin_basename(__FILE__));

    wp_enqueue_script('stipple', 'http://stippleit.com/stipple.js', false, false, true);

    $opts = get_option('stipple_options');

    if($opts['custom_stipple_load']) {
      wp_enqueue_script('stipple_load_full',
        $plugin_path . 'js/full_loader.js',
        Array('stipple'), false, true);

      wp_localize_script('stipple_load_full', 'STIPPLE_SETTINGS', array(
        'custom_loader' => $opts['custom_stipple_load_data']));
    } else if($opts['site_id']) {
      wp_enqueue_script('stipple_load_simple',
        $plugin_path . 'js/simple_loader.js',
        Array('stipple'), false, true);

      wp_localize_script('stipple_load_simple', 'STIPPLE_SETTINGS', array(
        'site_id' => $opts['site_id']));
    }
  }

  function stipple_admin_menu() {
    add_options_page('Stipple Configuration', 'Stipple', 'administrator',
    'stipple', 'stipple_config_html_page');
  }

  function stipple_register_options() {
    register_setting('stipple-options', 'stipple_options', 'stipple_validate');
  }

  function stipple_validate($v) {
    $v['site_id'] =  wp_filter_nohtml_kses($v['site_id']);
    switch($v['custom_stipple_load']) {
      case 0:
      case 1:
      case 2:
        break;
      default:
        $v['custom_stipple_load'] = 0;
    }

    return $v;
  }

?>
