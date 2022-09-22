<?php
//the submissions model will be used to interact with the WordPress CMS to get and write data.

class Submissions{
    public function __construct(){

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
        foreach($forms_arr as $form){
            $ids[rgar($form, 'title')] = intval(rgar($form, 'id'));
        }
        return $ids;
    }

    public function get_fields($form_id){
        $form_obj = GFAPI::get_form($form_id);
        if($form_obj){
                $field_types = array('card', 'checkbox', 'checkbox_and_select', 'custom_field', 'dynamic_field_map', 'field_map', 'field_select', 'generic_map', 'hidden', 'post_select', 'radio_button', 'save_button', 'select', 'select_custom', 'simple_condition', 'text', 'textarea');
            $fields = GFAPI::get_fields_by_type($form_obj, $field_types);
            $fields_arr = [];
            foreach($fields as $field){
                $fields_arr[$field->label] = $field->id;
            }
            return $fields_arr;
        }else{
            echo '<div class="notice notice-error"><p>get_fields failed due to a failure to retrieve the form object.</p></div>';
        }
        
    }
   
}