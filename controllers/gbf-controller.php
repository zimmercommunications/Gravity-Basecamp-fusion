<?php

class GBF_Controller{
    
    function __construct(){
        //Roll out ACF Options Page & ACF Fields
        $acf_model = new GBF_ACF;

        $gf_model = new Submissions;

        $bc_model = new ZimmerCommunications\models\Basecamp;        

        //Configure Endpoint for Auth Token retrieval. Set the Endpoint to fire the 
        add_action('rest_api_init', function(){
            register_rest_route('gbf/v1', 'auth', array('methods' => 'GET', 'callback' => array($this, 'redirected')));
        });

        //Require GravityForms & ACF Extended to be installed 
        register_activation_hook( __FILE__ .'./gravity-basecamp-fusion.php', array($this, 'child_plugin_activate') );
        
        $acf_model::gen_options_page();
        $acf_model::gen_options_fields();

        //Checks if BaseCamp's API token has been fetched and has option fields set, if not, it'll run the fetch.
        if(!$_SESSION['has_token'] && get_field('client_id', 'bcl-options') && get_field('client_secret', 'bcl-options')){
            $this->fetch_token();
        }

        //Provides functions to be used by front-end user
        $targetForm = get_field('form_id','bcl-options');
        
        //Hook new submission to the get_submissions function
        add_action( 'gform_after_submission_'.$targetForm, array($this, 'get_submissions'), 10, 2 );
        add_filter('acf/load_field/name=form_id', array($this, 'update_form_field_choices'));
        add_filter('acf/load_field/name=form_field_id', array($this, 'update_form_fields_id_choices'));

    }

    //Scripts added
    public function gbf_scripts(){
        //AJAX Oauth2 call for Basecamp API access.
        //wp_enqueue_script('token-ajax', plugin_dir_url(__DIR__) .'assets/js/ajax.js', false);
    }
    //Function to get auth'd for BC API. Initial fetch to get auth token
    public function fetch_token(){        
        $bc_model = new ZimmerCommunications\models\Basecamp;
        $redirect  = get_site_url(null, '', 'https').'/wp-json/gbf/v1/auth';
        $cid = get_field('client_id', 'bcl-options');
        $cs = get_field('client_secret','bcl-options');
        
        $bc_model::get_token($redirect, $cid, $cs);
                
    }
    //Function to get auth'd for BC API. Fires to return auth token, and receive actual token. 
    public function redirected(){
        $bc_model = new ZimmerCommunications\models\Basecamp;
        $redirect  = get_site_url(null, '', 'https').'/wp-json/gbf/v1/auth';
        $cid = get_field('client_id', 'bcl-options');
        $cs = get_field('client_secret','bcl-options');
        $token = $bc_model::get_token($redirect, $cid, $cs);

        //Send Data to token-res.php to be manually copy-pasted into the field on the options page.
        header('Location: '.get_site_url(null, '', 'https').'/wp-content/plugins/gravity-basecamp-fusion/views/token-res.php?r='.$token);
    }

    //Function to update options page field
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
            echo '<div class="notice notice-error"><p>Failed to populate Form ID to Watch due to a get_forms not returning an array</p></div>';
        }    
        // return the field
        return $field;
    }
    //Function to update options page field
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
                echo '<div class="notice notice-error"><p>Failed to populate Form Field ID choices due to a failure to retrieve the fields.</p></div>';
            }            
            // return the field
            return $field;
        }else{
            echo '<div class="notice notice-error"><p>Failed to populate Form Field ID choices due to a failure to retrieve the target form id.</p></div>';
            return $field;
        }
    }

    //Function to make sure plugins are installed
    public function child_plugin_activate(){
        // Require parent plugin
        if ( ! is_plugin_active( 'plugins/gravityforms/gravityforms.php' ) && current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error 
            wp_die('Sorry, but this plugin requires Gravity Forms to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
        if ( ! is_plugin_active( 'plugins/acf-extended/acf-extended.php' ) && current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error 
            wp_die('Sorry, but this plugin requires ACF Extended to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }

    //Function to grab new submission's data and send data to Base Camp
    public function get_submissions($form_ids = '', $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        $bc_model = new ZimmerCommunications\models\Basecamp;
        $gf_model = new Submissions;

        $form_id = get_field('form_id', 'bcl-options');
        $entry_obj = $gf_model::get_entry($form_ids, $search_criteria, $sorting, $paging, $total_count);

        print_r($entry_obj);        

        //Retrieve Options Menu Values from ACF options page
        $endpoint_url = get_field('endpoint_url', 'bcl-options');
        $data = [];
        //$field_choices = get_field_object('field_630e72abaf029')['choices'];

        //Data retrieval from ACF 
        if(have_rows('fields_to_send', 'bcl-options')):
            while(have_rows('fields_to_send', 'bcl-options')) : the_row();
                $value = get_sub_field('form_field_id');
                $key = get_sub_field('map_to');
            endwhile;
        endif;
            
  
        //Prepare Data for BaseCamp        

        //Send data to BaseCamp

    }
}






