<?php

/*
 * The RefundTransaction API operation issues a refund to the PayPal account holder associated with a transaction.
 * This API operation can be used to issue a full or partial refund for any transaction within a default period of 60 days from when the payment is received. 
 * @author: Hakuna Moni
 * @modified: 08/31/2015
 * @version: v1.0.0
 */

require_once 'NetSuiteService.php';
require_once "PayPalAPI.php";
require_once "functions.php";

// Get transactions from PayPal API
$paypal = new PayPalAPI();

$query = "&";

// (Conditional) Either the transaction ID or the payer ID must be specified. The transaction ID is the unique identifier of the transaction to be refunded.
//$transactionID = $_REQUEST['transaction_id'];
$transactionID = isset($_REQUEST['transaction_id']) ? $_REQUEST['transaction_id'] : '8Y076510UF9179345';
if (!empty($transactionID)){
    $query .= "&TRANSACTIONID=" . $transactionID;
}

// Conditional) Either the transaction ID or the payer ID must be specified.
$payerID = isset($_REQUEST['payer_id']) ? $_REQUEST['payer_id'] : "";
if (!empty($payerID)){
    $query .= "&PAYERID=".$payerID;
}

// (Conditional) This field is required for partial refunds and is also required for refunds greater than 100%. An ISO 4217 3-letter currency code, for example, USD for US Dollars.
$currencyCode = isset($_REQUEST['currency']) ? $_REQUEST['currency'] : "";
if (!empty($currencyCode)){
    $query .= "&CURRENCYCODE=".$currencyCode;
}

//(Optional) Your own invoice or tracking ID number.
$invoiceID = isset($_REQUEST['invoice_id']) ? $_REQUEST['invoice_id'] : "";
if (!empty($invoiceID)){
    $query .= "&INVOICEID=".$invoiceID;
}

// Type of refund you are making. It is one of the following values: Full, Partial, ExternalDispute, Other
$refundType = isset($_REQUEST['refund_type']) ? $_REQUEST['refund_type'] : "";
if (!empty($refundType)){
    $query .= "&REFUNDTYPE=".$refundType;
}

// (Optional) Refund amount. The amount is required if RefundType is Partial.
$amt = isset($_REQUEST['amt']) ? $_REQUEST['amt'] : "";
if (!empty($amt)){
    $query .= "&AMT=".$amt;
}

// (Optional) Custom memo about the refund.
$note = isset($_REQUEST['note']) ? $_REQUEST['note'] : "";
if (!empty($note)){
    $query .= "&NOTE=".$note;
}

// (Optional) Maximum time until you must retry the refund.
$retryUntil = isset($_REQUEST['retry_until']) ? $_REQUEST['retry_until'] : "";
if (!empty($retryUntil)){
    $query .= "&RETRYUNTIL=".$retryUntil;
}

// (Optional)Type of PayPal funding source (balance or eCheck) that can be used for auto refund.
$refundSource = isset($_REQUEST['refund_source']) ? $_REQUEST['refund_source'] : "";
if (!empty($refundSource)){
    $query .= "&REFUNDSOURCE=".$refundSource;
}

// (Optional) Flag to indicate that the buyer was already given store credit for a given transaction.
$refundAdvice = isset($_REQUEST['refund_advice']) ? $_REQUEST['refund_advice'] : "";
if (!empty($refundAdvice)){
    $query .= "&REFUNDADVICE=".$refundAdvice;
}

// (Optional) The amount of shipping paid.
$shippingAmt = isset($_REQUEST['shipping_amt']) ? $_REQUEST['shipping_amt'] : "";
if (!empty($shippingAmt)){
    $query .= "&SHIPPINGAMT=".$shippingAmt;
}

// (Optional) The amount of tax paid.
$taxAmt = isset($_REQUEST['tax_amt']) ? $_REQUEST['tax_amt'] : "";
if (!empty($taxAmt)){
    $query .= "&TAXAMT=".$taxAmt;
}

// (Conditional) Required if a value is passed. Key of the merchant-specific Private Label Credit Card (PLCC) information passed with the transaction, in the form of key-value pairs.
$merchantDatanKey = isset($_REQUEST['merchantdata_n_key']) ? $_REQUEST['merchantdata_n_key'] : "";
if (!empty($merchantDatanKey)){
    $query .= "&MERCHANTDATAnKEY=".$merchantDatanKey;
}

// (Conditional) Required if a key is passed. Value of the merchant-specific Private Label Credit Card (PLCC) information passed with the transaction, in the form of key-value pairs.
$merchantDatanValue = isset($_REQUEST['merchantdata_n_value']) ? $_REQUEST['merchantdata_n_value'] : "";
if (!empty($merchantDatanValue)){
    $query .= "&MERCHANTDATAnVALUE=".$merchantDatanValue;
}

// (Optional) A message ID used for idempotence to uniquely identify a message.
$msgSubID = isset($_REQUEST['msgsubid']) ? $_REQUEST['msgsubid'] : "";
if (!empty($msgSubID)){
    $query .= "&MSGSUBID=".$msgSubID;
}

if (!empty($transactionID)){    
    $res = $paypal->call($query, "RefundTransaction");
    
    echo '<pre>';
    print_r($res);
    echo '</pre>';

    /*if ($res){
        // Remove refunded transaction from NS
        $cusRecId = '1624';

        $service = new NetSuiteService();

        $search = new CustomRecordSearch();

        $recordRef = new RecordRef();
        $recordRef->internalId = $cusRecId;

        $searchBasic = new CustomRecordSearchBasic();
        $searchBasic->recType = $recordRef;

        $search->basic = $searchBasic;

        $request = new SearchRequest();
        $request->searchRecord = $search;

        $searchResponse = $service->search($request);

        if (!$searchResponse->searchResult->status->isSuccess) {
            echo "Search Error";
        } else {
            $records = $searchResponse->searchResult->recordList->record;
            
            if (count($records) > 0){
                
                foreach($records as $record){

                    if ($record->custransactionid == $transactionID){
                        $recordRef = new CustomRecordRef();
                        $recordRef->internalId = $record->internalId;
                        $recordRef->typeId = $cusRecId;
                        
                        $request = new DeleteRequest();
                        $request->baseRef = $recordRef;
                        
                        $deleteResponse = $service->delete($request);
                    }
                }
            }
        }
    }*/
}

?>
