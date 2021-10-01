<?php

require("../../db_connect/dbConnect.php");

class AUTHORIZATION_HELPER
{
	/*
	Check if user is authorize to access the App.
	*/
	public static function check_AUTHZ($email, $app_id, $pdo)
    {
        if (empty($email)) 
        {
            throw new InvalidArgumentException('Email must not be empty');
        }
        if (empty($app_id)) 
        {
            throw new InvalidArgumentException('App ID must not be empty');
        }
		
		$sql_user = "SELECT id_user, given_name, username, email, role_id FROM users_tb WHERE email=?";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$email]);
        $userDetail = $stmt_user->fetch();
        
        $sql_app = "SELECT id_apps, app_name, b2c_app_id FROM apps_tb WHERE b2c_app_id=?";
        $stmt_app = $pdo->prepare($sql_app);
        $stmt_app->execute([$app_id]);
        $appDetail = $stmt_app->fetch();

        $stmt = $pdo->prepare("SELECT count(*) FROM user_app_tb WHERE id_user = ? AND id_app = ?");
        $stmt->execute([$userDetail['id_user'], $appDetail['id_apps']]);
        $count = $stmt->fetchColumn();
    
        return $count;
        /*
        if () 
        {
            
        }
        */
    }
}
?>
