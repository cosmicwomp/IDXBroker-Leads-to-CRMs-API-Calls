<?php

// access URL and request method
$url = 'https://api.idxbroker.com/leads/lead?interval=168&startDatetime=2014-10-22+23:59:59&dateType=subscribeDate';
$method = 'GET';

// headers (required and optional)
$headers = array(
'Content-Type: application/x-www-form-urlencoded', // required
'accesskey: nb3aPvidEaSFgt6ncJHtGw', // required - replace with your own
'outputtype: json' // optional - overrides the preferences in our API control page
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

//Zoho Generate Authentication token

$username = "testUsername";
$password = "testPassword";
$param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$username."&PASSWORD=".$password;
$ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
$result = curl_exec($ch);
/*This part of the code below will separate the Authtoken from the result. 
Remove this part if you just need only the result*/
$anArray = explode("\n",$result);
$authToken = explode("=",$anArray['2']);
$cmp = strcmp($authToken['0'],"AUTHTOKEN");
echo $anArray['2'].""; if ($cmp == 0)
{
echo "Created Authtoken is : ".$authToken['1'];
return $authToken['1'];
}
curl_close($ch);

?>
