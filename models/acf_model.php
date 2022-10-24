<?php

class GBF_ACF{
    public function __construct(){
        // Customize the url setting to fix incorrect asset URLs.
        add_filter('acf/settings/url', array($this, 'my_acf_settings_url'));
        //add_filter('acf/load_field/name=form_id', array($this, 'acf_load_form_field_choices'));
        
    }
    public function my_acf_settings_url( $url ) {
        return MY_ACF_URL;
    }
    public function gen_options_page(){
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
                'post_id' => 'bcl-options',
                'autoload' => false,
                'update_button' => 'Update',
                'updated_message' => 'Options Updated',
            ));
            
        endif;
    }
    public function gen_options_fields(){
        if( function_exists('acf_add_local_field_group') ):

            acf_add_local_field_group(array(
                'key' => 'group_630e71fee0e2f',
                'title' => 'BaseCamp Link',
                'fields' => array(
                    array(
                        'key' => 'field_630e7238af028',
                        'label' => 'Endpoint URL',
                        'name' => 'endpoint_url',
                        'type' => 'text',
                        'instructions' => 'https://3.basecampapi.com/$ACCOUNT_ID/buckets/$bucket_ID/todolists/$todolist_ID/',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_633eeba82b78c',
                        'label' => 'Client ID',
                        'name' => 'client_id',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_633eebb52b78d',
                        'label' => 'Client Secret',
                        'name' => 'client_secret',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_630e7393af02b',
                        'label' => 'Form ID to Watch',
                        'name' => 'form_id',
                        'type' => 'select',
                        'instructions' => 'You must submit this change before continuing.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 1,
                            2 => 2,
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_630e72abaf029',
                        'label' => 'Fields to Send',
                        'name' => 'fields_to_send',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'field_630e7393af02b',
                                    'operator' => '!=empty',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'acfe_repeater_stylised_button' => 0,
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_630e7f678a294',
                                'label' => 'Form Field ID',
                                'name' => 'form_field_id',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    array(
                                        0 => 'Field Label One',
                                    ),
                                    array(
                                        0 => 'Field Label Two',
                                    ),
                                    array(
                                        0 => 'Field Label Three',
                                    ),
                                ),
                                'default_value' => false,
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'multiple' => 0,
                                'allow_null' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'return_format' => 'value',
                            ),
                            array(
                                'key' => 'field_630e7f7b8a295',
                                'label' => 'Map To',
                                'name' => 'map_to',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'content' => 'Content',
                                    'description' => 'Description',
                                    'assignee_ids' => 'Assignee BaseCamp IDs',
                                    'completion_subscribers_ids' => 'Subscriber IDs',
                                    'notify' => 'Notify',
                                    'due_on' => 'Due On',
                                    'starts_on' => 'Starts On',
                                ),
                                'default_value' => false,
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'return_format' => 'value',
                                'ajax' => 0,
                                'placeholder' => '',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_633eea2f2b78b',
                        'label' => 'Refresh Token',
                        'name' => 'refresh_token',
                        'type' => 'acfe_button',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'button_value' => 'Refresh',
                        'button_type' => 'button',
                        'button_class' => 'button button-secondary',
                        'button_id' => '',
                        'button_before' => '',
                        'button_after' => '',
                        'button_ajax' => 1,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'base-camp-link',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
                'acfe_display_title' => '',
                'acfe_autosync' => '',
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));
            
            endif;		
    }
    
}