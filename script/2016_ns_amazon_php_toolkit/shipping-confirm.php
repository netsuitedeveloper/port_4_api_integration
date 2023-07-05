<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebServiceOrders
 *  @copyright   Copyright 2008-2012 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2011-01-01
 */
/******************************************************************************* 
 * 
 *  Marketplace Web Service Orders PHP5 Library
 * 
 */

/**
 * Get Order  Sample
 */

include_once ('.config.inc.php');
include_once '../PHPToolkit/NetSuiteService.php';

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
      
$service_search_so = new NetSuiteService();
$service_search_so->setSearchPreferences(false, 80);
function get_web_page( $url,$curl_data )
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    $err     = curl_errno($ch);
    $errmsg  = curl_error($ch) ;
    $header  = curl_getinfo($ch);
    curl_close($ch); 
    return $content;
}

function save_array_to_file($filename,$b)
{
    if (!is_resource($filename))
    {
        if (!$file = fopen($filename,'w+')) return false;
    } else {
        $file = $filename;
    }
    foreach ($b as $key=>$val)
    {
        fwrite($file,(is_int($key) ? chr(6).(string)$key : chr(5).$key));
        if (is_array($val))
        {
            fwrite($file,chr(0)); //array starts
            save_array_to_file($file,$val);
            fwrite($file,chr(1)); //array ends
        }
        elseif (is_int($val))
        {
            fwrite($file,chr(2).(string) $val); //int
        }
        elseif (is_string($val))
        {
            fwrite($file,chr(3).$val); //string
        }
    }
    if (!is_resource($filename)) fclose($file);
    return true;
}
       
                  
$date_ago_month = date('Y-m-d\TH:i:s', time()-3600*24*20);  
echo $date;

$search_sales_order = new TransactionSearch();                                         
$search_sales_order->basic->shipDate->operator = 'after';
$search_sales_order->basic->shipDate->searchValue = $date_ago_month; 
$extenalorderField = new SearchStringCustomField();
$extenalorderField->searchValue = '-';
$extenalorderField->internalId = 'custbody_external_order_num';
$extenalorderField->operator = "contains";    
$search_sales_order->basic->customFieldList->customField = array($extenalorderField);                                              

$request = new SearchRequest();
$request->searchRecord = $search_sales_order; 
$searchResponse = $service_search_so->search($request);
$searchId = $searchResponse->searchResult->searchId;
$total_pages = $searchResponse->searchResult->totalPages;
Global $external_order; 
Global $tracking_number;
Global $shipping_date;
$tracking_number = array();
$external_order = array();
$shipping_date = array(); 
for ($num = 0; $num < count($searchResponse->searchResult->recordList->record); $num++){
    if ($searchResponse->searchResult->recordList->record[$num]->linkedTrackingNumbers){
        if(strpos($searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[5]->value , 'amazon')){                
            $tracking_number[] = $searchResponse->searchResult->recordList->record[$num]->linkedTrackingNumbers;
            $external_order[] = $searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[4]->value;
            $shipping_date[] = $searchResponse->searchResult->recordList->record[$num]->shipDate;
            echo $searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[4]->value;;    
        }
    } 
}
for ($pages = 1; $pages < $total_pages; $pages++){
    $request = new SearchMoreWithIdRequest();
    $request->pageIndex = $pages + 1;
    $request->searchId = $searchId;
    $searchResponse = $service_search_so->searchMoreWithId($request);
    for ($num = 0; $num < count($searchResponse->searchResult->recordList->record); $num++){
        echo '<br>' . $num;
        if ($searchResponse->searchResult->recordList->record[$num]->linkedTrackingNumbers){        
            if(strpos($searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[5]->value , 'amazon')){                
                $tracking_number[] = $searchResponse->searchResult->recordList->record[$num]->linkedTrackingNumbers;
                $external_order[] = $searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[4]->value;
                $shipping_date[] = $searchResponse->searchResult->recordList->record[$num]->lastModifiedDate;
                echo '<br>searched netsuite salesorder with tracking number' . $searchResponse->searchResult->recordList->record[$num]->linkedTrackingNumbers . '  ' . $searchResponse->searchResult->recordList->record[$num]->customFieldList->customField[4]->value;
            }
        } 
    }    
}                                                                                                    
                                                     

$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest(); 
$request->setSellerId(MERCHANT_ID);                                                               
$time_before_200 = time() + 6*60*60 - 200;
$time_before_4000 = time() - 3600*24*30; 
$date_before = date('Y-m-d\ H:i:s', $time_before_200);
$date_after = date('Y-m-d\ H:i:s', $time_before_4000);
echo $date_before ."<br>";
echo $date_after ."<br>";  
$request->setCreatedAfter(new DateTime($date_after, new DateTimeZone('UTC')));         
$request->setCreatedBefore(new DateTime($date_before, new DateTimeZone('UTC')));       
                                                                               
$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
$marketplaceIdList->setId(array(MARKETPLACE_ID));
$request->setMarketplaceId($marketplaceIdList);

invokeListOrders($service, $request); 
                                        

function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request) 
{
Global $external_order; 
Global $tracking_number;
Global $shipping_date;

$i = 0;                   
  try {                                        
          $response = $service->listOrders($request);        
            if ($response->isSetListOrdersResult()) {         
                $listOrdersResult = $response->getListOrdersResult();          
                if ($listOrdersResult->isSetOrders()) {       
                    $orders = $listOrdersResult->getOrders();
                    $orderList = $orders->getOrder();
                    foreach ($orderList as $order) {
                        if ($order->isSetOrderStatus()) 
                        {
                            if ($order->isSetAmazonOrderId()) 
                            {
                                $num = 0;                         
                                while( $num < count($external_order) ){
                                    if( $external_order[$num]== $order->getAmazonOrderId() ){
                                        if ($order->isSetOrderStatus()) 
                                        {                                   
                                            if($order->getOrderStatus() == 'Unshipped'){
                                                echo $order->getAmazonOrderId() . '<br>';
                                                echo $tracking_number[$num] . '<br>';                                            
                                                $submit_data[$i]['tracking_number'] = $tracking_number[$num];
                                                $submit_data[$i]['amazon_order_id'] = $order->getAmazonOrderId();
                                                $submit_data[$i]['shipping_date'] = $shipping_date[$num]; 
                                                $i++;                                                                                                                                           
                                            }
                                        }
                                    }
                                    $num++;
                                }                                                                       
                            }   
                        }
                    }
                    $filename = '../../temp.txt';          
                    
                    save_array_to_file($filename,$submit_data);
                    $curl_data = '';
                    $url = 'http://www.amazonconnector.saspas.com/MarketplaceWebService/Samples/SubmitFeedSample.php';
                    if (count($submit_data)){
                        echo 'ok';
                        $submit_feed = get_web_page($url,$curl_data);
                    }                    
                    echo $submit_feed;
                    //$array_result = read_array_from_file($filename);
                   // print_r($submit_data);            
                        
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


                