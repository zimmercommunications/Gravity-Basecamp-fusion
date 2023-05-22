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


    //Psuedo API to retrieve user data - TODO actually fetch user data from BC3 API
    public function get_user_ID($target_name){
        $users = '
        [
            {
                "id": 24712099,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8yNDcxMjA5OT9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--ca8d1cf8551e0af79323d2525033adcfbc1ac398",
                "name": "Carrie Berkbuegler",
                "email_address": "cberkbuegler@zrgmail.com",
                "personable_type": "User",
                "title": "Director of Sales",
                "bio": null,
                "location": null,
                "created_at": "2019-04-18T20:32:16.943Z",
                "updated_at": "2020-05-05T13:40:34.209Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBKMTeQE=--d2357f3b0142bff76b945f21952345f5850657b9/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 24712111,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8yNDcxMjExMT9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--92e17ce49904bc7eef41a03920f79b7dad19d4ef",
                "name": "Carla Leible",
                "email_address": "cleible@zrgmail.com",
                "personable_type": "User",
                "title": "General Manager",
                "bio": null,
                "location": null,
                "created_at": "2019-04-18T20:34:51.020Z",
                "updated_at": "2019-04-22T18:21:12.426Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBK8TeQE=--c1b5557376306b9de866b86b67e103e9eb173efb/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 25355855,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8yNTM1NTg1NT9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--e8af130531f03c51d9827cea36a1bd759f71667a",
                "name": "Cathy Atkins",
                "email_address": "cathy@fuze32.com",
                "personable_type": "User",
                "title": "Inbound Marketing Consultant",
                "bio": "",
                "location": null,
                "created_at": "2019-06-05T02:36:14.847Z",
                "updated_at": "2023-03-28T19:11:35.717Z",
                "admin": true,
                "owner": true,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBE%2FmggE=--d84f84f68ec1cad4dfee23b2cd7308fab62d528a/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 33349713,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8zMzM0OTcxMz9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--76c4e62e9a54f0b3c1c8acbbf5bc0078064595d4",
                "name": "Rachel Perrone",
                "email_address": "rperrone.zrg@gmail.com",
                "personable_type": "User",
                "title": "",
                "bio": null,
                "location": null,
                "created_at": "2020-12-10T22:42:51.658Z",
                "updated_at": "2021-06-24T15:20:51.555Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBFHg%2FAE=--9e70d6e494695d0c461a279bf9e464ad576e0f79/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 33461898,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8zMzQ2MTg5OD9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--7879fb4495332d06fe06e7441e04f31033763db4",
                "name": "Jesse Corkill",
                "email_address": "jcorkill97@gmail.com",
                "personable_type": "User",
                "title": "Web Developer",
                "bio": "",
                "location": null,
                "created_at": "2020-12-21T18:56:47.732Z",
                "updated_at": "2022-09-19T16:23:36.885Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBIqW%2FgE=--05b4c281ba12f9ab89c980c85cd50e30052cd666/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 35264588,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8zNTI2NDU4OD9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--cd509ae1fdcb7519b3657717be9a37ad4d9c242a",
                "name": "Nick Rasmussen",
                "email_address": "nrasmussen@zrgmail.com",
                "personable_type": "User",
                "title": "Account Executive",
                "bio": null,
                "location": null,
                "created_at": "2021-05-07T15:39:40.986Z",
                "updated_at": "2021-12-20T19:14:26.834Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBEwYGgI=--e2b49dc2327f4d710c43c3188bed286e5e287613/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 36728346,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8zNjcyODM0Nj9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--1d55be4a0252af25e529643028a97a0c0f660c6a",
                "name": "Sean Patton",
                "email_address": "spatton@mailzimmer.com",
                "personable_type": "User",
                "title": "Web Developer ",
                "bio": "",
                "location": "",
                "created_at": "2021-09-13T15:16:46.726Z",
                "updated_at": "2022-08-10T20:52:28.356Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": true,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBBpuMAI=--0f448ea14e747d8323d55ab2e1841a00161efaa1/avatar?v=1",
                "company": {
                    "id": 1709655,
                    "name": "fuze32"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            },
            {
                "id": 39964005,
                "attachable_sgid": "BAh7CEkiCGdpZAY6BkVUSSIpZ2lkOi8vYmMzL1BlcnNvbi8zOTk2NDAwNT9leHBpcmVzX2luBjsAVEkiDHB1cnBvc2UGOwBUSSIPYXR0YWNoYWJsZQY7AFRJIg9leHBpcmVzX2F0BjsAVDA=--11c027c4c382d97abdac8f5a881d92419f0c9385",
                "name": "Jake Harshman",
                "email_address": "jharshman@mailzimmer.com",
                "personable_type": "User",
                "title": "Web Designer",
                "bio": null,
                "location": null,
                "created_at": "2022-06-14T15:24:40.816Z",
                "updated_at": "2023-05-02T21:46:24.985Z",
                "admin": false,
                "owner": false,
                "client": false,
                "employee": false,
                "time_zone": "America/Chicago",
                "avatar_url": "https://bc3-production-assets-cdn.basecamp-static.com/4212850/people/BAhpBGXNYQI=--32f7113c5bb32e0f98b100bde661902dc5bd23f4/avatar?v=1",
                "company": {
                    "id": 2669666,
                    "name": "Zimmer Communications"
                },
                "can_manage_projects": true,
                "can_manage_people": true
            }
        ]';
        echo '<script type="text/javascript">console.log("Users")</script>'; 
        echo '<script type="text/javascript">console.log('.json_encode($users).')</script>'; 
        $selected = array_filter($users, function($v, $k){
            if(str_contains($v['name'], $target_name)){
                return true;
            }else{
                return false;
            }   
        }, ARRAY_FILTER_USE_BOTH);
        return $selected[0]['id'];
    }

}