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
            'callback' => array( $this, 'handle_oauth_res_v2' ),
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
            setcookie('oauth2state', 0, time()+60*60*24*30, '/');
            update_option('oauth2state', 0);
            exit('Invalid state: ' .$authState. ' does not equal ' . $_COOKIE['oauth2state']);

        } else {
            // Try to get an access token (using the authorization code grant)
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
    public function handle_oauth_res_v2(WP_REST_Request $request){
        /* replaced $_GET['state'] referrences with the variable $authState */
        $authState = $request['state'];

        // if($authState){
        //     setcookie('oauth2state', $authState, time()+60*60*24*30, '/');
        //     update_option('oauth2state', $authState, true);
        // }
        /* replaced $_GET['code'] referrences with the variable $code */
        $code = $request['code'];

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => '0380a7df112fc726de678383571bf0605975e85d',    // The client ID assigned to you by the provider
            'clientSecret'            => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',    // The client password assigned to you by the provider
            'redirectUri'             => 'https://dev.clear99.com/wp-json/gbf/v1/auth',
            'urlAuthorize'            => 'https://launchpad.37signals.com/authorization/new?type=web_server',
            'urlAccessToken'          => 'https://launchpad.37signals.com/authorization/token?type=web_server',
            'urlResourceOwnerDetails' => 'https://launchpad.37signals.com/authorization.json'
        ]);

        //$existingAccessToken = get_option('basecamp_access')['token'];
        $existingAccessToken = get_option('basecamp_access');
        
        // If we don't have an authorization code then get one
        if (!$code) {
        
            // Fetch the authorization URL from the provider; this returns the
            // urlAuthorize option and generates and applies any necessary parameters
            // (e.g. state).
            $authorizationUrl = $provider->getAuthorizationUrl();
        
            // Get the state generated for you and store it to a cookie. 
            //$_SESSION['oauth2state'] = $provider->getState();
            setcookie('oauth2state', $provider->getState(), time() + 14 * 24 * 60 * 60, '/' );
    
            // Optional, only required when PKCE is enabled. 
            // Get the PKCE code generated for you and store it to the session.
            //$_SESSION['oauth2pkceCode'] = $provider->getPkceCode();
            // setcookie('oauth2pkceCode', $provider->getPkceCode(), time() + 14 * 24 * 60 * 60, '/' );
        
            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;
        
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif ((!$authState) || empty($_COOKIE['oauth2state']) || $authState !== $_COOKIE['oauth2state']){
            //If it already has a value stored...//
            if ($_COOKIE['oauth2state']) {
                //ChatGPT's comments: When refreshing the access token, the function clears the oauth2state cookie and option. This behavior is incorrect. Instead, only clear the access_token cookie and option.
                //Saved state did not match the auth server's, so clear the value to re-fetch a new state
                setcookie('oauth2state', 0, time()+60*60*24*30, '/');
                update_option('oauth2state', 0, true);
                //setcookie('basecamptoken', 0, time()+60*60*24*30, '/');
                //update_option('basecamptoken', 0, true);
            }
        
            exit('Invalid state. OAuth2 Token: ' . $_COOKIE['oauth2state'] . ' and response authState was ' . $authState);
        
        }
        //Check if we have already have an access token and check if it's exired.. TO-DO: create a class for the old tokens and give it methods to check if it's exired and to re-fresh the token.       
        elseif($existingAccessToken && $existingAccessToken->hasExpired()){
        // elseif($existingAccessToken){
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken()
            ]);
        
            // Purge old access token and store new access token to your data store.
            setcookie('oauth2state', 0, time()+60*60*24*30, '/');
            update_option('oauth2state', 0, true);
        } else {
        
            try {
            
                // Optional, only required when PKCE is enabled.
                // Restore the PKCE code stored in the session.
                //$provider->setPkceCode($_COOKIE['oauth2pkceCode']);
        
                // Try to get an access token using the authorization code grant.
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $code
                ]);
        
                // We have an access token, which we may use in authenticated
                // requests against the service provider's API.
                echo 'Access Token: ' . $accessToken->getToken() . "<br>";
                echo 'Refresh Token: ' . $accessToken->getRefreshToken() . "<br>";
                echo 'Expired in: ' . $accessToken->getExpires() . "<br>";
                echo 'Already expired? ' . ($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
        
                // Using the access token, we may look up details about the
                // resource owner.
                $resourceOwner = $provider->getResourceOwner($accessToken);
        
                var_export($resourceOwner->toArray());
        
                // The provider provides a way to get an authenticated API request for
                // the service, using the access token; it returns an object conforming
                // to Psr\Http\Message\RequestInterface.
                //To Do -- Not sure if the resource URL is correct below.. 
                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    'https://launchpad.37signals.com/authorization.json',
                    $accessToken
                );
        
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        
                // Failed to get the access token or user details.
                exit($e->getMessage());
        
            }
            // Use this to interact with an API on the users behalf
            echo "Use this to interact with an API on the users behalf";

            // $basecamp_access = [];
            // $basecamp_access['token'] = $accessToken->getToken();
            // $basecamp_access['refresh_token'] = $accessToken->getRefreshToken();
            // $basecamp_access['expires'] = $accessToken->getExpires();
            // $basecamp_access['has_expired'] = $accessToken->hasExpired();

            // setcookie('basecamptoken', $basecamp_access, time()+60*60*24*30, '/');
            // update_option('basecamp_token', $basecamp_access, true);
            // $_SESSION['basecamptoken'] = $basecamp_access;

            setcookie('basecamptoken', $accessToken, time()+60*60*24*30, '/');
            update_option('basecamp_token', $accessToken, true);

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