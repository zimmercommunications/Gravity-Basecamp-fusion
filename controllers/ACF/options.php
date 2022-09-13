<?php
if( function_exists('acf_add_options_page') ):

    acf_add_options_page(array(
        'page_title' => 'BaseCamp Link',
        'menu_slug' => 'base-camp-link',
        'menu_title' => 'BaseCamp Link',
        'capability' => 'manage_options',
        'position' => '',
        'parent_slug' => 'options-general.php',
        'icon_url' => '',
        'redirect' => true,
        'post_id' => 'options',
        'autoload' => false,
        'update_button' => 'Update',
        'updated_message' => 'Options Updated',
    ));
    
    endif;