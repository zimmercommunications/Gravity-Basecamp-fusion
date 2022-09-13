<?php
//Provides functions to be used by front-end user

class GBF_Controller{
    function __construct(){
        //Require GravityForms to be installed
        register_activation_hook( __FILE__ .'./gravity-basecamp-fusion.php', array($this, 'child_plugin_activate') );
        //Hook new submission to the get_submissions function
        add_action( 'gform_after_submission_'.$form_ID.'', 'get_submissions', 10, 2 );

    }   


    private function child_plugin_activate(){
        // Require parent plugin
        if ( ! is_plugin_active( 'plugins/gravityforms/gravityforms.php' ) and current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error
            wp_die('Sorry, but this plugin requires the Parent Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }

    public function get_submissions($form_ids, $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        //Retrieve Options Menu Values
        $targetForm = get_field('form_id','base-camp-link');

        
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






