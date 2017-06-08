<?php
require_once getcwd() . '/header.php';
use Bigcommerce\Api\Client as Bigcommerce;

if (!function_exists('transformSimpleProduct')) {
	
    function transformSimpleProduct($product)
    {
      global $storeCurrency, $storeDomain;
     
      $outOfStock = false;
      if ($product->availability == "available") {
      	$outOfStock = true;
      } else {
      	if ($product->inventory_tracking != "none") {
	      $outOfStock = ($product->inventory_level === 0
	                    ? true
	                    : false);
      	}
      }

      $primaryImg = $product->primary_image;
      $productImage = ($primaryImg->standard_url != null) ? $primaryImg->standard_url : "";
      $productImageThumb = ($primaryImg->thumbnail_url != null) ? $primaryImg->thumbnail_url : "";
      
      $formattedProduct = new \Swagger\Client\Model\Product();
      $productVariant = new \Swagger\Client\Model\ProductVariants();

      $productCategories = implode(",", $product->categories);
      $formattedProduct->setCategories($productCategories);
      $formattedProduct->setCurrency($storeCurrency);
      
      $formattedProduct->setHandle($storeDomain . $product->custom_url);
      $formattedProduct->setImageSourceUrl($productImage);
      $formattedProduct->setImageThubnail($productImageThumb);
      $formattedProduct->setIsDownloadable($product->type == "digital" ? true : false);
      $formattedProduct->setIsVirtual($product->type == "digital" ? true : false);
      $formattedProduct->setOutStock($outOfStock);
      $formattedProduct->setPrice($product->price ? floatval($product->price) : 0.00);
      $formattedProduct->setPriceCompare(0.00);
      
      //$productTags = explode(",", $product['tags']);
      //$formattedProduct->setProductTags($productTags);
      
      $dateCreated = dateConvert($product->date_created);
      $formattedProduct->setPublishedAt($dateCreated);
      
      $formattedProduct->setRefId($product->id . "");
      $formattedProduct->setSku($product->sku);
      $formattedProduct->setSource("bigcommerce");
      $formattedProduct->setTags("tags");
      $formattedProduct->setTitle($product->name);
      $formattedProduct->setType($product->type);
      $formattedProduct->setUserId("");      
      $formattedProduct->setVendor("vendor");

      $formattedProduct->variants = array();

      $productVariant->setAttributes(array());
      $productVariant->setCreated($dateCreated);
      $productVariant->setImageSourceUrl($productImage);
      $productVariant->setInStock(!$outOfStock);
      $productVariant->setInventoryManagement($product->inventory_tracking || "");
      $productVariant->setInventoryQuantity($product->inventory_level . "");
      $productVariant->option1 = "";
      $productVariant->setPrice($product->price ? floatval($product->price) : 0.00);
      $productVariant->setPriceCompare(0.00);
      $productVariant->setProductRefId($product->id . "");
      $productVariant->setRefId($product->id . "");
      $productVariant->setSku($product->sku . "");
      $productVariant->setTaxable($product->tax_class_id);
      $productVariant->setTitle($product->name);
      
      $dateModified = dateConvert($product->date_modified);
      $productVariant->setUpdated($dateModified);
      $productVariant->setVisible($product->is_visible);
      $productVariant->setWeight($product->weight);

      array_push($formattedProduct->variants, $productVariant);

      return $formattedProduct;
    }
}


if (!function_exists('transformComplexProduct')) {
    function transformComplexProduct($product)
    {
      global $storeCurrency, $storeDomain;
      
      $outOfStock = false;
      if ($product->availability == "available") {
      	$outOfStock = true;
      } else {
      	if ($product->inventory_tracking != "none") {
	      $outOfStock = ($product->inventory_level === 0
	                    ? true
	                    : false);
      	}
      }

      $primaryImg = $product->primary_image;
      $productImage = ($primaryImg->standard_url != null) ? $primaryImg->standard_url : "";
      $productImageThumb = ($primaryImg->thumbnail_url != null) ? $primaryImg->thumbnail_url : "";

      $formattedProduct = new \Swagger\Client\Model\Product();
      
      $productCategories = implode(",", $product->categories);
      $formattedProduct->setCategories($productCategories);
      $formattedProduct->setCurrency($storeCurrency);
      
      $formattedProduct->setHandle($storeDomain . $product->custom_url);
      $formattedProduct->setImageSourceUrl($productImage);
      $formattedProduct->setImageThubnail($productImageThumb);
      $formattedProduct->setIsDownloadable($product->type == "digital" ? true : false);
      $formattedProduct->setIsVirtual($product->type == "digital" ? true : false);
      $formattedProduct->setOutStock($outOfStock);
      $formattedProduct->setPrice($product->price ? floatval($product->price) : 0.00);
      $formattedProduct->setPriceCompare(0.00);
      
//       $productTags = explode(",", $product['tags']);
//       $formattedProduct->setProductTags($productTags);

      
      $dateCreated = dateConvert($product->date_created);
      $formattedProduct->setPublishedAt($dateCreated);
      
      $formattedProduct->setRefId($product->id . "");
      $formattedProduct->setSku($product->sku);
      $formattedProduct->setSource("bigcommerce");
      $formattedProduct->setTags("tags");
      $formattedProduct->setTitle($product->name);
      $formattedProduct->setType($product->type);
      $formattedProduct->setUserId("");
      $formattedProduct->setVendor("vendor");
    
      $formattedProduct->variants = array();

      foreach ($product->skus as $variant) {

        $productVariant = new \Swagger\Client\Model\ProductVariants();

        $productVariant->setAttributes(array());
        $productVariant->setCreated($dateCreated);        
        
        $productVariant->setImageSourceUrl($variant->image_file ? $variant->image_file : $productImage); //todo
        $productVariant->setInStock(!$outOfStock);
        $productVariant->setInventoryManagement($product->inventory_tracking || "");
        $productVariant->setInventoryQuantity($variant->inventory_level . "");
        $productVariant->setPrice($variant->price ? floatval($variant->price) : floatval($variant->adjusted_price));
        $productVariant->setPriceCompare(0.00);
        $productVariant->setProductRefId($variant->product_id . "");
        $productVariant->setRefId($variant->id . "");
        $productVariant->setSku($variant->sku . "");
        $productVariant->setTaxable($product->tax_class_id);
        $productVariant->setTitle($product->name);
        
        $dateModified = dateConvert($product->date_modified);
	    $productVariant->setUpdated($dateModified);
	    $productVariant->setVisible($product->is_visible);
	    $productVariant->setWeight($variant->weight ? $variant->weight : $product->weight);

        array_push($formattedProduct->variants, $productVariant);
      }

      return $formattedProduct;
    }
}

if (!function_exists('transformCustomer')) {
    function transformCustomer($customer)
    {
      $addresses = $customer->addresses;
    	
      $areaCode = "000";
      
      if (!isset($customer->phone) || $customer->phone == null) {
      	$customer->phone = "0000000000";
      } else {
      	$customer->phone = preg_replace('/\D+/', "", $customer->phone);
     
      	if (strpos($customer->phone, "1") === 1) {
      	  $customer->phone = ltrim($customer->phone, "1");
      	}
      	$customer->phone = ltrim($customer->phone, "+1");
      	$areaCode = substr($customer->phone, 0, 3);
      }

      $formattedCustomer = new \Swagger\Client\Model\Customer();
      
      $dateCreated = dateConvert($customer->date_created);
      $dateModified = dateConvert($customer->date_modified);

      $formattedCustomer->setActivationDate($dateCreated);
      $formattedCustomer->setAreaCode($areaCode);    
      $formattedCustomer->setAddress1($addresses[0]->street_1);
      $formattedCustomer->setAddress2($addresses[0]->street_2);
      $formattedCustomer->setCity($addresses[0]->city);
      $formattedCustomer->setCompany($addresses[0]->company ? $addresses[0]->company : "");
      $formattedCustomer->setCountry($addresses[0]->country);
      $formattedCustomer->setProvince($addresses[0]->state);
      $formattedCustomer->setZip($addresses[0]->zip);      
      $formattedCustomer->setEmail($customer->email);
      $formattedCustomer->setFirstName($customer->first_name);
      $formattedCustomer->setLastName($customer->last_name);
      $formattedCustomer->setModifiedDate($dateModified);
      $formattedCustomer->setOrdersCount(0);
      $formattedCustomer->setPhone($customer->phone ? $customer->phone : "");      
      $formattedCustomer->setRefId($customer->id . "");
      $formattedCustomer->setSignedUpAt($dateCreated);
      $formattedCustomer->setTotalSpent(true ? floatval(0) : 0.00);
      $formattedCustomer->setVerified(false);       
     
      return $formattedCustomer;
    }
}

if (!function_exists('transformOrder')) {
    function transformOrder($order)
    {

      $orderProducts = $order->products;
    
      $dateCreated = dateConvert($order->date_created);
      $dateModified = dateConvert($order->date_modified);
    	
      $totalShipping = $order->shipping_cost_inc_tax;
    	 
      //$order['line_items'] = array_map("addProductReference", $order['line_items']);

      $formattedOrder = new \Swagger\Client\Model\Order();

      $formattedOrder->cart_token = "-";
      $formattedOrder->contact = $order->customer_id;
      $formattedOrder->setContactRefId($order->customer_id . "");
      $formattedOrder->email = "email";
      $formattedOrder->setFinancialStatus($order->payment_method);
      $formattedOrder->integration_id = "";
      $formattedOrder->setIqxOrder("");
      $formattedOrder->setProcessedAt($dateCreated);
      $formattedOrder->setRefId($order->id . "");
      $formattedOrder->setSubtotalPrice($order->subtotal_ex_tax ? floatval($order->subtotal_ex_tax) : 0.00);
      $formattedOrder->setTotalPrice($order->total_inc_tax ? floatval($order->total_inc_tax) : 0.00);
      $formattedOrder->setTotalShipping($totalShipping ? floatval($totalShipping) : 0.00);
      $formattedOrder->setTotalTax($order->total_tax ? floatval($order->total_tax) : 0.00);
      $formattedOrder->setUserId("");

      $lineItemsArray = array();

      foreach ($orderProducts as $key => $lineItem) {

        $orderLineItem = new \Swagger\Client\Model\OrderLineItems();

        $orderLineItem->setFulfillableQuantity($lineItem->quantity ? intVal($lineItem->quantity) : 0);
        $orderLineItem->setPrice($lineItem->base_price ? floatval($lineItem->base_price) : 0.00);
        $orderLineItem->setOrderPrice(0.00);
        $orderLineItem->setSku($lineItem->sku);
        $orderLineItem->setName($lineItem->name);
        $orderLineItem->setTitle($lineItem->name);
        $orderLineItem->setQuantity(intVal($lineItem->quantity));
        $orderLineItem->setGrams($lineItem->weight ? intVal($lineItem->weight) : 0);
        $orderLineItem->setRequiresShipping(false);
        $orderLineItem->setProductRefId($lineItem->product_id . "");

        array_push($lineItemsArray, $orderLineItem);
      }

      $formattedOrder->setLineItems($lineItemsArray);

      return $formattedOrder;
    }
}

if (!function_exists('dateConvert')) {
	function dateConvert($obj) {
		$datetime = new DateTime($obj);
		$dateCreated = $datetime->format(DateTime::ATOM);
		
		return $dateCreated;	
	}
}
