<?php
require 'vendor/autoload.php';
use Bigcommerce\Api\Client as Bigcommerce;


// basic auth
// Bigcommerce::configure(array(
// 		'store_url' => 'https://all-points-south9.mybigcommerce.com',
// 		'username'	=> 'Aidan',
// 		'api_key'	=> 'b4b80dd35f674a7f612bbe636132c45b545fb648'
// ));


/*
$object = new \stdClass();
$object->client_id = 'kqegihaeawy0e97dqg7jt82puemivj2';
$object->client_secret = '27kqptn0mvjogn0erlgivkfl14karl0';
$object->redirect_uri = 'https://app.com/redirect';
$object->code = $_REQUEST['code'];
$object->context = $_REQUEST['context'];
$object->scope = $_REQUEST['scope'];
Bigcommerce::useJson();

$authTokenResponse = Bigcommerce::getAuthToken($object);

// configure BC App
Bigcommerce::configure([
		'client_id' => 'gr6fhjqedvbltbj5hsdz83gruqwaago',
		'auth_token' => $authTokenResponse->access_token,
		'store_hash' => 'qsksjq4qfu'
]);

Bigcommerce::verifyPeer(false);

// test
$ping = Bigcommerce::getTime();

if ($ping) {
	echo "time:";
	echo $ping->format('H:i:s');
	echo "----time end";
} else {
	echo "no ping";
}
*/

$object = new \stdClass();
$object->client_id = 'kqegihaeawy0e97dqg7jt82puemivj2';
$object->client_secret = '27kqptn0mvjogn0erlgivkfl14karl0';
$object->redirect_uri = 'https://app.com/redirect';
$object->code = $_GET["code"];
$object->context = $_GET["context"];
$object->scope = $_GET["scope"];

// print_r($object);
//$authTokenResponse = Bigcommerce::getAuthToken($object);

// Bigcommerce::configure(array(
// 		'client_id' => 'kqegihaeawy0e97dqg7jt82puemivj2',
// 		'auth_token' => 'td6r7myuznys254i1sr6j75z6cnfxpi',
// 		'store_hash' => 'qsksjq4qfu'
// ));

// oauth2 auth
Bigcommerce::configure(array(
		'client_id' => 'gr6fhjqedvbltbj5hsdz83gruqwaago',
		'auth_token' => '7unrv1n4nqmbkguhac0jgppgjip6xz5',
		'store_hash' => 'qsksjq4qfu'
));

Bigcommerce::verifyPeer(false);

// initial test
// $ping = Bigcommerce::getTime();

// if ($ping) {
// 	echo "time:";
// 	echo $ping->format('H:i:s');
// 	echo "----time end";
// } else {
// 	echo "no ping";
// }

// global values
$store = Bigcommerce::getStore();
if ($store) {
	$storeCurrency = $store->currency;
	$storeDomain = "http://" . $store->domain;
	$storeID = $store->id; // hash
	$storeName = $store->name;
} else {
	echo "<h1>API error</h1>";
	exit;
}

$inTestMode = true;
$amplify_api_key = "NTI5Yjg2YzUtZmRmZi00ZDdkLThiNzgtY2UxNmIwNDM2MjJjOjQ3NTU1OWRiLTE0ZWEtNGFlYi1hNDA0LWY2MzY2YTE2YmU3Yg==";
Swagger\Client\Configuration::getDefaultConfiguration()->setApiKey('authorization', $amplify_api_key);
Swagger\Client\Configuration::getDefaultConfiguration()->setSandboxMode(true);

// webhook url
$webhookURL = "https://bigc.pagekite.me/webhook.php";

// webhooks
$webhookTypes = array(
		'store/product/created',
		'store/product/updated',
		'store/customer/created',
		'store/customer/updated',
		'store/order/updated' );

$customer_api_instance = new Swagger\Client\Api\CustomersApi();
$product_api_instance = new Swagger\Client\Api\ProductsApi();
$order_api_instance = new Swagger\Client\Api\OrdersApi();
