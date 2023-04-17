<?php

class WordPress{
    public function __construct(){
        add_action( 'rest_api_init', array( $this, 'register_gbf_v1_endpoint' ));
        add_action( 'admin_menu', array($this, 'bcf_menu') );
        add_action( 'save_post', array($this, 'test'));
    }
    public function test(){
        setcookie('solient-green', $_COOKIE['solient-green']++, time()+60*60*24*30);

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
            $authUrl = $provider->getAuthorizationUrl();
            setcookie('oauth2state', $provider->getState(), time()+60*60*24*30);
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($authState) || ($authState !== $_COOKIE['oauth2state'])) {
            setcookie('oauth2state', '', time()+60*60*24*30);
            unset($_SESSION['oauth2state']);
            exit('Invalid state' . $_COOKIE['oauth2state']);

        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                // We got an access token, let's now get the user's details
                $user = $provider->getResourceOwner($token);

                // Use these details to create a new profile
                //printf('Hello %s!', $user->getId());

            } catch (Exception $e) {

                // Failed to get user details
                exit('Oh dear...');
            }

            // Use this to interact with an API on the users behalf
            $token->getToken();
            setcookie('basecamptoken', $token, time()+60*60*24*30);
            $_SESSION['basecamptoken'] = $token;
        }
    }

    public function bcf_menu() {
        add_options_page( 'BaseCamp Link', 'BaseCamp Link', 'manage_options', 'base-camp-link', array($this, 'my_plugin_options'));
    }
    public function my_plugin_options() {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        //wp_enqueue_script( 'authorizeScript', plugin_dir_url(__DIR__) . 'assets/js/ajax-oauth.js', array(), '', true);
        // button to get api token and save it to the browser's cookies
        echo '<a href="https://dev.clear99.com/wp-json/gbf/v1/auth"><button id="authorize">Authorize</button></a>';    
        
    }
}