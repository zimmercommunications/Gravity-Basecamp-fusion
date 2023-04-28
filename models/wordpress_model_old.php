<?php

class WordPress{
    public function __construct(){
        session_start();
        $_SESSION['WordPressTest'] = 'true';
        add_action('rest_api_init', function(){
            register_rest_route('gbf/v1', array(
                'methods' => 'GET',
                'callback' => array($this, 'handle_oauth_res')
            ));
        });
    }
    function handle_oauth_res(){
        $_SESSION['test'] = "hey mom";
        exit('handle_oauth_res ran');
        $provider = new Stevenmaguire\OAuth2\Client\Provider\Basecamp([
            'clientId'          => '0380a7df112fc726de678383571bf0605975e85d',
            'clientSecret'      => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',
            /* Redirect to this plugin's response handler */
            'redirectUri'       => 'https://dev.clear99.com/wp-json/gbf/v1',
        ]);

        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            setcookie('oauth2state', $provider->getState(), time()+60*60*24*30);
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            setcookie('oauth2state', '', time()+60*60*24*30);
            unset($_SESSION['oauth2state']);
            exit('Invalid state');

        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
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
}