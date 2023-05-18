<?php

class GBF_Controller{
    
    function __construct(){
        //Roll out ACF Options Page & ACF Fields
        $acf_model = new GBF_ACF;
        $gf_model = new Submissions;
        $wp_model = new WordPress;
        $bc_model = new Basecamp;

        //Check if either token has expired, or if no token exists, check if it has an auth state to retrive a new token. TO-DO Something is broken with this and causes a loop with the auth server.
        $token = get_option('basecamp_token');
        $auth_state = $_COOKIE['auth2state'];
        if( $token == 0 || !$token || !$auth_state){
        //    $bc_model::get_token_v2();
        }
        //Configure Endpoint for Auth Token retrieval. Set the Endpoint to fire the 
        // add_action('rest_api_init', function(){
        //     register_rest_route('gbf/v1', 'auth', array('methods' => 'GET', 'callback' => array($this, 'redirected')));
        // });

        //Require GravityForms to be installed 
        register_activation_hook( __FILE__ .'./gravity-basecamp-fusion.php', array($this, 'child_plugin_activate') );
        
        $acf_model::gen_options_page();
        $acf_model::gen_options_fields();

        // Customize the url setting to fix incorrect asset URLs.
        add_filter('acf/settings/url', array($this, 'my_acf_settings_url'));        

        add_filter('acf/load_field/name=form_id', array($this, 'update_form_field_choices'), 10);
        add_filter('acf/load_field/name=form_field_id', array($this, 'update_form_fields_id_choices'), 9); //Using the below filter to targe the parent field first.
        //add_filter('acf/load_field/name=fields_to_send', array($this, 'update_form_fields_id_choices'), 9); 

        //Provides functions to be used by front-end user
        //$targetForm = get_field('form_id','option');
        $targetForm = get_field('form_id', 'options');
        //$targetForm = 1;
        //Hook new submission to the get_submissions function
        add_action( 'gform_after_submission_'.$targetForm, array($this, 'get_submissions'), 10, 2 );
    }


    public function my_acf_settings_url( $url ) {
        return MY_ACF_URL;
    }


    //Function to update options page field (Form ID to Watch)
    public function update_form_field_choices( $field ){
        // reset choices
        $field['choices'] = array();
        $submissions = new Submissions;
        //echo '<script type="text/javascript">console.log("Field IDs")</script>';  
        $choices = $submissions::get_forms_v2();          
        //echo '<script type="text/javascript">console.log('.json_encode($choices).')</script>';
        $choices_keys = array_keys($choices);
        //echo '<script type="text/javascript">console.log('.json_encode($choices_keys).')</script>';
        // loop through array and add to field 'choices'
        if( is_array($choices) ) {
            $i = 0;
            foreach( $choices as $choice ) {
                $key = $choice;
                $value = $choices_keys[$i];
                $field['choices'][$value] = $key;
                $i++;
            }
            // return the field
            return $field;
        }else{
            echo '<div class="notice notice-error"><p>Failed to populate Form ID to Watch due to a get_forms not returning an array</p></div>';
        }    

    }
    //Function to update options page field (Form Field ID)
    public function update_form_fields_id_choices( $field ) {
        $gf_model = new Submissions;
        
        // reset choices
        $field['choices'] = array();  
        //Grab Form ID from options page field
        $targetForm = get_field('form_id', 'options');
        //echo print_r($targetForm);
        if($targetForm){
            
            $choices = $gf_model::get_fields_v2($targetForm);
            echo '<script type="text/javascript">console.log("Fields")</script>';
            echo '<script type="text/javascript">console.log('.json_encode($choices).')</script>';


            // loop through array and add to field 'choices'
            if( $choices ) {      
                $i = 0;  
                foreach( $choices as $choice ) {  
                    $key = $choice['label'];
                    $value = $choice['id'];
                    $field['choices'][$value] = $key;
                    $i++;
                }
                return $field;
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

    //Function to make sure GravityForms is installed
    public function child_plugin_activate(){
        // Require parent plugin
        if ( ! is_plugin_active( 'plugins/gravityforms/gravityforms.php' ) && current_user_can( 'activate_plugins' ) ) {
            // Stop activation redirect and show error 
            wp_die('Sorry, but this plugin requires the Parent Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
        }
    }

    //Function to grab new submission's data and send data to Base Camp
    public function get_submissions($form_ids = '', $search_criteria = array(), $sorting = null, $paging = null, &$total_count = null){
        // if($_COOKIE['get_submissions']){
        //     setcookie('get_submissions', $_COOKIE['get_submissions']++, time()+3600, '/') ;
        // }
        // else{
        //     setcookie('get_submissions', 1, time()+3600, '/') ;
        // }
        
        $bc_model = New Basecamp;
        $gf_model = new Submissions;

        $form_ids = get_field('form_id', 'options');
        

        //? IDK what I'm testing for here.
        //echo '<pre>'.print_r(GFAPI::get_fields_by_type($form_obj, array('card', 'checkbox', 'checkbox_and_select', 'custom_field', 'dynamic_field_map', 'field_map', 'field_select', 'generic_map', 'hidden', 'post_select', 'radio_button', 'save_button', 'select', 'select_custom', 'simple_condition', 'text', 'textarea'), true)).'</pre>';        
        
        //Retrieve Options Menu Values from ACF options page
        $endpoint_url = get_field('endpoint_url', 'options');
        $data = [];
        //$field_choices = get_field_object('field_630e72abaf029')['choices'];

        //Data retrieval from ACF TODO: this may be broken..
        $postfields = [];
        if(have_rows('fields_to_send', 'options')):
            //Variable to hold the new form entry
            $entry_obj = $gf_model::get_entry($form_ids, $search_criteria, $sorting, $paging, $total_count);
            echo '<script type="text/javascript">console.log("Entry Obj")</script>'; 
            echo '<script type="text/javascript">console.log('.json_encode($entry_obj).')</script>'; 
            while(have_rows('fields_to_send', 'options')) : the_row();
                $value_key = get_sub_field('form_field_id'); // Eg. '14'
                $value = $entry_obj[$value_key]; //This is coming up NULL for some reason.
                $key = get_sub_field('map_to');  // Eg. 'content'              
                $postfields[$key] = $value;

            endwhile;
        endif;
        echo '<script type="text/javascript">console.log("Basecamp Data")</script>'; 
        echo '<script type="text/javascript">console.log('.json_encode($postfields).')</script>'; 
  
        //Prepare Data for BaseCamp by json_encoding it...       

        //Expected data form example:
        // '{
        //     "content": "API Test by Jesse",
        //     "description": "<h1>The modest, but handsome web developer</h1>",
        //     "due_on": "2023-05-01"
        // }'

        if(get_field('endpoint_url', 'options')){
            //Send data to BaseCamp via the basecamp class's method
            $bc_model::send_data(get_field('endpoint_url', 'options'), json_encode($postfields));

        }      



        

    }
}






	








