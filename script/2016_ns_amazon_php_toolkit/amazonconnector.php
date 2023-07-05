<?php               
include_once ('.config.inc.php');
include_once '../PHPToolkit/NetSuiteService.php';
require_once ('ListOrderItems.php');   
    
                     
$serviceUrl = "https://mws.amazonservices.com/Orders/2011-01-01";                            

$config = array (
    'ServiceURL' => $serviceUrl,
    'ProxyHost' => null,
    'ProxyPort' => -1,
    'MaxErrorRetry' => 3,
);

 $service = new MarketplaceWebServiceOrders_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);
 

 $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest(); 
 $request->setSellerId(MERCHANT_ID);

function getStateNameByAbbreviation($state){  
	if (strtoupper(trim($state))=="AK" || strtolower(trim($state))=="alaska"){ return "AK"; }  
	if (strtoupper(trim($state))=="AL" || strtolower(trim($state))=="alabama"){ return "AL"; }  
	if (strtoupper(trim($state))=="AR" || strtolower(trim($state))=="arkansas"){ return "AR"; }  
	if (strtoupper(trim($state))=="AZ" || strtolower(trim($state))=="arizona"){ return "AZ"; }  
	if (strtoupper(trim($state))=="CA" || strtolower(trim($state))=="california"){ return "CA"; }  
	if (strtoupper(trim($state))=="CO" || strtolower(trim($state))=="colorado"){ return "CO"; }  
	if (strtoupper(trim($state))=="CT" || strtolower(trim($state))=="connecticut"){ return "CT"; }  
	if (strtoupper(trim($state))=="DC" || strtolower(trim($state))=="district of columbia"){ return "DC"; }  
	if (strtoupper(trim($state))=="DE" || strtolower(trim($state))=="delaware"){ return "DE"; }  
	if (strtoupper(trim($state))=="FL" || strtolower(trim($state))=="florida"){ return "FL"; }  
	if (strtoupper(trim($state))=="GA" || strtolower(trim($state))=="georgia"){ return "GA"; }  
	if (strtoupper(trim($state))=="HI" || strtolower(trim($state))=="hawaii"){ return "HI"; }  
	if (strtoupper(trim($state))=="IA" || strtolower(trim($state))=="iowa"){ return "IA"; }  
	if (strtoupper(trim($state))=="ID" || strtolower(trim($state))=="idaho"){ return "ID"; }  
	if (strtoupper(trim($state))=="IL" || strtolower(trim($state))=="illinois"){ return "IL"; }  
	if (strtoupper(trim($state))=="IN" || strtolower(trim($state))=="indiana"){ return "IN"; }  
	if (strtoupper(trim($state))=="KS" || strtolower(trim($state))=="kansas"){ return "KS"; }  
	if (strtoupper(trim($state))=="KY" || strtolower(trim($state))=="kentucky"){ return "KY"; }  
	if (strtoupper(trim($state))=="LA" || strtolower(trim($state))=="louisiana"){ return "LA"; }  
	if (strtoupper(trim($state))=="MA" || strtolower(trim($state))=="massachusetts"){ return "MA"; }  
	if (strtoupper(trim($state))=="MD" || strtolower(trim($state))=="maryland"){ return "MD"; }  
	if (strtoupper(trim($state))=="ME" || strtolower(trim($state))=="maine"){ return "ME"; }  
	if (strtoupper(trim($state))=="MI" || strtolower(trim($state))=="michigan"){ return "MI"; }  
	if (strtoupper(trim($state))=="MN" || strtolower(trim($state))=="minnesota"){ return "MN"; }  
	if (strtoupper(trim($state))=="MO" || strtolower(trim($state))=="missouri"){ return "MO"; }  
	if (strtoupper(trim($state))=="MS" || strtolower(trim($state))=="mississippi"){ return "MS"; }  
	if (strtoupper(trim($state))=="MT" || strtolower(trim($state))=="montana"){ return "MT"; }  
	if (strtoupper(trim($state))=="NC" || strtolower(trim($state))=="north carolina"){ return "NC"; }  
	if (strtoupper(trim($state))=="ND" || strtolower(trim($state))=="north dakota"){ return "ND"; }  
	if (strtoupper(trim($state))=="NE" || strtolower(trim($state))=="nebraska"){ return "NE"; }  
	if (strtoupper(trim($state))=="NH" || strtolower(trim($state))=="new hampshire"){ return "NH"; }  
	if (strtoupper(trim($state))=="NJ" || strtolower(trim($state))=="new jersey"){ return "NJ"; }  
	if (strtoupper(trim($state))=="NM" || strtolower(trim($state))=="new mexico"){ return "NM"; }  
	if (strtoupper(trim($state))=="NV" || strtolower(trim($state))=="nevada"){ return "NV"; }  
	if (strtoupper(trim($state))=="NY" || strtolower(trim($state))=="new york"){ return "NY"; }  
	if (strtoupper(trim($state))=="OH" || strtolower(trim($state))=="ohio"){ return "OH"; }  
	if (strtoupper(trim($state))=="OK" || strtolower(trim($state))=="oklahoma"){ return "OK"; }  
	if (strtoupper(trim($state))=="OR" || strtolower(trim($state))=="oregon"){ return "OR"; }  
	if (strtoupper(trim($state))=="PA" || strtolower(trim($state))=="pennsylvania"){ return "PA"; }  
	if (strtoupper(trim($state))=="RI" || strtolower(trim($state))=="rhode island"){ return "RI"; }  
	if (strtoupper(trim($state))=="SC" || strtolower(trim($state))=="south carolina"){ return "SC"; }  
	if (strtoupper(trim($state))=="SD" || strtolower(trim($state))=="south dakota"){ return "SD"; }  
	if (strtoupper(trim($state))=="TN" || strtolower(trim($state))=="tennessee"){ return "TN"; }  
	if (strtoupper(trim($state))=="TX" || strtolower(trim($state))=="texas"){ return "TX"; }  
	if (strtoupper(trim($state))=="UT" || strtolower(trim($state))=="utah"){ return "UT"; }  
	if (strtoupper(trim($state))=="VA" || strtolower(trim($state))=="virginia"){ return "VA"; }  
	if (strtoupper(trim($state))=="VT" || strtolower(trim($state))=="vermont"){ return "VT"; }  
	if (strtoupper(trim($state))=="WA" || strtolower(trim($state))=="washington"){ return "WA"; }  
	if (strtoupper(trim($state))=="WI" || strtolower(trim($state))=="wisconsin"){ return "WI"; }  
	if (strtoupper(trim($state))=="WV" || strtolower(trim($state))=="west virginia"){ return "WV"; }  
	if (strtoupper(trim($state))=="WY" || strtolower(trim($state))=="wyoming"){ return "WY"; }  
}                                                                                               
  
 
$time_before_200 = time() - 4000;
$time_before_4000 = time() - 15*24*60*60; 
$date_before = date('Y-m-d H:i:s', $time_before_200);
$date_after = date('Y-m-d H:i:s', $time_before_4000); 
$request->setCreatedAfter(new DateTime($date_after, new DateTimeZone('UTC')));        
$request->setCreatedBefore(new DateTime($date_before, new DateTimeZone('UTC')));

 // Set the marketplaces queried in this ListOrdersRequest
 $marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
 $marketplaceIdList->setId(array(MARKETPLACE_ID));
 $request->setMarketplaceId($marketplaceIdList);

 // Set the order statuses for this ListOrdersRequest (optional)
 // $orderStatuses = new MarketplaceWebServiceOrders_Model_OrderStatusList();
 // $orderStatuses->setStatus(array('Shipped'));
 // $request->setOrderStatus($orderStatuses);

 // Set the Fulfillment Channel for this ListOrdersRequest (optional)
 //$fulfillmentChannels = new MarketplaceWebServiceOrders_Model_FulfillmentChannelList();
 //$fulfillmentChannels->setChannel(array('MFN'));
 //$request->setFulfillmentChannel($fulfillmentChannels);
 
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrdersRequest
 // object or array of parameters
 invokeListOrders($service, $request); 
                                        
/**
  * List Orders Action Sample
  * ListOrders can be used to find orders that meet the specified criteria.
  *   
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
  */
  function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request) 
  {
      Global $itemlist_array;
      try {
              $service_net = new NetSuiteService();
              $response = $service->listOrders($request);        
                if ($response->isSetListOrdersResult()) {         
                    $listOrdersResult = $response->getListOrdersResult();          
                    if ($listOrdersResult->isSetOrders()) {       
                        $orders = $listOrdersResult->getOrders();
                        $orderList = $orders->getOrder();
                        foreach ($orderList as $order) {
                            if ($order->isSetOrderStatus()) 
                            {
                                $failed_order_number = 0;
                                echo("                        OrderStatus\n");
                                echo("                            " . $order->getOrderStatus() . "\n");
                                echo("                            " . $order->getAmazonOrderId() . "\n");
                                if ($order->getOrderStatus() != 'Canceled'){     
                                    $search_external_order = new TransactionSearch();                                       
                                    $extenalorderField = new SearchStringCustomField();
                                    $extenalorderField->searchValue = $order->getAmazonOrderId();
                                    $extenalorderField->internalId = 'custbody_external_order_num';
                                    $extenalorderField->operator = "startsWith";    
                                    $search_external_order->basic->customFieldList->customField = array($extenalorderField);                            
                                    $request_external_order = new SearchRequest();
                                    $request_external_order->searchRecord = $search_external_order; 
                                    $searchResponse_external_order = $service_net->search($request_external_order);
                                    if (!$searchResponse_external_order->searchResult->recordList->record['0']->internalId) {          
                                        if ($order->isSetAmazonOrderId()) 
                                        {
                                             $request_listOrderItem = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();                
                                             $request_listOrderItem->setSellerId(MERCHANT_ID);
                                             $request_listOrderItem->withAmazonOrderId($order->getAmazonOrderId());               
                                             invokeListOrderItems($service, $request_listOrderItem);
                                            echo("                        AmazonOrderId\n");
                                            echo("                            " . $order->getAmazonOrderId() . "\n");
                                        }
                                        if ($order->isSetSellerOrderId()) 
                                        {
                                            echo("                        SellerOrderId\n");
                                            echo("                            " . $order->getSellerOrderId() . "\n");
                                        }
                                        if ($order->isSetPurchaseDate()) 
                                        {
                                            echo("                        PurchaseDate\n");
                                            echo("                            " . $order->getPurchaseDate() . "\n");
                                        }
                                        if ($order->isSetLastUpdateDate()) 
                                        {
                                            echo("                        LastUpdateDate\n");
                                            echo("                            " . $order->getLastUpdateDate() . "\n");
                                        }
                                        if ($order->isSetOrderStatus()) 
                                        {
                                            echo("                        OrderStatus\n");
                                            echo("                            " . $order->getOrderStatus() . "\n");
                                        }
                                        if ($order->isSetFulfillmentChannel()) 
                                        {
                                            echo("                        FulfillmentChannel\n");
                                            echo("                            " . $order->getFulfillmentChannel() . "\n");
                                        }
                                        if ($order->isSetSalesChannel()) 
                                        {
                                            echo("                        SalesChannel\n");
                                            echo("                            " . $order->getSalesChannel() . "\n");
                                        }
                                        if ($order->isSetOrderChannel()) 
                                        {
                                            echo("                        OrderChannel\n");
                                            echo("                            " . $order->getOrderChannel() . "\n");
                                        }
                                        if ($order->isSetShipServiceLevel()) 
                                        {
                                            echo("                        ShipServiceLevel\n");
                                            echo("                            " . $order->getShipServiceLevel() . "\n");
                                        }
             
                                        if ($order->isSetOrderTotal()) { 
                                            echo("                        OrderTotal\n");
                                            $orderTotal = $order->getOrderTotal();
                                            if ($orderTotal->isSetCurrencyCode()) 
                                            {
                                                echo("                            CurrencyCode\n");
                                                echo("                                " . $orderTotal->getCurrencyCode() . "\n");
                                            }
                                            if ($orderTotal->isSetAmount()) 
                                            {
                                                echo("                            Amount\n");
                                                echo("                                " . $orderTotal->getAmount() . "\n");
                                            }
                                        } 
                                        if ($order->isSetNumberOfItemsShipped()) 
                                        {
                                            echo("                        NumberOfItemsShipped\n");
                                            echo("                            " . $order->getNumberOfItemsShipped() . "\n");
                                        }
                                        if ($order->isSetNumberOfItemsUnshipped()) 
                                        {
                                            echo("                        NumberOfItemsUnshipped\n");
                                            echo("                            " . $order->getNumberOfItemsUnshipped() . "\n");
                                        }
                                        if ($order->isSetPaymentExecutionDetail()) { 
                                            echo("                        PaymentExecutionDetail\n");
                                            $paymentExecutionDetail = $order->getPaymentExecutionDetail();
                                            $paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
                                            foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
                                                echo("                            PaymentExecutionDetailItem\n");
                                                if ($paymentExecutionDetailItem->isSetPayment()) { 
                                                    echo("                                Payment\n");
                                                    $payment = $paymentExecutionDetailItem->getPayment();
                                                    if ($payment->isSetCurrencyCode()) 
                                                    {
                                                        echo("                                    CurrencyCode\n");
                                                        echo("                                        " . $payment->getCurrencyCode() . "\n");
                                                    }
                                                    if ($payment->isSetAmount()) 
                                                    {
                                                        echo("                                    Amount\n");
                                                        echo("                                        " . $payment->getAmount() . "\n");
                                                    }
                                                } 
                                                if ($paymentExecutionDetailItem->isSetPaymentMethod()) 
                                                {
                                                    echo("                                PaymentMethod\n");
                                                    echo("                                    " . $paymentExecutionDetailItem->getPaymentMethod() . "\n");
                                                }
                                            }
                                        }  
                                        if ($order->isSetBuyerEmail()) 
                                        {                                                                   
                                            $service_net->setSearchPreferences(false, 20);                                                                                               
                                            $search_customer = new CustomerSearch();
                                            $search_customer->basic->email->operator = "startsWith";               
                                            $search_customer->basic->email->searchValue = $order->getBuyerEmail();
                                            $request_search_customer = new SearchRequest();
                                            $request_search_customer->searchRecord = $search_customer;
                                            $searchResponse = $service_net->search($request_search_customer);

                                            if (!$searchResponse->searchResult->recordList->record['0']->internalId) {    
                                                $customer = new Customer();
                                                if ($order->isSetBuyerName()) 
                                                {
                                                    $customer_name = explode(" ", $order->getBuyerName() );
                                                    if ( count($customer_name) == 3 ){
                                                         $customer->firstName = $customer_name[0];
                                                         $customer->middleName = $customer_name[1];
                                                         $customer->lastName = $customer_name[2];
                                                    }else if ( count($customer_name) == 2 )
                                                    {
                                                         $customer->firstName = $customer_name[0];
                                                         $customer->lastName = $customer_name[1];
                                                    }else if ( count($customer_name) > 3 ){
                                                        $customer->firstName = $customer_name[0];
                                                        $customer->middleName = $customer_name[1];
                                                        foreach ( $orderList as $order ){
                                                             $customer_over_name = $customer_name;
                                                             unset($customer_over_name[0]);
                                                             unset($customer_over_name[1]);
                                                             $customer->lastName = implode(" ", $customer_over_name);
                                                        } 
                                                    }else{
                                                         $customer->firstName = $order->getBuyerName();
                                                         $customer->lastName = 'unknown'; 
                                                    }                                 
                                                                                   
                                                }                                    
                                                $customer->isPerson = "individual";
                                                $bstringField = new BooleanCustomFieldRef();
                                                $bstringField->internalId = 'custentity_is_poolzoom_customer';
                                                $bstringField->value = 1;
                                                $customer->customFieldList = array($bstringField); 
                                                $customer->email = $order->getBuyerEmail();
                                                if ($order->isSetShippingAddress()) {                 
                                                    $shippingAddress = $order->getShippingAddress();
                                                    if ($shippingAddress->isSetName()) 
                                                    {
                                                        $customer->addressbookList->addressbook['addressee'] = $shippingAddress->getName();                               
                                                    }
                                                    if ($shippingAddress->isSetAddressLine1()) 
                                                    {   
                                                       $customer->addressbookList->addressbook['addr1'] = $shippingAddress->getAddressLine1();              
                                                    }
                                                    if ($shippingAddress->isSetAddressLine2()) 
                                                    {
                                                        $customer->addressbookList->addressbook['addr2'] = $shippingAddress->getAddressLine2();              
                                                    }
                                                    if ($shippingAddress->isSetCity()) 
                                                    {
                                                        $customer->addressbookList->addressbook['city'] = $shippingAddress->getCity();
                                                    }
                                                    if ($shippingAddress->isSetCounty()) 
                                                    {
                                                        $customer->addressbookList->addressbook['country'] = $shippingAddress->getCounty();       
                                                    }
                                                    if ($shippingAddress->isSetStateOrRegion()) 
                                                    {
														$state_name = $shippingAddress->getStateOrRegion();
														$abbreviation_state = getStateNameByAbbreviation($state_name);
                                                        $customer->addressbookList->addressbook['state'] = $abbreviation_state; 
                                                    }
                                                    if ($shippingAddress->isSetPostalCode()) 
                                                    {
                                                        $customer->addressbookList->addressbook['zip'] = $shippingAddress->getPostalCode();
                                                    }
                                                    if ($shippingAddress->isSetPhone()) 
                                                    {
                                                        $customer->phone = $shippingAddress->getPhone();
                                                        $customer->addressbookList->addressbook['phone'] = $shippingAddress->getPhone();                                                                                    
                                                    }
                                                    $customer->addressbookList->replaceAll = 1;
                                                }                                    
                                                $request_customer_add = new AddRequest();                                                       
                                                $request_customer_add->record = $customer;

                                                $addResponse_customer_add = $service_net->add($request_customer_add);

                                                if (!$addResponse_customer_add->writeResponse->status->isSuccess) {
                                                    echo "ADD ERROR";
                                                } else {
                                                    $customer_internal_id = $addResponse_customer_add->writeResponse->baseRef->internalId;
                                                }                                                   
                                            } else {                                                 
                                                $customer = new Customer(); 
                                                $customer->internalId = $searchResponse->searchResult->recordList->record['0']->internalId;  
                                                if ($order->isSetShippingAddress()) {                 
                                                    $shippingAddress = $order->getShippingAddress();
                                                    if ($shippingAddress->isSetName()) 
                                                    {
                                                        $customer->addressbookList->addressbook['addressee'] = $shippingAddress->getName();                               
                                                    }
                                                    if ($shippingAddress->isSetAddressLine1()) 
                                                    {   
                                                       $customer->addressbookList->addressbook['addr1'] = $shippingAddress->getAddressLine1();              
                                                    }
                                                    if ($shippingAddress->isSetAddressLine2()) 
                                                    {
                                                        $customer->addressbookList->addressbook['addr2'] = $shippingAddress->getAddressLine2();              
                                                    }
                                                    if ($shippingAddress->isSetCity()) 
                                                    {
                                                        $customer->addressbookList->addressbook['city'] = $shippingAddress->getCity();
                                                    }
                                                    if ($shippingAddress->isSetCounty()) 
                                                    {
                                                        $customer->addressbookList->addressbook['country'] = $shippingAddress->getCounty();       
                                                    }
                                                    if ($shippingAddress->isSetStateOrRegion()) 
                                                    {
                                                        $customer->addressbookList->addressbook['state'] = $shippingAddress->getStateOrRegion(); 
                                                    }
                                                    if ($shippingAddress->isSetPostalCode()) 
                                                    {
                                                        $customer->addressbookList->addressbook['zip'] = $shippingAddress->getPostalCode();
                                                    }
                                                    if ($shippingAddress->isSetPhone()) 
                                                    {
                                                        $customer->phone = $shippingAddress->getPhone();
                                                        $customer->addressbookList->addressbook['phone'] = $shippingAddress->getPhone();                                                                                    
                                                    }
                                                    $customer->addressbookList->replaceAll = 1; 
                                                }   
                                                $request_customer_update = new UpdateRequest();
                                                $request_customer_update->record = $customer;
                                                $addResponse_customer_update = $service_net->update($request_customer_update);
                                                if (!$addResponse_customer_update->writeResponse->status->isSuccess) {
                                                    echo "Update ERROR";
                                                } else {
                                                    $customer_internal_id = $addResponse_customer_update->writeResponse->baseRef->internalId;
                                                }                                                                                                          
                                            }                                                                       
                                        }                            
                                        if ($order->isSetShipmentServiceLevelCategory()) 
                                        {
                                            echo("                        ShipmentServiceLevelCategory\n");
                                            echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
                                        }
                                        if ($order->isSetShippedByAmazonTFM()) 
                                        {
                                            echo("                        ShippedByAmazonTFM\n");
                                            echo("                            " . $order->getShippedByAmazonTFM() . "\n");
                                        }
                                        if ($order->isSetTFMShipmentStatus()) 
                                        {
                                            echo("                        TFMShipmentStatus\n");
                                            echo("                            " . $order->getTFMShipmentStatus() . "\n");
                                        }                                                                                                                                             
                                        $so = new SalesOrder();
                                        $so->entity = new RecordRef();
                                        if ( $customer_internal_id ){
                                           $so->entity->internalId = $customer_internal_id; 
                                        }                                            
                                        $so->location = new RecordRef();
                                        $so->location->internalId = 1;
                                        $so->itemList = new SalesOrderItemList();                            
                                        $so->shipMethod->internalId = 71840;                            
                                        if ($order->isSetPurchaseDate()) 
                                        {
                                            $so->shipDate = $order->getPurchaseDate();                                            
                                        }
                                        
                                                                              

                                        $so->paymentMethod->internalId = 17;     
                                        $so->ccNumber = '4111111111111111';
                                        $so->ccExpireDate = '2014-01-01T00:57:03Z';                                          
                                        $so->ccApproved = 1;
                                        foreach( $itemlist_array as $item_array )
                                        {
                                            if ( isset($item_array['internal_id']) && !empty($item_array['internal_id']) ){
                                                $soi = new SalesOrderItem();
                                                $soi->item = new RecordRef();
                                                if ( $item_array['internal_id'] ) {   
                                                    $soi->item->internalId = $item_array['internal_id'];                                
                                                }                            
                                                $soi->quantity = $item_array['quantity_order'];    
                                                $svr = new getSelectValueRequest();
                                                $svr->fieldDescription = new GetSelectValueFieldDescription();
                                                $svr->pageIndex = 1;
                                                $priceFields = array(
                                                    'recordType'  => RecordType::salesOrder,
                                                    'sublist'    => 'itemList',
                                                    'field'    => 'price',
                                                    'filterByValueList'    => array(
                                                        'filterBy'    => array(
                                                            array(
                                                            'field'    => 'item',
                                                            'sublist'    => 'itemList',
                                                            'internalId'        => $item_array['internal_id'],
                                                        )
                                                            )
                                                    )
                                                );

                                                setFields($svr->fieldDescription, $priceFields);

                                                $gsv = $service_net->getSelectValue($svr);

                                                $id = null;
                                                foreach($gsv->getSelectValueResult->baseRefList->baseRef as $pricelevel) {
                                                    if ($pricelevel->name == 'Amazon Price') {
                                                        $id = $pricelevel->internalId;
                                                        break;
                                                    }
                                                }
                                                
                                                if ($id != null) {                                  
                                                } else {
                                                    foreach($gsv->getSelectValueResult->baseRefList->baseRef as $pricelevel) {
                                                        if ($pricelevel->name == 'Custom') {
                                                            $id = $pricelevel->internalId;
                                                            break;
                                                        }
                                                    }                                                      
                                                }
                                                $soi->price = new RecordRef();
                                                $soi->price->internalId = $id;
                                                $soi->amount = $item_array['total_amount'];
                                                $so->shippingCost += $item_array['shipping_cost']; 
                                                $so->itemList->item[] = $soi;      
                                            }else{
                                                $failed_order_number++;
                                                echo "<br>---------------------UNKNOWN ITEM FOUND. PLEASE CHECK IT.-------------------</br>";
                                                echo "<br>Amazon Order ID : " . $order->getAmazonOrderId();                                    
                                                $so->memo = "test order  *****" . $failed_order_number . "ITEM FAILED***** PLEASE CHECK";
                                                $to      = 'amazon.sales@poolzoom.com';
                                                $subject = 'FAILED ORDER Order ID: # ' . $order->getAmazonOrderId();
                                                $message = 'Found a order has problem, so fail to import.<br> please fix it. <br> amazon order id:' . $order->getAmazonOrderId();
                                                $headers = 'From: amazon.sales@poolzoom.com' . "\r\n" .
                                                'Reply-To: amazon.sales@poolzoom.com' . "\r\n" .
                                                'X-Mailer: PHP/' . phpversion();

                                                mail($to, $subject, $message, $headers);                                                
                                            }                                                      
                                        }
                                        $astringField1 = new StringCustomFieldRef();
                                        $astringField1->value = 'https://sellercentral.amazon.com/gp/orders-v2/details/ref=ag_orddet_cont_myo?ie=UTF8&orderID=' . $order->getAmazonOrderId();
                                        $astringField1->internalId = 'custbody_external_order_url';
                                        $astringField2 = new StringCustomFieldRef();
                                        $astringField2->value = $order->getAmazonOrderId();
                                        $astringField2->internalId = 'custbody_external_order_num';                                    
                                        if ($order->isSetLastUpdateDate()) 
                                        { 
                                            $astringField3 = new DateCustomFieldRef();
                                            $astringField3->internalId = 'custbody_external_order_datetime';
                                            $astringField3->value = $order->getLastUpdateDate();     
                                        }
                                        if ( $so->shippingCost ){
                                            $astringField4 = new StringCustomFieldRef();
                                            $astringField4->value = $so->shippingCost;
                                            $astringField4->internalId = 'custbody_imported_ship_cost';
                                        }
                                        if ($order->isSetShipmentServiceLevelCategory()) 
                                        {
                                            $astringField5 = new StringCustomFieldRef();
                                            $astringField5->value = $order->getShipmentServiceLevelCategory();
                                            $astringField5->internalId = 'custbody_imported_ship_method';
                                            echo("                        ShipmentServiceLevelCategory\n");
                                            echo("                            " . $order->getShipmentServiceLevelCategory() . "\n");
                                        }                                     
                                        $so->customFieldList->customField = array($astringField1,$astringField2,$astringField3,$astringField4,$astringField5);                                                                                  
                                         
                                       

                                        $request_add_so = new AddRequest();
                                        $request_add_so->record = $so;

                                        $addResponse_add_so = $service_net->add($request_add_so);

                                        if (!$addResponse_add_so->writeResponse->status->isSuccess) {
                                            echo "ADD ERROR";
                                            exit();
                                        } else {
                                            echo "ADD SUCCESS, id " . $addResponse_add_so->writeResponse->baseRef->internalId;
                                        }
                                    } 
                                     
                                }
                                
                            }
                        }             
                            
                    } 
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

              echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
        