<?php
/*
Plugin Name:  GravityForms Basecamp Fusion
Description:  A WordPress plugin to optionally send GravityForms submissions to the BaseCamp API to turn into a pre-fromated job. 
Version:      1.006
Author:       Jesse Corkill 
Author URI:   https://github.com/reckypoo
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
session_start();

function option_exists($name, $site_wide=false){
    global $wpdb; return $wpdb->query("SELECT * FROM ". ($site_wide ? $wpdb->base_prefix : $wpdb->prefix). "options WHERE option_name ='$name' LIMIT 1");
}

if(!option_exists("basecamp_token")){
    add_option('basecamp_token', 0);
}
if(!option_exists("oauth2state")){
    add_option('oauth2state', 0);
}

define("PROJECT_ROOT_PATH", __DIR__ );
define( 'MY_ACF_PATH', plugin_dir_path(__FILE__) . 'plugins/acf' );
define( 'MY_ACF_URL', plugin_dir_url(__FILE__) . 'plugins/acf/' );
define('COMPOSER', __DIR__ . '/vendor');

require_once PROJECT_ROOT_PATH . '/inc/bootstrap.php';

$GBF = new GBF_Controller;
//$GBF::update_field_choices();