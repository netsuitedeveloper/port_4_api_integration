/**
 * Version    Date       	Author           Remarks
 * 1.00       18 May 2018	Hakuna Moni
 *
 * Script : Retrieve Shopify Products
 * Triggered manually running the script url
 * Parameters passed : null
 * Type : Suitelet
 *
 */

// TODO: Initiate Basic Info
var log_scriptName = "sl_shopify_getproducts";
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
var log_keyInfo = "";
var log_reason = "";

function suitelet(request, response) {
  try {
    nlapiLogExecution("DEBUG", "log_scriptName", log_scriptName);
    nlapiLogExecution("DEBUG", "log_runningDate", log_runningDate);
    nlapiLogExecution("DEBUG", "log_runningDateTime", log_runningDateTime);

    // TODO: Initiate Shopify Info
    var sh_headerUrl = buildShopifyHeaderUrl(
      sh_nz_apikey,
      sh_nz_apipwd,
      sh_nz_domain
    );
    if (!sh_headerUrl) {
      nlapiLogExecution("ERROR", "'sh_headerUrl'", "Failed to Build ");
      return null;
    }
    nlapiLogExecution("DEBUG", "Shopify Basic Header URL: ", sh_headerUrl);
    response.write("\n\nShopify Basic Header URL :\n" + sh_headerUrl);

    // // TODO: Retrieve Count of Products from Shopify
    // var strJSON_respBodyShopifyProductCount = sh_RetrieveCountProducts(sh_headerUrl);
    // if (!strJSON_respBodyShopifyProductCount) {
    // 	nlapiLogExecution('ERROR', "'strJSON_respBodyShopifyProductCount'", "Failed to Retrieve Response");
    // 	return null;
    // }
    // nlapiLogExecution('DEBUG', 'strJSON_respBodyShopifyProductCount: ', strJSON_respBodyShopifyProductCount);
    // response.write("\n\n strJSON_respBodyShopifyProductCount :\n" + strJSON_respBodyShopifyProductCount);

    // // TODO: Get Count of Products from Shopify
    // var arrJSON_respBodyShopifyProductCount = JSON.parse(strJSON_respBodyShopifyProductCount);
    // response.write("\n\n arrJSON_respBodyShopifyProductCount :\n" + arrJSON_respBodyShopifyProductCount['count']);

    // TODO: Retrieve Lists of Products Info from Shopify
    var strJSON_respBodyShopifyProducts = sh_RetrieveListProducts(sh_headerUrl);
    if (!strJSON_respBodyShopifyProducts) {
      nlapiLogExecution(
        "ERROR",
        "'strJSON_respBodyShopifyProducts'",
        "Failed to Retrieve Response"
      );
      return null;
    }
    nlapiLogExecution(
      "DEBUG",
      "strJSON_respBodyShopifyProducts: ",
      strJSON_respBodyShopifyProducts
    );
    response.write(
      "\n\n strJSON_respBodyShopifyProducts :\n" +
        strJSON_respBodyShopifyProducts
    );

    return null;
  } catch (e) {
    nlapiLogExecution("ERROR", "Fatal Error", e);
    return null;
  }
}
