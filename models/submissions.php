<?php
//the submissions model will be used to interact with the WordPress CMS to get and write data.

namespace GBF;

require_once('././plugins/gravityforms/includes/api.php');

class Submissions{
    private function __construct(){

    }
    //Function 
    public function get_entry($form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        $entries = GFAPI::get_entries($form_ids, $search_criteria , $sorting, $paging, $total_count);
        $entry_id = GFAPI::get_entry( $entries[0]['id'] );
        
        //Return entry's fields
        
    }

}