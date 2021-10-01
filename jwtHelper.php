<?php


$b2c_metadata_link = file_get_contents("https://yourb2cdomain.b2clogin.com/yourb2cdomain.onmicrosoft.com/v2.0/.well-known/openid-configuration?p=B2C_1_SignUpAndSignIn");
//echo $b2c_meta_data;

class JWT_HELPER 
{

    /*
        Get the value of jwks_uri...
    */
    
    public static function get_JWKS_URI($b2c_meta_data)
    {
        $metadata_obj = json_decode($b2c_meta_data);

        if (empty($metadata_obj)) 
        {
            throw new InvalidArgumentException('metadata must not be empty');
        }
        if (!isset($metadata_obj->{'jwks_uri'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "jwks_uri" parameter');
        }
       return $metadata_obj->{'jwks_uri'};
        
    }

    /*
        Get the value of kid...
    */
    
    public static function get_KID($b2c_meta_data)
    {

        $jwks_uri_return = file_get_contents(JWT_HELPER::get_JWKS_URI($b2c_meta_data));
        $jwks_obj = json_decode($jwks_uri_return);

        if (empty($jwks_obj)) 
        {
            throw new InvalidArgumentException('jwks_uri must not be empty');
        }
        if (!isset($jwks_obj->{'keys'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "keys" parameter');
        }
       return $jwks_obj->keys[0]->kid;
        //return $jwks_uri_return;
    }

	public static function get_e($b2c_meta_data)
    {

        $jwks_uri_return = file_get_contents(JWT_HELPER::get_JWKS_URI($b2c_meta_data));
        $jwks_obj = json_decode($jwks_uri_return);

        if (empty($jwks_obj)) 
        {
            throw new InvalidArgumentException('jwks_uri must not be empty');
        }
        if (!isset($jwks_obj->{'keys'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "keys" parameter');
        }
       return $jwks_obj->keys[0]->e;
        //return $jwks_uri_return;
    }

	public static function get_n($b2c_meta_data)
    {

        $jwks_uri_return = file_get_contents(JWT_HELPER::get_JWKS_URI($b2c_meta_data));
        $jwks_obj = json_decode($jwks_uri_return);

        if (empty($jwks_obj)) 
        {
            throw new InvalidArgumentException('jwks_uri must not be empty');
        }
        if (!isset($jwks_obj->{'keys'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "keys" parameter');
        }
       return $jwks_obj->keys[0]->n;
        //return $jwks_uri_return;
    }
	
	//Get jwks_uri Object.
	public static function get_jwks_uri_Object($b2c_meta_data)
    {

        $jwks_uri_return = file_get_contents(JWT_HELPER::get_JWKS_URI($b2c_meta_data));
        $jwks_obj = json_decode($jwks_uri_return);

        if (empty($jwks_obj)) 
        {
            throw new InvalidArgumentException('jwks_uri must not be empty');
        }
        if (!isset($jwks_obj->{'keys'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "keys" parameter');
        }
        return json_encode($jwks_obj->{'keys'});
        //return $jwks_uri_return;
    }
	
	//Return the working JSON format that the go script will check for JWK values. The script is in ~/test.go
	public static function get_jwk_in_json($b2c_meta_data)
    {

        $jwks_uri_return = file_get_contents(JWT_HELPER::get_JWKS_URI($b2c_meta_data));
        $jwks_obj = json_decode($jwks_uri_return);

        if (empty($jwks_obj)) 
        {
            throw new InvalidArgumentException('jwks_uri must not be empty');
        }
        if (!isset($jwks_obj->{'keys'})) 
        {
            throw new UnexpectedValueException('metadata must contain a "keys" parameter');
        }
		
		$jwk_array = array("alg"=>"RS256", "e"=>$jwks_obj->keys[0]->e, "n"=>$jwks_obj->keys[0]->n, "kty"=>$jwks_obj->keys[0]->kty, "use"=>$jwks_obj->keys[0]->use);
		return json_encode($jwk_array);
    }
}
