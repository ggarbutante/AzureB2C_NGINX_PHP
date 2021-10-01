<?php

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/jwtHelper.php';

use Firebase\JWT\JWT;

class  JWT_VALIDATOR
{
	//generate PUB Key by executing the go script.
	public static function get_PUBKEY_from_GO($jwks_uri_Object)
    {
		$output = shell_exec('go run /home/test.go');
		$output = ltrim($output,"-----BEGIN RSA PUBLIC KEY-----\n");
		$output = str_replace("-----END RSA PUBLIC KEY-----", "", $output);
		
		//remove strange whitspaces...
		$output = preg_replace('/\s+/', '', $output);

		$str = chunk_split($output, 64, "\n");
		$public_key = "-----BEGIN PUBLIC KEY-----\n".$str."-----END PUBLIC KEY-----\n";
				
		return $public_key;
    }
	
	//generate PUB Key by reading file content generated from go cron job.
	public static function get_PUBKEY_from_FILE()
    {
		$output = file_get_contents("/var/www/html/.pub.key");
		$output = ltrim($output,"-----BEGIN RSA PUBLIC KEY-----\n");
		$output = str_replace("-----END RSA PUBLIC KEY-----", "", $output);
		
		//remove strange whitspaces...
		$output = preg_replace('/\s+/', '', $output);

		$str = chunk_split($output, 64, "\n");
		$public_key = "-----BEGIN PUBLIC KEY-----\n".$str."-----END PUBLIC KEY-----\n";
				
		return $public_key;
    }

	public static function validate_JWT($jwt, $public_key)
    {
		try 
		{
			$decoded = JWT::decode($jwt, $public_key, array('RS256'));
			$decoded_array = (array) $decoded;
			$validationOutput = $decoded;
		}

		catch(Exception $e) 
		{
			$validationOutput = "Error_Message: " .$e->getMessage();
		}
				
		return $validationOutput;
    }
}
