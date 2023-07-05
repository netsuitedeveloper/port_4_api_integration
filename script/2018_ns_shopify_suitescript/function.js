/**
 * Version    Date       	Author           Remarks
 * 1.00       16 May 2018	Hakuna Moni
 *
 * Script : Function
 * Type : JavaScript
 *
 */

/**
 * 	Initiate Shopify Info
 *	www.thebrandoutlet.co.nz
 */
var sh_nz_apikey = "aaa";
var sh_nz_apipwd = "ppp";
var sh_nz_domain = "bbb.myshopify.com/admin/";

/**
 * 	Function: Retrieve Shopify Product Single Info by passing ID
 *	GET /admin/products/#{product_id}.json
 *	Parameters: basicUrl, proID (Product ID)
 */
function sh_RetrieveSingleProductByID(basicUrl, proID) {
  if (basicUrl === undefined) return null;
  if (proID === undefined) return null;

  var url = basicUrl + "products/" + proID + ".json";

  var response = nlapiRequestURL(url);

  var respCode = response.getCode();
  if (respCode != "200") {
    nlapiLogExecution("ERROR", "sh_GetProductById response", respCode);
    return null;
  }

  return response.getBody();
}

/**
 * 	Function: Retrieve Shopify List of Products
 *	GET /admin/products.json
 *	Parameters: basicUrl, pageNum, apiLimit
 */
function sh_RetrieveListProducts(basicUrl, pageNum, apiLimit) {
  if (basicUrl === undefined) return null;
  if (pageNum === undefined) pageNum = 1;
  if (apiLimit === undefined) apiLimit = 250;

  var url = basicUrl + "products.json?limit=" + apiLimit + "&page=" + pageNum;

  var response = nlapiRequestURL(url);

  var respCode = response.getCode();
  if (respCode != "200") {
    nlapiLogExecution("ERROR", "sh_RetrieveListProducts response", respCode);
    return null;
  }

  return response.getBody();
}

/**
 * 	Function: Retrieve a count of all products of Shopify
 *	GET /admin/products/count.json
 *	Parameters: basicUrl
 */
function sh_RetrieveCountProducts(basicUrl) {
  if (basicUrl === undefined) return null;

  var url = basicUrl + "products/count.json";

  var response = nlapiRequestURL(url);

  var respCode = response.getCode();
  if (respCode != "200") {
    nlapiLogExecution("ERROR", "sh_RetrieveListProducts response", respCode);
    return null;
  }

  return response.getBody();
}

/**
 * 	Function: Build Shopify Header URL which will be used as basic url
 *	Parameters: apikey, apipwd, domain
 */
function buildShopifyHeaderUrl(apikey, apipwd, domain) {
  if (apikey === undefined) return null;
  if (apipwd === undefined) return null;
  if (domain === undefined) return null;

  var url = "https://" + apikey + ":" + apipwd + "@" + domain;

  return url;
}

/**
 * 	Function: Find Item by handle
 *	Parameters: handle
 */
function findItemInternalId(handle) {
  var filters = [];
  // filters.push(new nlobjSearchFilter('itemid', null, 'is', handle));
  filters.push(new nlobjSearchFilter("custitem_sh_handle", null, "is", handle));

  var results = nlapiSearchRecord("inventoryitem", null, filters, null);

  if (results != null && results.length > 0) {
    return results[0].getId();
  }
  return null;
}

/**
 * 	Function: Check Governance
 *	Parameters:
 */
function _check_Governance() {
  // Get the remaining usage points of the scripts
  var usage = nlapiGetContext().getRemainingUsage();

  // If the script's remaining usage points are bellow 1,000 ...
  if (usage < 1000) {
    // ...yield the script
    var state = nlapiYieldScript();
    // Throw an error or log the yield results
    if (state.status == "FAILURE") {
      nlapiLogExecution(
        "debug",
        "Failed to yield script (do-while), exiting: Reason = " +
          state.reason +
          " / Size = " +
          state.size
      );
      throw "Failed to yield script";
    } else if (state.status == "RESUME")
      nlapiLogExecution("DEBUG", "Resuming script");
  }

  return true;
}

/**
 * 	Function: Create Custom Record "Akina Webservice Error Log"
 *	Parameters: scriptname, datetime, step, reason, keyinfo, attachment, folderID
 */
function _log_registry(
  scriptname,
  datetime,
  step,
  reason,
  keyinfo,
  attachment,
  folderID
) {
  if (scriptname === undefined) return null;
  if (attachment === undefined) attachment = null;
  if (folderID === undefined) folderID = "7";

  var recWSLog = nlapiCreateRecord("customrecord_akina_api_err_log");

  recWSLog.setFieldValue("custrecord_akina_log_date", datetime);
  recWSLog.setFieldValue("custrecord_akina_log_script_name", scriptname);
  recWSLog.setFieldValue("custrecord_akina_log_step", step);
  recWSLog.setFieldValue("custrecord_akina_log_reason", reason);
  recWSLog.setFieldValue("custrecord_akina_log_key_info", keyinfo);

  var log_today = new Date();
  var log_runningDate =
    log_today.getFullYear() +
    "-" +
    (log_today.getMonth() + 1) +
    "-" +
    log_today.getDate();
  var log_runningDateTime =
    log_runningDate +
    "_" +
    log_today.getHours() +
    ":" +
    log_today.getMinutes() +
    ":" +
    log_today.getSeconds();
  var log_randomNum = Math.floor(Math.random() * 100);

  if (attachment) {
    requestFileName = log_runningDateTime + " " + log_randomNum + ".txt";
    fileRequest = nlapiCreateFile(requestFileName, "PLAINTEXT", attachment);
    fileRequest.setFolder(folderID);
    idFileRequest = nlapiSubmitFile(fileRequest);

    recWSLog.setFieldValue("custrecord_akina_log_attach", idFileRequest);
  }

  idRecWSLog = nlapiSubmitRecord(recWSLog, false, true);

  return true;
}
