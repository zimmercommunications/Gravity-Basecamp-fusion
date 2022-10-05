<?php
//The basecamp model is to be used to interact with the Basecamp API

class Basecamp{
    public function __construct(){

    }
    public function send_data($endpoint){

    }
    public function prep_data($data){

    }
    public function handle_oauth_response($res){
        return $res;
    }
    public function get_token(){
        $provider = new Stevenmaguire\OAuth2\Client\Provider\Basecamp([
            'clientId'          => '0380a7df112fc726de678383571bf0605975e85d',
            'clientSecret'      => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',
            'redirectUri'       => 'https://dev.clear99.com/wp-json/gbf/v1/auth',
        ]);

        if (!isset($_GET['code'])) {

            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
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
                printf('Hello %s!', $user->getId());

            } catch (Exception $e) {

                // Failed to get user details
                exit('Oh dear...');
            }

            // Use this to interact with an API on the users behalf
            echo $token->getToken();
            $_SESSION['basecamptoken'] = $token;
        }
    }    
}