<?php

//define some variables for both calls
$idx_broker_api_key = 'YourAPIKey';
$bombbomb_api_key = 'YourBombBombAPIKey';
$interval = 1; //number of hours before $start_date see IDX Broker API docs for more on this
$start_date = date('c'); //using current date to begin pulling leads, set to ISO 8601

//get leads from IDX Broker API

// access URL and request method
$url = 'https://api.idxbroker.com/leads/lead?interval=' . $interval . '&' . $start_date . '&dateType=subscribeDate';
$method = 'GET';

// headers (required and optional)
$headers = array(
  'Content-Type: application/x-www-form-urlencoded', // required
  'accesskey: ' . $idx_broker_api_key, // required - replace with your own
  'outputtype: json', // optional - overrides the preferences in our API control page
  'version: 1.1.1'// overide IDX Broker dashboard settings to ensure we use the correct API version
);

// set up cURL
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $url);
curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

if ($method != 'GET')
  curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);

// exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
$response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code >= 200 || $code < 300)
  $response = json_decode($response,true);
else
  $error = $code;

//call to bombbomb in a similar fashion as the IDX Broker call

foreach ($response as $lead){
// access URL and request method
$url = 'https://app.bombbomb.com/app/api/api.php?';
$data = array('method' => 'AddContact', 'api_key' => $bombbomb_api_key, 'eml' => $lead["email"], 'firstname' => $lead["firstName"], 'lastname' => $lead["lastName"]);
$data = http_build_query($data); // encode and & delineate
$method = 'POST';

// headers (required and optional)
$headers = array(
    'Content-Type: application/x-www-form-urlencoded', // required
    );

// set up cURL
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $url.$data);
curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

if ($method != 'GET')
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);

// send the data
curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

// exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
$bomb_response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code >= 200 || $code < 300)
    $bomb_response = json_decode(response,true);
else
    $error = $code;

}




?>
