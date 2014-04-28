<?php
	#Jiaming Li
	#A simple page that I created to play with Yelp's api and 
	#Uses Geoplugin's ip address database to find the user's location(not so accurate but the best I can do)
	#Uses Yelp's api to get data about places to eat nearby
	$re = file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']);
	$geo = unserialize($re);
	# Set default latitude and longitude
	$latitude = "47.653555";
	$longitude = "-122.3062295";
	# If the data return from geoplugin is good, then update latitude and longitude
	if (intval($geo['geoplugin_status']) == 200) {
		$latitude = $geo['geoplugin_latitude'];
		$longitude = $geo['geoplugin_longitude'];
	}
	# Set up OAuth stuffs
	require_once ('OAuth.php');
	$unsigned_url = "http://api.yelp.com/v2/search?term=food&ll=$latitude,$longitude&radius_filter=2000";
	$consumer_key = "";
	$consumer_secret = "";
	$token = "";
	$token_secret = "";
	$token = new OAuthToken($token, $token_secret);
	$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
	$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
	$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
	$oauthrequest->sign_request($signature_method, $consumer, $token);
	$signed_url = $oauthrequest->to_url();
	$ch = curl_init($signed_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch); // Yelp response
	curl_close($ch);
	$response = json_decode($data, true);
	$index = rand(0, $response["total"] - 1);	
	while (!isset($response["businesses"][$index])) {
		$index = rand(0, $response["total"] - 1);
	}
	$result = $response["businesses"][$index]["name"];
?>
<!Doctype html>
<html>
	<head>
		<title> Where to eat? </title>
		<link rel="stylesheet" type="text/css" href="whereToEat.css">
	</head>

	<body>
		<h1> The almighty oracle says you should eat <span><?= $result ?></span> </h1>
		<h1> <?= (int)$response["businesses"][$index]["distance"]?> meters away from you</h1>
		<h1> At <span><?= $response["businesses"][$index]["location"]["display_address"][0] ?></span> </h1>
	</body>
</html>
