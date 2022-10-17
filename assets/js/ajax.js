/*
 * Button: Ajax Data
 * 
 * @Json    data  Ajax data
 * @jQuery  $el   jQuery field element
 */
 
filter('acfe/fields/button/data',                  data, $el);
filter('acfe/fields/button/data/name=my_button',   data, $el);
filter('acfe/fields/button/data/key=field_abc123', data, $el);
acf.addFilter('acfe/fields/button/data/name=my_button', function(data, $el){
    
    // add custom key
    data.custom_key = 'value';    
    
    // return
    return data;
    
});

//Config for Oauth2 Call
const config = {
    client: {
      id: '3b6a529d065cb260f894e1407716a4b5a5994bed',
      secret: 'd5c8682ac065c4e30205b2e54cd9c8b6a2a84d8d'
    },
    auth: {
      tokenHost: 'https://launchpad.37signals.com/authorization/new'
    }
  };
  
  const { ClientCredentials, ResourceOwnerPassword, AuthorizationCode } = require('simple-oauth2');


//The Oauth2 Call
  async function run() {
    const client = new AuthorizationCode(config);
  
    const authorizationUri = client.authorizeURL({
      redirect_uri: 'https://caylacorkill.local/wp-json/gbf/v1/auth',
      scope: '<scope>',
      state: '<state>'
    });
  
    // Redirect example using Express (see http://expressjs.com/api.html#res.redirect)
    res.redirect(authorizationUri);
  
    const tokenParams = {
      code: '<code>',
      redirect_uri: 'https://caylacorkill.local/wp-json/gbf/v1/auth',
      scope: '<scope>',
    };
  
    try {
      const accessToken = await client.getToken(tokenParams);
    } catch (error) {
      console.log('Access Token Error', error.message);
    }
  }
  
  run();