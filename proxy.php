<?php

// Define your HTTP API server (domain only) here
define('TARGET_HOST', "www.example.com");
// Change request timeout as needed
define('REQUEST_TIMEOUT', 180);

// We use http_build_url() from https://github.com/jakeasmith/http_build_url
define('BASEPATH', getcwd());
require_once 'http_build_url.php';

// A helper function to check if one string starts with another substring.
function starts_with($string, $query) {
	return substr($string, 0, strlen($query)) === $query;
}

// First we reconstruct the request URI as following:
// https://www.mysite.com/api/get_products?filter=1 => https://www.example.com/api/get_products?filter=1
$parsed_url = parse_url($_SERVER['REQUEST_URI']);
$parsed_url['host'] = TARGET_HOST;
$parsed_url['scheme'] = 'https';
$new_url = http_build_url($parsed_url);

// Initialize and configure our curl session
$session = curl_init($new_url);

curl_setopt($session, CURLOPT_CONNECTTIMEOUT, REQUEST_TIMEOUT);
curl_setopt($session, CURLOPT_TIMEOUT, REQUEST_TIMEOUT);

// This implementation supports POST and GET only, add custom login here as needed
$request_method = $_SERVER['REQUEST_METHOD'];
if ($request_method === 'POST') {
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
} else {
    curl_setopt($session, CURLOPT_CUSTOMREQUEST, $request_method);
}

$request_content_type = $_SERVER["CONTENT_TYPE"];
curl_setopt($session, CURLOPT_HTTPHEADER, array("Content-Type: $request_content_type"));
curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 1);
curl_setopt($session, CURLOPT_HEADER, true);

// Here we pass our request cookies to curl's request
$cookie_string = '';
foreach ($_COOKIE as $key => $value) {
    $cookie_string .= "$key=$value;";
};
curl_setopt($session, CURLOPT_COOKIE, $cookie_string);

// Finally, trigger the request
$response = curl_exec($session);

// Due to CURLOPT_HEADER=1 we will receive body and headers, so we need to split them
$header_size = curl_getinfo($session, CURLINFO_HEADER_SIZE);
$response_body = substr($response, $header_size);

$response_httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);
$response_content_type = curl_getinfo($session, CURLINFO_CONTENT_TYPE);
$response_error = curl_error($session);
curl_close($session);

// This part copies all Set-Cookie headers from curl's response to this php response
$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
foreach (explode("\r\n", $header_text) as $i => $line)
	if (starts_with($line, "Set-Cookie")) {
		header($line, 0);
	}

header("Content-type: $response_content_type", 1);

http_response_code($response_httpcode);

// Send the response output
$response = $response_error ? $response_error : $response_body;
echo $response;
