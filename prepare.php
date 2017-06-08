<?php
require_once getcwd() . '/helper.php';
use Bigcommerce\Api\Client as Bigcommerce;

define('DEBUG', false);

function dump($obj, $show = false) {
  if (DEBUG || $show) {
    echo "<pre>";
	print_r($obj);
	echo "</pre>";
  }
}

$products = Bigcommerce::getProducts();
// if ($products) {
// 	foreach ($products as $product) {
// 		echo $product->name;
// 		echo ",";
// 		echo $product->price;
// 		echo "<hr>";
// 	}
// } else {
// 	echo "no products";
// }


$customers = Bigcommerce::getCustomers();
// if ($customers) {
// 	foreach ($customers as $customer) {
// 		echo $customer->company;
//      echo ",";
// 		echo $customer->email;
// 		dump($customer);
// 		echo "<hr>";
// 	}
// } else {
// 	echo "no customers";
// }

$orders = Bigcommerce::getOrders();
// if ($orders) {
// 	foreach ($orders as $order) {
// 		echo $order->customer_id;
// 		echo ",";
// 		echo $order->subtotal_ex_tax;
//      dump($order);
// 		echo "<hr>";
// 	}
// } else {
// 	echo "no orders";
// }


foreach ($customers as $key => $customer) {
  $transformedCustomer = transformCustomer($customer); 
	
  try {
      $result = $customer_api_instance->createOrUpdateCustomer($transformedCustomer);
	  dump($result);
  } catch (Exception $e) {
      echo '<hr>Exception when calling CustomersApi->createOrUpdateCustomer: ', $e->getMessage(), PHP_EOL;
      dump($e);
  }

}

foreach ($products as $key => $product) {
  if (count($product->skus) > 1) {
    $transformedProduct = transformComplexProduct($product);
  } else {
    $transformedProduct = transformSimpleProduct($product);
  }
  
  try {
      $result = $product_api_instance->createOrUpdateProduct($transformedProduct);
      dump($result);
  } catch (Exception $e) {
  	echo '<hr>Exception when calling ProductsApi->createOrUpdateProduct: ', $e->getMessage(), PHP_EOL;
    dump($e);
  }

}

foreach ($orders as $key => $order) {

  $transformedOrder = transformOrder($order);

  try {
    $result = $order_api_instance->createOrUpdateOrder($transformedOrder);
    dump($result);
  } catch (Exception $e) {
    echo 'Exception when calling OrdersApi->createOrUpdateOrder: ', $e->getMessage(), PHP_EOL;
    dump($e);
  }

}


// if in test mode wipe all webhooks from the store
if ($inTestMode) {
  	
  try {
  	$webhooksForDelete = Bigcommerce::listWebhooks();
      
  } catch (Exception $e) {    
    echo 'Exception when calling Bigcommerce::listWebhooks: ', $e->getMessage(), PHP_EOL;
    dump($e);
  }

  if ($webhooksForDelete) {
	foreach ($webhooksForDelete as $hook) {
	
	  try {
	    $result = Bigcommerce::deleteWebhook($hook->id);
	    dump($result);
	        
	  } catch (Exception $e) {
	    echo 'Exception when calling Bigcommerce::deleteWebhook: ', $e->getMessage(), PHP_EOL;
	    dump($e);
	  }
	}    
  }
}
  
foreach ($webhookTypes as $webhookType) {  	
  try {      
    $obj = array(
  			'scope'         => $webhookType,
  			'destination'   => $webhookURL . '?scope=' . $webhookType,
   			"is_active"     => true);   
      
    $result = Bigcommerce::createWebhook(json_encode($obj));           
    dump($result);
    
  } catch (Exception $e) {
    echo 'Exception when calling Bigcommerce::createWebhook: ', $e->getMessage(), PHP_EOL;
    dump($e);
  }
}
 
/*
 * Debug:
 */
//   $webhooksForDelete = Bigcommerce::listWebhooks();
//   echo "LIST HOOKS start-";
//   dump($webhooksForDelete);
//   echo "-end";
?>
