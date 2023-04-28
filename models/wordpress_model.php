<?php

class WordPress{
    public function __construct(){
        
        //add_action( 'admin_menu', array($this, 'bcf_menu') ); Was actually a native ACF function so moved this functionality to acf_model.php
        add_action( 'rest_api_init', array( $this, 'register_gbf_v1_endpoint' ));
    }


    public function register_gbf_v1_endpoint() {
        register_rest_route(
          'gbf/v1',
          '/auth',
          array(
            'methods'  => 'GET',
            'callback' => array( $this, 'handle_oauth_res' ),
          )
        );
      }
    public function handle_oauth_res(WP_REST_Request $request){
        /* replaced $_GET['state'] referrences with the variable $authState */
        $authState = $request['state'];
        if($authState){
            setcookie('oauth2state', $authState, time()+60*60*24*30, '/');
            update_option('oauth2state', $authState, true);
        }
        /* replaced $_GET['code'] referrences with the variable $code */
        $code = $request['code'];

        $provider = new Stevenmaguire\OAuth2\Client\Provider\Basecamp([
            'clientId'          => '0380a7df112fc726de678383571bf0605975e85d',
            'clientSecret'      => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',
            /* Redirect to this plugin's response handler */
            'redirectUri'       => 'https://dev.clear99.com/wp-json/gbf/v1/auth',
        ]);

        if (!isset($code)) {

            // If we don't have an authorization code then get one
            //echo "If we don't have an authorization code then get one";
            $authUrl = $provider->getAuthorizationUrl();
            setcookie('oauth2state', $provider->getState(), time()+60*60*24*30, '/');
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        //echo "Check given state against previously stored one to mitigate CSRF attack";
        } elseif (empty($authState) || ($authState !== get_option('oauth2state'))) {
            setcookie('oauth2state', '', time()+60*60*24*30, '/');
            unset($_SESSION['oauth2state']);
            exit('Invalid state: ' .$authState. ' does not equal ' . $_COOKIE['oauth2state']);

        } else {
            // Try to get an access token (using the authorization code grant)
            //echo "";
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the user's details
                //echo "We got an access token, let's now get the user's details";
                $user = $provider->getResourceOwner($token);

                // Use these details to create a new profile
                //printf('Hello %s!', $user->getId());

            } catch (Exception $e) {

                // Failed to get user details
                exit('Oh dear...');
            }

            // Use this to interact with an API on the users behalf
            echo "Use this to interact with an API on the users behalf";
            $token->getToken();

            setcookie('basecamptoken', $token, time()+60*60*24*30, '/');
            update_option('basecamp_token', $token, true);
            $_SESSION['basecamptoken'] = $token;
            header('Location: '.get_site_url().'/wp-admin/options-general.php?page=base-camp-link');
        }
        header('Location: '.get_site_url().'/wp-admin/options-general.php?page=base-camp-link');
    }


    public function my_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        // button to get api token and save it to the browser's cookies
        echo '<a href="https://dev.clear99.com/wp-json/gbf/v1/auth"><button id="authorize">Authorize</button></a>';

        //echo do_shortcode('[acfe_form name="bcf-form"]');
        if($_COOKIE['basecamptoken']){
            echo '<h2>BaseCamp API Token Recieved.</h2>';
        }
    }
}