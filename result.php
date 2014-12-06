<?php
	$apikey = 'wyd3dhj7kcpv492x3pwmczes';
	$tomato_url = 'http://api.rottentomatoes.com/api/public/v1.0.json?apikey='.$apikey;
	$movie_url = 'http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey='.$apikey.'&q='.$_GET['query'].'&page_limit='.$_GET['limiter'].'&page='.$_GET['page'];
	$curl = curl_init($movie_url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$curl_response = curl_exec($curl);
	if ($curl_response === false) {
	    $info = curl_getinfo($curl);
	    curl_close($curl);
	    die('error occured during curl exec. Additioanl info: ' . var_export($info));
	}
	curl_close($curl);
	$decoded = json_decode($curl_response);
	if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
	    die('error occured: ' . $decoded->response->errormessage);
	}
	//echo 'response ok!';
	echo $curl_response;
	//echo json_encode($decoded->movies);
	//var_export($decoded->response);

	//