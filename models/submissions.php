<?php
//the submissions model will be used to interact with the WordPress CMS to get and write data.

class Submissions{
    private function __construct(){

    }
    //Function 
    public function get_entry($form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        $entry = GFAPI::get_entries($form_ids, $search_criteria, $sorting, $paging, $total_count);
        $entry_id = GFAPI::get_entry($entries[0]['id']); 
        return $entry_id;       
    //Return entry's fields   
    

    }
    public function get_forms($active = true, $trash = false, $sort_column = 'id', $sort_dir = 'ASC'){
        //Returns array of Form objects. 
        $forms_arr = GFAPI::get_forms($active, $trash, $sort_column, $sort_dir);
        $ids = [];
        foreach($form_arr as $form){
            array_push($ids, rgar($form, 'id'));
        }
        return $ids;

    }

    public function get_fields($form_id){
        $form_obj = GFAPI::get_form($form_id);
        $field_types = array('card', 'checkbox', 'checkbox_and_select', 'custom_field', 'dynamic_field_map', 'field_map', 'field_select', 'generic_map', 'hidden', 'post_select', 'radio_button', 'save_button', 'select', 'select_custom', 'simple_condition', 'text', 'textarea');
        $fields = GFAPI::get_fields_by_type($form_obj, $field_types);
        $fields_arr = [];
        foreach($fields as $field){
            array_push($fields_arr, $field->label);
        }
        return $fields_arr;
    }

}