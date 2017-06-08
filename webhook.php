<?php
$webhook_content = '';
$webhook = fopen('php://input' , 'rb');
while(!feof($webhook)){ //loop through the input stream while the end of file is not reached
    $webhook_content .= fread($webhook, 4096); //append the content on the current iteration
}
fclose($webhook); //close the resource

$data = json_decode($webhook_content, true); // convert the json to array

$scope = $data["scope"];
$obj = $data["data"];
$id = $obj["id"];

// simple way to debug...
// file_put_contents("amplify.txt", $scope . "-". "$id", FILE_APPEND);

require_once getcwd() . '/helper.php';
use Bigcommerce\Api\Client as Bigcommerce;

switch ($scope) {
	// customers
	case "store/customer/created":
	case "store/customer/updated":
		
		$customer = Bigcommerce::getCustomer($id);
		
		if ($customer) {
			$transformedCustomer = transformCustomer($customer);
			
			try {
				$result = $customer_api_instance->createOrUpdateCustomer($transformedCustomer);
				echo "Webhook triggered:";
				print_r($result);
				 
				// reply to server with 200 code
				replyOK();
				 
			} catch (Exception $e) {
				echo 'Exception when calling CustomersApi->createOrUpdateCustomer: ', $e->getMessage(), PHP_EOL;
			}
		}	    
	    break;
  
	// products
  	case "store/product/created":
  	case "store/product/updated":
  	
  		$product = Bigcommerce::getProduct($id);
  	
	  	if ($product) {
		  	if (count($product->skus) > 1) {
	        	$transformedProduct = transformComplexProduct($product);
	      	} else {
	        	$transformedProduct = transformSimpleProduct($product);
	      	}
	      	
	      	try {
	      		$result = $product_api_instance->createOrUpdateProduct($transformedProduct);
	      		print_r($result);
	      		 
	      		// reply to server with 200 code
	      		replyOK();
	      	} catch (Exception $e) {
	      		echo 'Exception when calling ProductsApi->createOrUpdateProduct: ', $e->getMessage(), PHP_EOL;
	      	}
	  	}      	
      	break;
  
	// orders
	case "store/order/updated":
		
		$order = Bigcommerce::getOrder($id);
		
		if ($order) {
			$transformedOrder = transformOrder($order);
			
			try {
				$result = $order_api_instance->createOrUpdateOrder($transformedOrder);
				print_r($result);
				 
				// reply to server with 200 code
				replyOK();
			} catch (Exception $e) {
				echo 'Exception when calling OrdersApi->createOrUpdateOrder: ', $e->getMessage(), PHP_EOL;
			}
		}        	
      	break;
}

/*
 * To acknowledge that you received the webhook without issue, 
 * your server should return a 200 HTTP status code. 
 */
function replyOK() {	
	// echoing out an empty json string (as example) would result in a 200 OK status
	echo json_encode(array());
	
	// or alternatively
	//header("Status: 200");
}
?>