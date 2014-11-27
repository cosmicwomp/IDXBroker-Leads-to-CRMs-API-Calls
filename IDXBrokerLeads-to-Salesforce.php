<?php

//define some variables for both calls
$idx_broker_api_key = 'YourAPIKey';
$salesforce_username ='YourSalesforceUserName';
$salesforce_password = 'YourSalesforcePassword';
$salesforce_token = 'YourSalesforceSecurityToken';
//added this as the company as the Lead object does require the Company field, but this is not in by default in the IDX return
$IDX_as_Company = 'IDX Lead';
$interval = 1; //number of hours before $start_date see IDX Broker API docs for more on this
$start_date = '2014-11-01+23:59:59' //date to begin pulling leads

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

//connection to salesforce based on https://developer.salesforce.com/page/Getting_Started_with_the_Force.com_Toolkit_for_PHP

    define("USERNAME", $salesforce_username);
define("PASSWORD", $salesforce_password);
define("SECURITY_TOKEN", $salesforce_token);


//The force.com PHP is avaiable from salesforce at https://github.com/developerforce/Force.com-Toolkit-for-PHP

require_once ('Force.com-Toolkit-for-PHP-master/soapclient/SforceEnterpriseClient.php');

$mySforceConnection = new SforceEnterpriseClient();
$mySforceConnection->createConnection("Force.com-Toolkit-for-PHP-master/soapclient/enterprise.wsdl.xml");
$mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);



  //salesforce records to create array
  $records = array();

  //if there are more than 100 leads push them to this array but you may handle this differently
  $overflow = array();
  //set this at -1 and let it count up each loop to set index the in the array
  $array_num = -1;

  foreach ($response as $lead){

  if ($array_num == 100){
  //salesforce has a limit of 200 Leads added at one time setting the limit at 100 just because

  // this is simple handling for over 100 leads in the IDX Broker return

  //if this array gets any overflow you will likely need to save them for another API call
  array_push($overflow, $lead["firstName"], $lead["lastName"], $lead["email"]);

  }
  else {
// add 1 to the index
  $array_num = $array_num + 1;

  $records[$array_num] = new stdclass();
$records[$array_num]->FirstName = $lead["firstName"];
$records[$array_num]->LastName = $lead["lastName"];
$records[$array_num]->Email = $lead["email"];
$records[$array_num]->Company = $IDX_as_Company;
}

  };

$sf_response = $mySforceConnection->create($records, 'Lead');

?>
