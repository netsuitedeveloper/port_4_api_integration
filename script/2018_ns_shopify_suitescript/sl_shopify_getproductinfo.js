/**
 * Version    Date       	Author           Remarks
 * 1.00       16 May 2018	Hakuna Moni
 *
 * Script : Retrieve Shopify Product Info
 * Triggered manually running the script url
 * Parameters passed : sh_pro_id ('Shopify Product ID')
 * Type : Suitelet
 *
 */

// TODO: Initiate Basic Info
var log_scriptName = "sl_shopify_getproductinfo";
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
    if (request.getMethod() == "GET") {
      nlapiLogExecution("DEBUG", "log_scriptName", log_scriptName);
      nlapiLogExecution("DEBUG", "log_runningDate", log_runningDate);
      nlapiLogExecution("DEBUG", "log_runningDateTime", log_runningDateTime);

      // TODO: Get Parameter Passed
      var sh_proID = request.getParameter("sh_pro_id");
      if (!sh_proID) {
        nlapiLogExecution("ERROR", "'sh_pro_id'", "No parameter value matched");
        return null;
      }
      nlapiLogExecution("DEBUG", "Passed Parameters: ", sh_proID);
      response.write("Shopify Product ID :" + sh_proID);

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

      // TODO: Retrieve Single Product Info from Shopify
      var strJSON_respBodyShopifyProduct = sh_RetrieveSingleProductByID(
        sh_headerUrl,
        sh_proID
      );
      if (!strJSON_respBodyShopifyProduct) {
        nlapiLogExecution(
          "ERROR",
          "'strJSON_respBodyShopifyProduct'",
          "Failed to Retrieve Response"
        );
        return null;
      }
      nlapiLogExecution(
        "DEBUG",
        "strJSON_respBodyShopifyProduct: ",
        strJSON_respBodyShopifyProduct
      );
      response.write(
        "\n\n strJSON_respBodyShopifyProduct :\n" +
          strJSON_respBodyShopifyProduct
      );

      return null;
    }
  } catch (e) {
    nlapiLogExecution("ERROR", "Fatal Error", e);
    return null;
  }
}
