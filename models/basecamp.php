<?php
//The basecamp model is to be used to interact with the Basecamp API

class Basecamp{
    public function __construct(){

    }
    public function send_data($endpoint){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://3.basecampapi.com/4212850/buckets/17409592/todolists/4692674705/todos.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "content": "API Test by Jesse",
        "description": "<h1>The modest, but handsome web developer</h1>",
        "due_on": "2023-05-01"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer BAhbB0kiAbB7ImNsaWVudF9pZCI6IjAzODBhN2RmMTEyZmM3MjZkZTY3ODM4MzU3MWJmMDYwNTk3NWU4NWQiLCJleHBpcmVzX2F0IjoiMjAyMy0wNS0wMlQxNjo0NToyN1oiLCJ1c2VyX2lkcyI6WzQzMzY0MzkyXSwidmVyc2lvbiI6MSwiYXBpX2RlYWRib2x0IjoiMzQxZThmNzY1MzdlMWQwOTIzYWVjMTgwNzAyMjQzMGIifQY6BkVUSXU6CVRpbWUNUNAewB5CvbUJOg1uYW5vX251bWkCqQI6DW5hbm9fZGVuaQY6DXN1Ym1pY3JvIgdoEDoJem9uZUkiCFVUQwY7AEY=--f8430303f7a5301edb79731d2b8b360d6bcc463a',
            'Cookie: _bc3_session=Pri8I%2FDbPcDK4D1ITu94ZfWZARgRSGuXpKHh7Im0XarpGuIGy7fH8Jx2hrLiH1Kyiab1ZXJ0EqfpF8XSn1sa0dsOf4VXZTY3guuirdfYqDOaTkkPOovgEE4mV9YLJji0NKEQL05CuqtoHw%2BdRiXScjqnkNTekeyF%2FUHkNjzKOKy1yi364PK5f976O8oV%2F5HKUEaqEVDCuJ9h2IdI%2BGy7a9QeHNAn695DO76hKfAJClwQh44%2FUORwXpbXCGzMGYyJUxgP7Bnr1L6WXw%3D%3D--MEe1ChSckv8cOCGt--lhxS0Lvx5pWt04kOhiNK9g%3D%3D; force-primary-dc=true'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

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
    public function get_token(WP_REST_Request $request){
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
}