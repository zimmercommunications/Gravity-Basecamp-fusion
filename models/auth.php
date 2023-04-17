<?php
/* Triggered by ../assets/js/ajax-oauth.js
/* this file is used by the 'authorize' button on the options page to trigger the inital fetch for the auth token from the BC auth server.

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