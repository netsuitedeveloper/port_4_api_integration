<?php

date_default_timezone_set('UTC');

require_once 'NetSuiteService.php';
require_once "PayPalAPI.php";
require_once "functions.php";

$paypal = new PayPalAPI();

$trans = array();

$l_date = file_get_contents("cookie.ini");

function get_transactions($start_date){
    global $paypal, $trans;
    
    $query = "&VERSION=94&STARTDATE=".$start_date."&ENDDATE=".date('Y-m-d H:i:s')."&TRANSACTIONCLASS=All";
    $res = $paypal->call($query, "TransactionSearch");
    
    if ($res){
        file_put_contents("cookie.ini", date('Y-m-d H:i:s'));
    }
    
    $last_date = "";
    $last_trans = !empty($res["L_TRANSACTIONID99"]) ? $res["L_TRANSACTIONID99"] : "";
                
    for($i = 0; $i < 100; $i++){
        
        if (empty($res["L_TRANSACTIONID".$i]))
            continue;

        $query = "&VERSION=94&TRANSACTIONID=".$res["L_TRANSACTIONID".$i];
        $result = $paypal->call($query, "GetTransactionDetails");
        
        if (empty($result['TRANSACTIONID']))
            continue;

        $single = array(
            'RECEIVERBUSINESS' => isset($result['RECEIVERBUSINESS']) ? $result['RECEIVERBUSINESS'] : "",
            'RECEIVEREMAIL' => isset($result['RECEIVEREMAIL']) ? $result['RECEIVEREMAIL'] : "",
            'RECEIVERID' => isset($result['RECEIVERID']) ? $result['RECEIVERID'] : "",
            'EMAIL' => isset($result['EMAIL']) ? $result['EMAIL'] : "",
            'PAYERID' => isset($result['PAYERID']) ? $result['PAYERID'] : "",
            'PAYERSTATUS' => isset($result['PAYERSTATUS']) ? $result['PAYERSTATUS'] : "",
            'COUNTRYCODE' => isset($result['COUNTRYCODE']) ? $result['COUNTRYCODE'] : "",
            'BUSINESS' => isset($result['BUSINESS']) ? $result['BUSINESS'] : "",
            'ADDRESSOWNER' => isset($result['ADDRESSOWNER']) ? $result['ADDRESSOWNER'] : "",
            'ADDRESSSTATUS' => isset($result['ADDRESSSTATUS']) ? $result['ADDRESSSTATUS'] : "",
            'TIMESTAMP' => isset($result['TIMESTAMP']) ? date('m/d/Y H:i:s', strtotime($result['TIMESTAMP'])) : "",
            'CORRELATIONID' => isset($result['CORRELATIONID']) ? $result['CORRELATIONID'] : "",
            'ACK' => isset($result['ACK']) ? $result['ACK'] : "",
            'FIRSTNAME' => isset($result['FIRSTNAME']) ? $result['FIRSTNAME'] : "",
            'LASTNAME' => isset($result['LASTNAME']) ? $result['LASTNAME'] : "",
            'TRANSACTIONID' => isset($result['TRANSACTIONID']) ? $result['TRANSACTIONID'] : "",
            'TRANSACTIONTYPE' => isset($result['TRANSACTIONTYPE']) ? $result['TRANSACTIONTYPE'] : "",
            'PAYMENTTYPE' => isset($result['PAYMENTTYPE']) ? $result['PAYMENTTYPE'] : "",
            'ORDERTIME' => isset($result['ORDERTIME']) ? date('m/d/Y H:i:s', strtotime($result['ORDERTIME'])) : "",
            'AMT' => isset($result['AMT']) ? $result['AMT'] : "",
            'CURRENCYCODE' => isset($result['CURRENCYCODE']) ? $result['CURRENCYCODE'] : "",
            'PAYMENTSTATUS' => isset($result['PAYMENTSTATUS']) ? $result['PAYMENTSTATUS'] : "",
            'PENDINGREASON' => isset($result['PENDINGREASON']) ? $result['PENDINGREASON'] : "",
            'REASONCODE' => isset($result['REASONCODE']) ? $result['REASONCODE'] : "",
            'PROTECTIONELIGIBILITY' => isset($result['PROTECTIONELIGIBILITY']) ? $result['PROTECTIONELIGIBILITY'] : "",
            'L_CURRENCYCODE0' => isset($result['L_CURRENCYCODE0']) ? $result['L_CURRENCYCODE0'] : "",
            'L_TAXABLE0' => isset($result['L_TAXABLE0']) ? $result['L_TAXABLE0'] : "",
        );
		        
		array_push($trans, $single);
        
        /*if (!empty($result['ORDERTIME'])){
		    $last_date = date('Y-m-d H:i:s', strtotime($result['ORDERTIME']));
            file_put_contents("cookie.ini", $last_date);
        }*/
    }
    
    /*if (strtotime($start_date) <= strtotime($last_date) && $last_trans != ""){
        get_transactions($last_date);
    }*/
    
}

if (!empty($l_date)){
    get_transactions($l_date);
}else{
    get_transactions(date('Y-m-d H:i:s'));
}

krsort($trans);
$trans = array_values($trans);

echo "Total Transactions: " . count($trans) . "<hr />";

echo '<pre>';
print_r($trans);
echo '</pre>';

?>
