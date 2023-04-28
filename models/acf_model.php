<?php

class GBF_ACF{
    public function __construct(){

        //add_filter('acf/load_field/name=form_id', array($this, 'acf_load_form_field_choices'));

        // When including the PRO plugin, hide the ACF Updates menu
        add_filter('acf/settings/show_updates', '__return_false', 100);        
    }

    public function gen_options_page() {        
        if( function_exists('acf_add_options_page') ) {    
            acf_add_options_page(array(
                'page_title'    => 'BaseCamp Link',
                'menu_title'    => 'BaseCamp Link',
                'menu_slug'     => 'base-camp-link',
                'capability'    => 'manage_options',
                'redirect'      => false
            ));
        }
        else{
            add_options_page( 'BaseCamp Link', 'BaseCamp Link', 'manage_options', 'base-camp-link', array($this, 'my_plugin_options'));
        }
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
                        'aria-label' => '',
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
                        'key' => 'field_630e7393af02b',
                        'label' => 'Form ID to Watch',
                        'name' => 'form_id',
                        'aria-label' => '',
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
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                        'allow_custom' => 0,
                        'search_placeholder' => '',
                    ),
                    array(
                        'key' => 'field_630e72abaf029',
                        'label' => 'Fields to Send',
                        'name' => 'fields_to_send',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
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
                        'button_label' => 'Add Row',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_630e7f678a294',
                                'label' => 'Form Field ID',
                                'name' => 'form_field_id',
                                'aria-label' => '',
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
                                        0 => 'Your Message',
                                    ),
                                    array(
                                        0 => '',
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
                                'allow_custom' => 0,
                                'search_placeholder' => '',
                                'parent_repeater' => 'field_630e72abaf029',
                            ),
                            array(
                                'key' => 'field_630e7f7b8a295',
                                'label' => 'Map To',
                                'name' => 'map_to',
                                'aria-label' => '',
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
                                'allow_custom' => 0,
                                'search_placeholder' => '',
                                'parent_repeater' => 'field_630e72abaf029',
                            ),
                        ),
                        'rows_per_page' => 20,
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
                'show_in_rest' => false,
                'acfe_display_title' => '',
                'acfe_autosync' => '',
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));
            
            endif;		
    }
    
}