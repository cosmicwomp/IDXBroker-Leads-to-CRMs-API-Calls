<?php


//define some variables for both calls
$idx_broker_api_key = 'YourAPIKey';
$followupboss_api_key = 'YourFollowUpBossAPIKey';
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

//call to FollowUpBoss in a similar fashion as the IDX Broker call

foreach ($response as $lead){

  $data = array(
    'source' => 'IDXBroker',
    'type' => 'Registration',
    'person' => array(
      'firstName' => $lead["firstName"],
      'lastName' => $lead["lastName"],
      'emails' => array(array('value' => $lead["email"]))
    )
  );
  // init cURL
  $ch = curl_init('https://api.followupboss.com/v1/events');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch, CURLOPT_USERPWD, $followupboss_api_key . ':');
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

  // exec the cURL request and returned information. Store the returned HTTP code in $code for later reference
  $FollowUpBoss_response = curl_exec($handle);
  $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

  if ($code >= 200 || $code < 300)
  $FollowUpBoss_response = json_decode($FollowUpBoss_response,true);
  else
  $error = $code;

}
?>
