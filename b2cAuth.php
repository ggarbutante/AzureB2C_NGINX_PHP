<?php

// Initialize the session.
session_start();

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/jwtValidator.php';

require __DIR__ . '/authorizationHelper.php';

// Getting help from Firebase Lib...
//use Firebase\JWT\JWT;



if ((!isset($_SESSION['id_token'])) and (!isset($_SESSION['code'])) and (!isset($_SESSION['state']))) 
{
	// POST request still not working..
	//if ( (isset($_POST['id_token'])) and (isset($_POST['code'])) and (isset($_POST['state'])) ) 
	# Trying GET for now..
	if ( (isset($_GET['id_token'])) and (isset($_GET['code'])) and (isset($_GET['state'])) ) 
	{
		
		//validate JWT first
		$id_token 	= $_GET['id_token'];
		$code		= $_GET['code'];
		$state		= $_GET['state'];
		
		//$pub_key = JWT_VALIDATOR::get_PUBKEY_from_FILE();
		//echo $pub_key;
		
		$validationResult = JWT_VALIDATOR::validate_JWT($id_token, JWT_VALIDATOR::get_PUBKEY_from_FILE());
		//echo $output;

		//if JWT is not valid, set user session...
		//if (str_starts_with((String) $validationResult, 'Error_Message:'))
		if (is_string($validationResult))
		{
			header("HTTP/1.1 401 Unauthorized");
			die();
		}
		//if JWT is valid, set user session.
		else
		{
			// Set session variables
			$_SESSION["id_token"] = $id_token;
			$_SESSION["code"] = $code;
			$_SESSION["state"] = $state;
			
			// Auth success! Now check if user is authorized.
			$email = $validationResult->emails[0];
			$app_id = $validationResult->aud;
			$checkAuthStatus = AUTHORIZATION_HELPER::check_AUTHZ($email, $app_id, $pdo);

			// If $checkAuthStatus = 1, user is authorized and will get access to the app main page.
			if ($checkAuthStatus == 1)
			{
				header("HTTP/1.1 200 OK");
				die();
				//header("HTTP/1.1 203 Non-Authoritative Information"); //203 Non-Authoritative Information
				//header("X-Original-URI: ".$state);
				//die();
			}
			// If $checkAuthStatus = 0, user is unauthorized and will receive status code 403 Forbidden.
			else
			{
				header("HTTP/1.1 403 Forbidden");
				die("User ".$email." is not Authorized to access this application: ".$app_id.".");
			}
		}

	}
	else 
	{
		 //return 401 and redirect to b2c login.
		 header("HTTP/1.1 401 Unauthorized");
		 die();
		 //header("HTTP/1.1 403 Forbidden");
		 //die("User NOT AUTHORIZED!!!!!");
	}

}
// If Session found, validate JWT id_token session first to make sure it is not expired.
else
{
	$id_token 	= $_SESSION['id_token'];
	//$code		= $_SESSION['code'];
	//$state		= $_SESSION['state'];
	$validationResult = JWT_VALIDATOR::validate_JWT($id_token, JWT_VALIDATOR::get_PUBKEY_from_FILE());
	
	
	//if JWT cookie is not valid or is expired, return 401 and redirect to b2c login.
	//if (str_starts_with((String) $validationResult, 'Error_Message:'))
	if (is_string($validationResult))
	{
		header("HTTP/1.1 401 Unauthorized");
		die();
	}
	//if JWT cookie is valid, check user if authorized to access the app.
	else
	{
		// Auth success! Now check if user is authorized.
		$email = $validationResult->emails[0];
		$app_id = $validationResult->aud;
		$checkAuthStatus = AUTHORIZATION_HELPER::check_AUTHZ($email, $app_id, $pdo);

		// If $checkAuthStatus = 1, user is authorized and will get access to the app main page.
		if ($checkAuthStatus == 1)
		{
			header("HTTP/1.1 200 OK");
			die();
			//header("HTTP/1.1 203 Non-Authoritative Information"); //203 Non-Authoritative Information
			//header("X-Original-URI: ".$state);
			//die();
		}
		// If $checkAuthStatus = 0, user is unauthorized and will receive status code 403 Forbidden.
		else
		{
			header("HTTP/1.1 403 Forbidden");
			die("User ".$email." is not Authorized to access this application: ".$app_id.".");
		}

	}
}

?>
