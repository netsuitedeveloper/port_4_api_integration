<?php

/*
 * Get transactions using PayPal API and add transactions into NS.
 * This function runs per 30 minutes everyday by cron-job task.
 * @author: Hakuna Moni
 * @modified: 8/14/2015
 * @version: v1.0.0
 */

require_once 'NetSuiteService.php';
require_once "PayPalAPI.php";
require_once "functions.php";

// Get transactions from PayPal API
$paypal = new PayPalAPI();
$trans = array();

function get_transactions($start_date){
    
    global $paypal, $trans;
    
    // Get transactions with "TransactionSearch" method 
    $query = "&STARTDATE=".$start_date."&ENDDATE=".date('Y-m-d H:i:s')."&TRANSACTIONCLASS=All";
    $res = $paypal->call($query, "TransactionSearch");

    $last_date = "";
    
    // Get latest transaction id
    $last_trans = !empty($res["L_TRANSACTIONID99"]) ? $res["L_TRANSACTIONID99"] : "";
    
    for($i = 0; $i < 100; $i++){
        
        // skip if transaction id is empty or null
        if (empty($res["L_TRANSACTIONID".$i]))
            continue;
        
        // Get transaction details with "GetTransactionDetails" method
        $query = "&TRANSACTIONID=".$res["L_TRANSACTIONID".$i];
        $result = $paypal->call($query, "GetTransactionDetails");
        
        // skip if transaction id is empty or null from transaction details
        if (empty($result['TRANSACTIONID']))
            continue;

        $single = array(
            'RECEIVERBUSINESS'      => isset($result['RECEIVERBUSINESS']) ? $result['RECEIVERBUSINESS'] : "",
            'RECEIVEREMAIL'         => isset($result['RECEIVEREMAIL']) ? $result['RECEIVEREMAIL'] : "",
            'RECEIVERID'            => isset($result['RECEIVERID']) ? $result['RECEIVERID'] : "",
            'EMAIL'                 => isset($result['EMAIL']) ? $result['EMAIL'] : "",
            'PAYERID'               => isset($result['PAYERID']) ? $result['PAYERID'] : "",
            'PAYERSTATUS'           => isset($result['PAYERSTATUS']) ? $result['PAYERSTATUS'] : "",
            'COUNTRYCODE'           => isset($result['COUNTRYCODE']) ? $result['COUNTRYCODE'] : "",
            'BUSINESS'              => isset($result['BUSINESS']) ? $result['BUSINESS'] : "",
            'ADDRESSOWNER'          => isset($result['ADDRESSOWNER']) ? $result['ADDRESSOWNER'] : "",
            'ADDRESSSTATUS'         => isset($result['ADDRESSSTATUS']) ? $result['ADDRESSSTATUS'] : "",
            'TIMESTAMP'             => isset($result['TIMESTAMP']) ? date('Y-m-d H:i:s', strtotime($result['TIMESTAMP'])) : "",
            'CORRELATIONID'         => isset($result['CORRELATIONID']) ? $result['CORRELATIONID'] : "",
            'ACK'                   => isset($result['ACK']) ? $result['ACK'] : "",
            'FIRSTNAME'             => isset($result['FIRSTNAME']) ? $result['FIRSTNAME'] : "",
            'LASTNAME'              => isset($result['LASTNAME']) ? $result['LASTNAME'] : "",
            'TRANSACTIONID'         => isset($result['TRANSACTIONID']) ? $result['TRANSACTIONID'] : "",
            'TRANSACTIONTYPE'       => isset($result['TRANSACTIONTYPE']) ? $result['TRANSACTIONTYPE'] : "",
            'PAYMENTTYPE'           => isset($result['PAYMENTTYPE']) ? $result['PAYMENTTYPE'] : "",
            'ORDERTIME'             => isset($result['ORDERTIME']) ? date('Y-m-d H:i:s', strtotime($result['ORDERTIME'])) : "",
            'AMT'                   => isset($result['AMT']) ? $result['AMT'] : "",
            'CURRENCYCODE'          => isset($result['CURRENCYCODE']) ? $result['CURRENCYCODE'] : "",
            'PAYMENTSTATUS'         => isset($result['PAYMENTSTATUS']) ? $result['PAYMENTSTATUS'] : "",
            'PENDINGREASON'         => isset($result['PENDINGREASON']) ? $result['PENDINGREASON'] : "",
            'REASONCODE'            => isset($result['REASONCODE']) ? $result['REASONCODE'] : "",
            'PROTECTIONELIGIBILITY' => isset($result['PROTECTIONELIGIBILITY']) ? $result['PROTECTIONELIGIBILITY'] : "",
            'L_CURRENCYCODE0'       => isset($result['L_CURRENCYCODE0']) ? $result['L_CURRENCYCODE0'] : "",
            'L_TAXABLE0'            => isset($result['L_TAXABLE0']) ? $result['L_TAXABLE0'] : "",
        );
        
        array_push($trans, $single);
    }
}

// Search transactions from last date
$l_date = file_get_contents("cookie.ini");
if (!empty($l_date)){
    get_transactions($l_date);
}

// Add PayPal Transactions into NS
$cusRecId = '1624';
$service = new NetSuiteService();

if ($trans){
    
    krsort($trans);
    $trans = array_values($trans);
    
    foreach($trans as $t){
        
        file_put_contents("cookie.ini", date('Y-m-d H:i:s', strtotime($t['ORDERTIME'])));
        
        $tmp_date = file_get_contents("cookie.ini");
        
        echo $tmp_date . '<hr />';        
        
        $app = new CustomRecord();
        $app->name = "PayPal Transaction";
        
        $recordRef = new RecordRef();
        $recordRef->internalId = '1624';
        
        $fld1 = new StringCustomFieldRef();
        $fld1->value = $t['RECEIVERBUSINESS'];
        $fld1->internalId = '1645';
        
        $fld2 = new StringCustomFieldRef();
        $fld2->value = $t['RECEIVEREMAIL'];
        $fld2->internalId = '1624';
        
        $fld3 = new StringCustomFieldRef();
        $fld3->value = $t['RECEIVERID'];
        $fld3->internalId = '1628';
        
        $fld4 = new StringCustomFieldRef();
        $fld4->value = $t['EMAIL'];
        $fld4->internalId = '1629';
        
        $fld5 = new StringCustomFieldRef();
        $fld5->value = $t['PAYERID'];
        $fld5->internalId = '1630';
        
        $fld6 = new StringCustomFieldRef();
        $fld6->value = $t['PAYERSTATUS'];
        $fld6->internalId = '1632';
        
        $fld7 = new StringCustomFieldRef();
        $fld7->value = $t['COUNTRYCODE'];
        $fld7->internalId = '1631';
        
        $fld8 = new StringCustomFieldRef();
        $fld8->value = $t['BUSINESS'];
        $fld8->internalId = '1633';
        
        $fld9 = new StringCustomFieldRef();
        $fld9->value = $t['ADDRESSOWNER'];
        $fld9->internalId = '1634';
        
        $fld10 = new StringCustomFieldRef();
        $fld10->value = $t['ADDRESSSTATUS'];
        $fld10->internalId = '1627';
        
        $fld11 = new StringCustomFieldRef();
        $fld11->value = date('Y-m-d H:i:s', strtotime($t['TIMESTAMP']));
        $fld11->internalId = '1626';
        
        $fld12 = new StringCustomFieldRef();
        $fld12->value = $t['CORRELATIONID'];
        $fld12->internalId = '1625';
        
        $fld13 = new StringCustomFieldRef();
        $fld13->value = $t['ACK'];
        $fld13->internalId = '1653';
        
        $fld14 = new StringCustomFieldRef();
        $fld14->value = $t['FIRSTNAME'] . " " . $t['LASTNAME'];
        $fld14->internalId = '1654';
                
        $fld16 = new StringCustomFieldRef();
        $fld16->value = $t['TRANSACTIONID'];
        $fld16->internalId = '1656';
        
        $fld17 = new StringCustomFieldRef();
        $fld17->value = $t['TRANSACTIONTYPE'];
        $fld17->internalId = '1657';
        
        $fld18 = new StringCustomFieldRef();
        $fld18->value = $t['PAYMENTTYPE'];
        $fld18->internalId = '1658';
        
        $fld19 = new StringCustomFieldRef();
        $fld19->value = date('Y-m-d H:i:s', strtotime($t['ORDERTIME']));
        $fld19->internalId = '1659';
        
        $fld20 = new StringCustomFieldRef();
        $fld20->value = $t['AMT'];
        $fld20->internalId = '1660';
        
        $fld21 = new StringCustomFieldRef();
        $fld21->value = $t['CURRENCYCODE'];
        $fld21->internalId = '1661';
        
        $fld22 = new StringCustomFieldRef();
        $fld22->value = $t['PAYMENTSTATUS'];
        $fld22->internalId = '1662';
        
        $fld23 = new StringCustomFieldRef();
        $fld23->value = $t['PENDINGREASON'];
        $fld23->internalId = '1663';
        
        $fld24 = new StringCustomFieldRef();
        $fld24->value = $t['REASONCODE'];
        $fld24->internalId = '1664';
        
        $fld25 = new StringCustomFieldRef();
        $fld25->value = $t['PROTECTIONELIGIBILITY'];
        $fld25->internalId = '1665';
        
        $app->recType = $recordRef;
        $app->customFieldList = new CustomFieldList();
        $app->customFieldList->customField = array($fld1, $fld2, $fld3, $fld4, $fld5, $fld6, $fld7, $fld8, $fld9, $fld10,
                                                   $fld11, $fld12, $fld13, $fld14, $fld16, $fld17, $fld18, $fld19, $fld20,
                                                   $fld21, $fld22, $fld23, $fld24, $fld25);
        
        $request = new AddRequest();
        $request->record = $app;
        
        $addResponse = $service->add($request);
        
        echo '<pre>';
        print_r($addResponse);
        echo '</pre>';
    }
}
?>