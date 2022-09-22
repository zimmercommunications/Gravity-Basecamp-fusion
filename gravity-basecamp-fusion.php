<?php
/*
Plugin Name:  GravityForms Basecamp Fusion
Description:  A WordPress plugin to optionally send GravityForms submissions to the BaseCamp API to turn into a pre-fromated job. 
Version:      1.001
Author:       Jesse Corkill 
Author URI:   https://github.com/reckypoo
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
define("PROJECT_ROOT_PATH", __DIR__ );
define( 'MY_ACF_PATH', plugin_dir_path(__FILE__) . 'plugins/acf' );
define( 'MY_ACF_URL', plugin_dir_url(__FILE__) . 'plugins/acf/' );

require_once PROJECT_ROOT_PATH . '/inc/bootstrap.php';


$GBF = new GBF_Controller;
//$GBF::update_field_choices();