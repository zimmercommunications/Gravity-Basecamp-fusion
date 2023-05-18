<?php
//The basecamp model is to be used to interact with the Basecamp API

class Basecamp{
    public function __construct(){
        
    }
    public function send_data($endpoint, $postfields){

        $curl = curl_init();

        if(!get_option('basecamp_token') && is_user_logged_in()){
            exit('Missing OAuth Token, please refetch in the options menu.');
        }
        elseif(!get_option('basecamp_token')){
            //Do Nothing
        }
        else{
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://3.basecampapi.com/4212850/buckets/17409592/todolists/4692674705/todos.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postfields,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer '.get_option('basecamp_token')->getToken(),
                    'Cookie: _bc3_session=Pri8I%2FDbPcDK4D1ITu94ZfWZARgRSGuXpKHh7Im0XarpGuIGy7fH8Jx2hrLiH1Kyiab1ZXJ0EqfpF8XSn1sa0dsOf4VXZTY3guuirdfYqDOaTkkPOovgEE4mV9YLJji0NKEQL05CuqtoHw%2BdRiXScjqnkNTekeyF%2FUHkNjzKOKy1yi364PK5f976O8oV%2F5HKUEaqEVDCuJ9h2IdI%2BGy7a9QeHNAn695DO76hKfAJClwQh44%2FUORwXpbXCGzMGYyJUxgP7Bnr1L6WXw%3D%3D--MEe1ChSckv8cOCGt--lhxS0Lvx5pWt04kOhiNK9g%3D%3D; force-primary-dc=true',
                    'User-Agent: Gravity Forms Basecamp Fusion (https://dev.clear99.com/)'
                ),
                ));
        
                $response = curl_exec($curl);
        
                curl_close($curl);
                echo $response;
        }
    }
    public function prep_data($data){
        $data = [
                "content" => "API Test by Jesse",
                "assignee_ids" => "",
                "description" => "",
                "due_on" => "",
                "starts_on" => "",
                "completion_subscriber_ids" => "",
                "notify" => "", 
        ];

        return $data;
    }
    public function handle_oauth_response($res){
        return $res;
    }
    public function get_token_v2(){
        exit();
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => '0380a7df112fc726de678383571bf0605975e85d',    // The client ID assigned to you by the provider
            'clientSecret'            => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',    // The client password assigned to you by the provider
            'redirectUri'             => 'https://dev.clear99.com/wp-json/gbf/v1/auth',
            'urlAuthorize'            => 'https://launchpad.37signals.com/authorization/new?type=web_server',
            'urlAccessToken'          => 'https://launchpad.37signals.com/authorization/token',
            'urlResourceOwnerDetails' => 'https://3.basecampapi.com/4212850'
        ]);
        
        // We don't have an authorization code, so we get one

        
        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $provider->getAuthorizationUrl();
    
        // Get the state generated for you and store it to a cookie that expires in two weeks. (I'm not sure if it should expire at all)
        setcookie('oauth2state', $provider->getState(), time() + 14 * 24 * 60 * 60, '/');

    
        // Optional, only required when PKCE is enabled.
        // Get the PKCE code generated for you and store it to a cookie that expires in two weeks. (I'm not sure if it should expire at all)
        //setcookie('oauth2pkceCode', $provider->getPkceCode(), time() + 14 * 24 * 60 * 60, '/');
    
        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
        exit;
        

    }
    //Function to initiate the authentication process.
    public function get_token(){
        $authState = $_COOKIE('oauth2state');

        $provider = new Stevenmaguire\OAuth2\Client\Provider\Basecamp([
            'clientId'          => '0380a7df112fc726de678383571bf0605975e85d',
            'clientSecret'      => '64b11d0a24af0470a3dafa7b75fc1362bb25ab3e',
            /* Redirect to this plugin's response handler */
            'redirectUri'       => 'https://dev.clear99.com/wp-json/gbf/v1/auth',
            'urlResourceOwnerDetails' => 'https://launchpad.37signals.com/authorization.json'
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
}