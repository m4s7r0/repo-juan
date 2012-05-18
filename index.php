<?php
//session_start();

include_once("lib/EpiCurl.php");
include_once("lib/EpiOAuth.php");
include_once("lib/EpiTwitter.php");
include_once("lib/secret.php");

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
$oauth_token = $_GET["oauth_token"];
$msg = "I have downloaded an eCoupon from http://lenovo-promos.orchestra.io/ saving up to $550 on a Lenovo IdeaPad Z570!!";

	if($oauth_token == '')
	{
		$url = $twitterObj->getAuthorizationUrl();
		$html = file_get_contents("home.html");
		$html = str_replace("{URL}", $url, $html);
		$html = str_replace("{TWEET}", $msg, $html);
		print($html);
	}
	else
	{
		$twitterObj->setToken($_GET["oauth_token"]);
		$token = $twitterObj->getAccessToken();
		$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
		$_SESSION["ot"] = $token->oauth_token;
		$_SESSION["ots"] = $token->oauth_token_secret;

		$twitterInfo= $twitterObj->get_accountVerify_credentials();
		$twitterInfo->response;

		$update_status = $twitterObj->post_statusesUpdate(array("status" => $msg));
		$temp = $update_status->response;
		//print_r($temp);
		if(array_key_exists('error', $temp))
			echo("There was a problem");
		else
		{
			$username = $twitterInfo->screen_name;
			$profilepic = $twitterInfo->profile_image_url;
			$html = file_get_contents("download.html");
			$html = str_replace("{NOMBRE}", $username, $html);
			$html = str_replace("{IMAGEN}", $profilepic, $html);
			print($html);

/*
			header("Content-disposition: attachment; filename=CuponLenovo.pdf");
			header("Content-type: application/pdf");
			readfile("CuponLenovo.pdf");
*/
		}
     }
?>