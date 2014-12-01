<?php

//define some variables for both calls
$idx_broker_api_key = 'YourAPIKey';
$contactually_api_key = 'YourContactuallyAPIKey';
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

echo  $code;

//call to contactually in a similar fashion as the IDX Broker call

foreach ($response as $lead){

$data = '{"contact":{"first_name":"' . $lead["firstName"] . '","last_name":"' . $lead["firstName"] . '","email":"' . $lead["email"] . '"}}';

// init cURL
$handle = curl_init('https://www.contactually.com/api/v1/contacts.json?api_key=' . $contactually_api_key);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

// exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
$contactually_response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code >= 200 || $code < 300)
$contactually_response = json_decode($contactually_response, true);
else
$error = $code;


}

?>
