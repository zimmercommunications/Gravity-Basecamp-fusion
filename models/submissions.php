<?php
//the submissions model will be used to interact with the WordPress CMS to get and write data.

class Submissions{
    public function __construct(){

    }
    //Function to retrieve the data from the most recent form entry
    public function get_entry($form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = 1){
        //Get an object of all of the most recent entries' data
        $entries = GFAPI::get_entry_ids($form_ids, $search_criteria, $sorting, $paging, $total_count);
        //Pull the ID from the first entry in the $entry object
        $entry_id = GFAPI::get_entry($entries[0]); 
        return $entry_id;       
    //Return entry's fields   
    

    }
    public function get_forms($active = true, $trash = false, $sort_column = 'id', $sort_dir = 'ASC'){
        //Returns array of Form objects. 
        $forms_arr = GFAPI::get_forms($active, $trash, $sort_column, $sort_dir);
        $ids = [];
        if($forms_arr){
            foreach($forms_arr as $form){                
                $ids[rgar($form, 'title')] = rgar($form, 'id');
            }
            return $ids;
        }else{
            echo '<div class="notice notice-error"><p>get_fields failed due to a failure to retrieve form ids array.</p></div>';        
        }
    }
    public function get_forms_v2($active = true, $trash = false, $sort_column = 'id', $sort_dir = 'ASC'){
        //Returns array of Form objects. 
        $forms_arr = GFAPI::get_forms($active, $trash, $sort_column, $sort_dir);
        $ids = [];
        if($forms_arr){
            $i = 0;
            foreach($forms_arr as $form){
                $key = $form['id'];
                $value = $form['title'];
                $ids[$key] = $value;
                $i++;
            }
            return $ids;
        }else{
            echo '<div class="notice notice-error"><p>get_fields failed due to a failure to retrieve form ids array.</p></div>';        
        }
    }

    public function get_fields($form_id){
        $form_obj = GFAPI::get_form($form_id);
        if($form_obj){
                $field_types = array('card', 'checkbox', 'checkbox_and_select', 'custom_field', 'dynamic_field_map', 'field_map', 'field_select', 'generic_map', 'hidden', 'post_select', 'radio_button', 'save_button', 'select', 'select_custom', 'simple_condition', 'text', 'textarea');
            $fields = GFAPI::get_fields_by_type($form_obj, $field_types, true);
            $fields_arr = [];
            if($fields){
                foreach($fields as $field){
                    $fields_arr[$field->label] = $field->id;
                }
                return $fields_arr;
            }else{
                echo '<div class="notice notice-error"><p>Form does not have fields. Add fields or pick a form that has fields.</p></div>';
            }
        }else{
            echo '<div class="notice notice-error"><p>get_fields failed due to a failure to retrieve the form object.</p></div>';
        }
        
    }

    public function get_fields_v2($form_id){
        $form_obj = GFAPI::get_form($form_id);
        if($form_obj){
            $fields = $form_obj['fields'];
            return $fields;
        }
        else{
            echo '<div class="notice notice-error"><p>get_fields failed due to a failure to retrieve the form object.</p></div>';
        }
    }
   
}