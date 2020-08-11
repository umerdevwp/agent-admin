<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:title" content="Agent Admin Login">
    <meta property="og:description" content="brevis, barbatus clabulares aliquando convertam de dexter, peritus capio. devatio clemens habitio est.">
    <meta property="og:image" content="http://digipunk.netii.net/images/radar.gif">
    <meta property="og:url" content="http://digipunk.netii.net">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    
    <link rel="stylesheet" href="components/base/base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="components/base/script.js"></script>

	<!-- Latest CDN production Javascript and CSS -->
	<script src="https://global.oktacdn.com/okta-signin-widget/3.1.0/js/okta-sign-in.min.js" type="text/javascript"></script>
	<link href="https://global.oktacdn.com/okta-signin-widget/3.1.0/css/okta-sign-in.min.css" type="text/css" rel="stylesheet"/>

  </head>
  <body>
    <div class="page page-gradient-bg">
      <div id="particles-container"></div>
      <section class="section-lg section-one-screen">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="row row-10 align-items-end">
                <div class="col-6 col-sm-7"><a href="index.html"><img src="images/AgentAdmin_Logo.png" alt=""></a></div>
                <!--<div class="col-6 col-sm-5 text-right">
                  <a class="font-weight-bold" href="#">Sign In</a>
                  <span class="px-2">|</span>
                   <a href="https://zfrmz.com/HkojFayuoZH8407xrVaR">Register</a> 
                  <a href="#">Register</a>
                </div>-->
              </div>
              <form class="panel" method="post">
                <!--<div class="panel-header">
                  <div class="row row-10">
                    <div class="col-md-4">
                      <button class="btn btn-primary btn-block"> <span class="fa fa-facebook-f"></span> Facebook</button>
                    </div>
                    <div class="col-md-4">
                      <button class="btn btn-danger btn-block"><span class="fa fa-google-plus"></span> Google +</button>
					  </div>
                    <div class="col-md-4">
                      
                    </div>
                  </div>
                </div>-->
                <div class="panel-body">
                  <div class="row row-30">
                    <div class="col-lg-5 order-lg-2 login-description">
                      <h4>You'll Have Access To Your:</h4>
                      <ul>
                        <li>Corporate Documents</li>
                        <li>Federal Notices</li>
                        <li>State Notices</li>
                        <li>Service of Process</li>
						            <li>Commercial Mail</li>
                      </ul>
                    </div>
                    
          <div class="col-lg-7 order-lg-1" id="osw-container2"></div>
          
                  </div>
                </div>
                <div class="panel-footer hide">
                  <div class="row row-10">
                    <div class="col-sm-6">
                      <div class="custom-control custom-switch custom-switch-lg custom-switch-primary">
                        <input class="custom-control-input" type="checkbox" id="yooedpuq"/>
                        <label class="custom-control-label" for="yooedpuq">Remember me
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-6 text-sm-right">
                      <button class="btn btn-primary">Sign In</button>
                    </div>
                  </div>
                </div>
                <div id="divPositiveSSL">
            <script type="text/javascript"> //<![CDATA[
var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png", "POSDV", "none");
</script>
            </div>
              </form>

            </div>
            
          </div>
          
        </div>
      </section>
      
    </div>
    
  </body>

<script type="text/javascript">
//<!--
//function redirectTo(sUrl) {
//window.location = sUrl
//}
//-->
</script>

<script>

var signIn = new OktaSignIn(
  {
    baseUrl: '<?=getenv("OKTA_BASE_URL");?>',
    el: '#osw-container2',
	authParams: {
		issuer: '<?=getenv("OKTA_BASE_URL");?>oauth2/default',
    responseType: ['id_token', 'token']
	},
	idps: [
            {type: 'google', id: '0oa2d6hm9qeSSv62N357'},
            ],
	logo: '',
	language: 'en',
	i18n: {
		en: {
		  'primaryauth.title': ' '
		}
	},
  }
);

signIn.showSignInToGetTokens({
  clientId: '<?=getenv("OKTA_CLIENT_ID");?>',

  // must be in the list of redirect URIs enabled for the OIDC app
  redirectUri: '<?=getenv("OKTA_REDIRECT_URI");?>',

  // Return an access token from the authorization server
  getAccessToken: true,

  // Return an ID token from the authorization server
  getIdToken: true,
  scope: 'openid profile'
});
</script>
</html>