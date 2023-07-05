/**
 * Copyright (c) 2015 XXX
 * All Rights Reserved.
 */

var RESTheaders = new Array();

var RESTurlCreate = "https://app.detrack.com/api/v1/deliveries/create.json";
var RESTurlEdit = "https://app.detrack.com/api/v1/deliveries/update.json";
var RESTKey = "aaa";

var shippingItems = [
  1482, //- local delivery
  64067, //- ship 1 hour S C area
  66337, //- will call - Morgan Hill
  1481, //- will call - Santa Clara
  /*21219,*/ 66254, //- juan to deliver
  72146, //- justin to deliver
  72145, //- gilbert to deliver
  72144, //- daniel to deliver
];

// Detrack api needs next content type
RESTheaders["Content-Type"] = "application/x-www-form-urlencoded";

/**
 *
 *
 * @param (string) stType Type of operation submitted
 * @authors Alex Qian
 * @version 1.0
 */
function submitToDetrack_AfterSubmit(type) {
  try {
    var newId = nlapiGetRecordId();
    var newType = nlapiGetRecordType();
    nlapiLogExecution(
      "DEBUG",
      "type:" + type + ", RecordType: " + newType + ", Id:" + newId
    );

    if (type != "edit" && type != "create") return;

    // get invoice data from invoice record
    var curRecord = nlapiLoadRecord("invoice", nlapiGetRecordId());

    // date field should be format of "2015-02-09"
    var dateString = curRecord.getFieldValue("custbody38");
    var date = dateString
      ? new Date(dateString)
      : new Date(curRecord.getFieldValue("trandate"));
    dateString = date.toISOString().substring(0, 10);

    var tranId = curRecord.getFieldValue("tranid"); // Invoice #
    var shipAddr = curRecord.getFieldValue("shipaddress");
    var company = curRecord.getFieldValue("custbody37"); //  Company Name
    var phone = curRecord.getFieldValue("custbody1");

    var shipMethod = curRecord.getFieldText("shipmethod");
    var shipMethodValue = curRecord.getFieldValue("shipmethod");

    nlapiLogExecution(
      "DEBUG",
      "ShippingMethod",
      shipMethodValue + " - " + shipMethod
    );

    if (shippingItems.indexOf(+shipMethodValue) == -1) {
      return;
    }

    var instruction = curRecord.getFieldText("custbody11"); //curRecord.getFieldValue('memo');
    var po = curRecord.getFieldValue("otherrefnum"); // PO #

    // make json structure
    var jsonData = {};
    jsonData.date = dateString;
    jsonData.do = tranId;
    jsonData.address = shipAddr;
    jsonData.deliver_to = company;
    jsonData.customer = company;
    jsonData.phone = phone;
    jsonData.assign_to = curRecord.getFieldText("custbody39"); // Delivery Device
    //jsonData.instructions = curRecord.getFieldValue('custbody11');	// Delivery Notes
    jsonData.instructions = instruction ? instruction : "";
    jsonData.invoice_amt = curRecord.getFieldValue("total");
    jsonData.invoice_no = tranId;
    //jsonData.assign_to = shipMethod;

    jsonData.items = [];

    nlapiLogExecution(
      "DEBUG",
      "Data",
      date + " - " + tranId + " - " + shipAddr + " - " + phone
    );

    // loop items
    var itemCount = curRecord.getLineItemCount("item");
    for (lineNo = 1; lineNo <= itemCount; lineNo++) {
      var sku = curRecord.getLineItemText("item", "item", lineNo);
      var desc = curRecord.getLineItemValue("item", "description", lineNo);
      var quantity = curRecord.getLineItemValue("item", "quantity", lineNo);

      jsonData.items.push({ sku: sku, qty: quantity, desc: desc, po_no: po });
    }
    var deliveryString = JSON.stringify(jsonData);
    nlapiLogExecution("DEBUG", "JSON", deliveryString);

    // call Detrack api
    var RESTjson =
      "key=" +
      RESTKey +
      "&json=" +
      encodeURIComponent("[" + deliveryString + "]");
    nlapiLogExecution("DEBUG", "Parameter", RESTjson);

    var RESTurl = type == "create" ? RESTurlCreate : RESTurlEdit;

    var response = nlapiRequestURL(RESTurl, RESTjson, RESTheaders, "POST");
    var responseCode = response.getCode();

    nlapiLogExecution(
      "DEBUG",
      "API response",
      responseCode + " - " + response.getBody()
    );

    if (responseCode != 200) {
      nlapiLogExecution(
        "ERROR",
        "API Error",
        responseCode + " - " + response.getBody()
      );
      return;
    }

    var jsonResponse = JSON.parse(response.getBody());
  } catch (e) {
    nlapiLogExecution("DEBUG", "GetCommittedTotal Exception", e.message);
  }
}
