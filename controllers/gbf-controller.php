<?php

class GBF_Controller{
    
    function __construct(){
        //Require GravityForms to be installed 
        register_activation_hook( __FILE__ .'./gravity-basecamp-fusion.php', array($this, 'child_plugin_activate') );
        
        //Roll out ACF Options Page & ACF Fields
        $acf_model = new GBF_ACF;
        $gf_model = new Submissions;

        $acf_model::gen_options_page();
        $acf_model::gen_options_fields();

        //Provides functions to be used by front-end user
        $targetForm = get_field('form_id','base-camp-link');
        
        //Hook new submission to the get_submissions function
        add_action( 'gform_after_submission_'.$targetForm, 'get_submissions', 10, 2 );
        add_filter('acf/load_field/name=form_id', array($this, 'update_form_field_choices'));
        add_filter('acf/load_field/name=form_field_id', array($this, 'update_form_fields_id_choices'));
    }


    
    public function update_form_field_choices($field){ 
        // reset choices
        $field['choices'] = array();
        $submissions = new Submissions;
        $choices = $submissions::get_forms();
        $choices_keys = array_keys($choices);
        // loop through array and add to field 'choices'
        if( is_array($choices) ) {
            foreach( $choices as $choice ) {
                $field['choices'][$choice] = $choice;
            }
        }else{
            echo '<div class="notice notice-error"><p>update_form_fields_id_choices failed due to a get_forms not returning an array</p></div>';
        }    
        // return the field
        return $field;
    }

    public function update_form_fields_id_choices( $field ) {
        $gf_model = new Submissions;
        
        // reset choices
        $field['choices'] = array();  
        //Grab Form ID from options page field
        $targetForm = get_field('form_id', 'bcl-options');
        if($targetForm){
            
            $choices = $gf_model::get_fields($targetForm);            
            // loop through array and add to field 'choices'
            if( $choices ) {      
                  
                foreach( $choices as $choice ) {  
                    $key = array_keys($choices, $choice);                                  
                    $field['choices'][ $choice ] = $key;                
                }            
            }else{
                echo '<div class="notice notice-error"><p>update_form_fields_id_choices failed due to a failure to retrieve the fields.</p></div>';
            }            
            // return the field
            return $field;
        }else{
            echo '<div class="notice notice-error"><p>update_form_fields_id_choices failed due to a failure to retrieve the target form id.</p></div>';
            return $field;
        }
    }
    




    public function child_plugin_activate(){
        // Require parent plugin
        if ( ! is_plugin_active( 'plugins/gravityforms/gravityforms.php' ) && current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error 
            wp_die('Sorry, but this plugin requires the Parent Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }

    public function get_submissions($form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        //Retrieve Options Menu Values
        
        
        // Ask the GF api for it's data and store it in a variable to hand off to the basecamp model
        $gravityModel = new Submissions;
        $sorting = array(
            'key' => 'date_created',
            'direction' => 'DEC',
            'is_numeric' => false
        );
        $submission = $gravityModel->get_entry($targetForm, array(), $sorting, null, 1);
        //Prepare Data for BaseCamp        

        //Send data to BaseCamp
        $basecampModel = New Basecamp;
    }
}






