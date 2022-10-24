<?php
namespace ZimmerCommunications\models;

//The basecamp model is to be used to interact with the Basecamp API
use Stevenmaguire\OAuth2\Client\Provider\Basecamp as Provider;
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
    public function get_token($redirect, $cid, $cs){
        $token = '';
        $provider = new Provider([
            'clientId'          => $cid,
            'clientSecret'      => $cs,
            'redirectUri'       => $redirect,
        ]);

        if (!isset($_GET['code'])) {

            //If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            if(!$authUrl){
                exit('getAuthorizationUrl() returned an empty string on line 28 of basecamp.php '. print_r($provider));
            }
            //To-Do: $provider->getState() is not returning a value.
            $_SESSION['oauth2state'] = $provider->getState();

            //redirect to auth token server url
            header('Location: '.$authUrl);
            exit;

        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state. State was: '.$_GET['state'].'. oauth2state was: '.$_SESSION['oauth2state'].'.');

        } else {
            // Try to get an access token (using the authorization code grant)
            try{
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);  
                // Use this to interact with an API on the users behalf
                return $token->getToken();
            }catch(Exception $e){
                echo 'Caught exception: ', $e->getMessage(), '\n';
            }
              
        }

    }    
}