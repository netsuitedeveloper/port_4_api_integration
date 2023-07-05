<?php
function clearPayPalTransactions($cusRecTypeId, $service){
    
    /* Get PayPal Transactions List */
    $search = new CustomRecordSearch();

    $recordRef = new RecordRef();
    $recordRef->internalId = $cusRecTypeId;

    $searchBasic = new CustomRecordSearchBasic();
    $searchBasic->recType = $recordRef;

    $search->basic = $searchBasic;

    $request = new SearchRequest();
    $request->searchRecord = $search;

    $searchResponse = $service->search($request);

    if (!$searchResponse->searchResult->status->isSuccess) {
        return "There is no transactions for PayPal.";
    } else {
        
        $deleted = 0;
        
        /* Remove transactions */
        $records = $searchResponse->searchResult->recordList->record;
        
        foreach($records as $record){
            
            $recordRef = new CustomRecordRef();
            $recordRef->internalId = $record->internalId;
            $recordRef->typeId = $cusRecTypeId;
            
            $request = new DeleteRequest();
            $request->baseRef = $recordRef;
            
            $deleteResponse = $service->delete($request);
            
            if ($deleteResponse->writeResponse->status->isSuccess){
                $deleted++;        
            }
        }
        
        return $deleted . " Custom Records has been removed successfully.";
    }
}

function searchCustomRecord($internalId, $service){
    /* Get PayPal Accounts List */
    $search = new CustomRecordSearch();

    $recordRef = new RecordRef();
    $recordRef->internalId = $internalId;

    $searchBasic = new CustomRecordSearchBasic();
    $searchBasic->recType = $recordRef;

    $search->basic = $searchBasic;

    $request = new SearchRequest();
    $request->searchRecord = $search;

    $searchResponse = $service->search($request);
    
    return $searchResponse;
}

?>
