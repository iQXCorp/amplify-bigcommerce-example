<?php
require_once getcwd() . '/helper.php';
use Bigcommerce\Api\Client as Bigcommerce;

$products = Bigcommerce::getProducts();
// if ($products) {
// 	foreach ($products as $product) {
// 		echo $product->name;
// 		echo ",";
// 		echo $product->price;
// 		 echo "<pre>";
// 		 if ($product->name == "[Sample] Smith Journal 13") {
// 		 	print_r($product);
		 	
// 		 	$skus = $product->skus;
// 		 	print_r($skus);
		 	
// 		 }
// 		 echo "</pre>";
// 		echo "<hr>";
// 	}
// } else {
// 	echo "no products";
// }


$customers = Bigcommerce::getCustomers();
// if ($customers) {
// 	foreach ($customers as $customer) {
// 		echo $customer->company;
// echo ",";
// 		echo $customer->email;
// 		echo "<pre>";
// 		print_r($customer);
// 		echo "</pre>";
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
// 		echo "<pre>";
// 		print_r($order);
// 		echo "</pre>";
// 		echo "<hr>";
// 	}
// } else {
// 	echo "no orders";
// }


foreach ($customers as $key => $customer) {
  $transformedCustomer = transformCustomer($customer); 
	
  try {
      $result = $customer_api_instance->createOrUpdateCustomer($transformedCustomer);
//       echo "<pre>";
//       print_r($result);
//       echo "</pre>";
  } catch (Exception $e) {
  	
	  echo "ERROR CUSTOMERS:<pre>";
	  print_r($e);
	  echo "</pre>";
  	
      echo '<hr>Exception when calling CustomersApi->createOrUpdateCustomer: ', $e->getMessage(), PHP_EOL;
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
      //print_r($result);
  } catch (Exception $e) {
  	
  	echo "ERROR PRODUCTS:<pre>";
  	print_r($e);
  	echo "</pre>";
    echo '<hr>Exception when calling ProductsApi->createOrUpdateProduct: ', $e->getMessage(), PHP_EOL;
  }

}

foreach ($orders as $key => $order) {

  $transformedOrder = transformOrder($order);

  try {
    $result = $order_api_instance->createOrUpdateOrder($transformedOrder);
//     echo "PRINT RESULTS:";
//     echo "<pre>";
//     print_r($result);
//     echo "</pre>";
  } catch (Exception $e) {
  	echo "ERROR ORDERS:<pre>";
  	print_r($e);
  	echo "</pre>";
    echo 'Exception when calling OrdersApi->createOrUpdateOrder: ', $e->getMessage(), PHP_EOL;
  }

}


// if in test mode wipe all webhooks from the store
if ($inTestMode) {
  	
  try {
  	$webhooksForDelete = Bigcommerce::listWebhooks();
      
  } catch (Exception $e) {
    echo "ERROR webhooks:<pre>";
    print_r($e);
    echo "</pre>";
    echo 'Exception when calling Bigcommerce::deleteWebhook: ', $e->getMessage(), PHP_EOL;
  }
//     echo "LIST HOOKS start-";
//     echo "<pre>";
//     print_r($webhooksForDelete);
//     echo "</pre>";
//     echo "-end";
//     echo "<hr>";

  if ($webhooksForDelete) {
	foreach ($webhooksForDelete as $hook) {
	
	  try {
	    $result = Bigcommerce::deleteWebhook($hook->id);
// 	        echo "DELETE HOOKS:<pre>";
// 			print_r($result);
// 	        echo "</pre>";
	        
	  } catch (Exception $e) {
	    echo 'Exception when calling Bigcommerce::deleteWebhook: ', $e->getMessage(), PHP_EOL;
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
      
          echo "<pre>";
          print_r($obj);
          echo "</pre>";      
      
    $result = Bigcommerce::createWebhook(json_encode($obj));           
      
//       echo "Created Hook: " . $webhookType;
//       echo "<pre>";
//       print_r($result);
//       echo "</pre>";
      
  } catch (Exception $e) {
	echo "<pre>";
    print_r($e);
    echo "</pre>";
    echo 'Exception when calling Bigcommerce::createWebhook: ', $e->getMessage(), PHP_EOL;
  }
}
  
//   $webhooksForDelete = Bigcommerce::listWebhooks();
//   echo "LIST HOOKS start-";
//   echo "<pre>";
//   print_r($webhooksForDelete);
//   echo "</pre>";
//   echo "-end";
//   echo "<hr>";
?>
