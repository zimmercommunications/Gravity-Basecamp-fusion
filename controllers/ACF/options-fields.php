<?php
			if( function_exists('acf_add_local_field_group') ):
                if(class_exists('Submissions')){

                }else{
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
                                'key' => 'field_630e7393af02b',
                                'label' => 'Form ID to Watch',
                                'name' => 'form_id',
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
                            ),
                            array(
                                'key' => 'field_630e72abaf029',
                                'label' => 'Fields to Send',
                                'name' => 'fields_to_send',
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
                                'button_label' => '',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_630e7f678a294',
                                        'label' => 'Form Field ID',
                                        'name' => 'form_field_id',
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
                                        'key' => 'field_630e7f7b8a295',
                                        'label' => 'Map To',
                                        'name' => 'map_to',
                                        'type' => 'select',
                                        'instructions' => 'assignee
                                                    content
                                                    description
                                                    content
                                                    due_on',
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
                                    ),
                                ),
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
            }
                
                endif;		