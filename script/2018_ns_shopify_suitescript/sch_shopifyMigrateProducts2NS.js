/**
 * Version    Date       	Author           Remarks
 * 1.00       18 May 2018	Hakuna Moni
 *
 * Script : Migrate Shopify Products To NetSuite
 * Triggered manually running turn on script to run
 * Parameters passed :
 * Type : Scheduled Script
 *
 */

// TODO: Initiate Basic Info
var log_scriptName = "sch_shopifyMigrateProducts2NS";
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
var log_step = "";
var log_reason = "";
var log_keyInfo = "";

var log_keyInfo_round = "";
var log_keyInfo_round_pro = "";

function scheduled(type) {
  try {
    var sh_pageNum = 0;
    var sh_apiLimit = 250;

    nlapiLogExecution("DEBUG", "Script Name", log_scriptName);
    nlapiLogExecution("DEBUG", "Script Start Time", log_runningDateTime);

    //*****************************************
    log_step = "Step 0: Initiate Shopify Info";
    //*****************************************
    var sh_headerUrl = buildShopifyHeaderUrl(
      sh_nz_apikey,
      sh_nz_apipwd,
      sh_nz_domain
    );
    if (!sh_headerUrl) {
      log_reason = "Failed to buildShopifyHeaderUrl";
      _log_registry(
        log_scriptName,
        log_runningDateTime,
        log_step,
        log_reason,
        log_keyInfo
      );
      return false;
    }

    //*****************************************
    log_step = "Step 1: Get Count of Products Shopify";
    //*****************************************
    var strJSON_respBodyShopifyProductCount =
      sh_RetrieveCountProducts(sh_headerUrl);
    var arrJSON_respBodyShopifyProductCount = JSON.parse(
      strJSON_respBodyShopifyProductCount
    );
    if (
      arrJSON_respBodyShopifyProductCount == null ||
      !arrJSON_respBodyShopifyProductCount.hasOwnProperty("count")
    ) {
      log_reason = "Failed to arrJSON_respBodyShopifyProductCount";
      _log_registry(
        log_scriptName,
        log_runningDateTime,
        log_step,
        log_reason,
        log_keyInfo,
        strJSON_respBodyShopifyProductCount
      );
      return false;
    }
    var sh_productsCount = parseInt(
      arrJSON_respBodyShopifyProductCount["count"]
    );
    log_keyInfo = "(sh_productsCount)" + sh_productsCount;

    //*****************************************
    log_step = "Step 2: Loop Rounds";
    //*****************************************
    var roundCount = Math.ceil(sh_productsCount / sh_apiLimit);

    for (var i = 1; i <= roundCount; i++) {
      if (sh_productsCount < sh_apiLimit) {
        sh_apiLimit = sh_productsCount;
      }
      sh_productsCount = sh_productsCount - sh_apiLimit;
      log_keyInfo_round = "(Round)" + i + "(sh_apiLimit)" + sh_apiLimit;

      ///////////////////////////////////////////
      log_step = "Step 2_1: Get Shopify Products List";
      ///////////////////////////////////////////
      var strJSON_respBodyShopifyProductList = sh_RetrieveListProducts(
        sh_headerUrl,
        i,
        sh_apiLimit
      );
      var arrJSON_respBodyShopifyProductList = JSON.parse(
        strJSON_respBodyShopifyProductList
      );
      if (
        arrJSON_respBodyShopifyProductList == null ||
        !arrJSON_respBodyShopifyProductList.hasOwnProperty("products")
      ) {
        log_reason = "Failed to arrJSON_respBodyShopifyProductList";
        _log_registry(
          log_scriptName,
          log_runningDateTime,
          log_step,
          log_reason,
          log_keyInfo + log_keyInfo_round,
          strJSON_respBodyShopifyProductList
        );
        continue;
      }

      ///////////////////////////////////////////
      log_step = "Step 2_2: Loop Products";
      ///////////////////////////////////////////
      for (
        var j = 0;
        j < arrJSON_respBodyShopifyProductList["products"].length;
        j++
      ) {
        //*****************************************
        log_step = "Step 2_2_1: Check Validation";
        //*****************************************
        var arrJSON_shopifyProduct =
          arrJSON_respBodyShopifyProductList["products"][j];
        var sh_proHandle = arrJSON_shopifyProduct["handle"];
        var sh_proID = arrJSON_shopifyProduct["id"];
        log_keyInfo_round_pro = "(Nth)" + j + "(sh_proID)" + sh_proID;
        if (arrJSON_shopifyProduct["title"].length > 60) {
          nlapiLogExecution(
            "AUDIT",
            "(Title over 60)" + arrJSON_shopifyProduct["title"],
            log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
          );
        }
        if (sh_proHandle.length > 60) {
          log_reason = "(Handle over 60)" + sh_proHandle;
          _log_registry(
            log_scriptName,
            log_runningDateTime,
            log_step,
            log_reason,
            log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
          );
          nlapiLogExecution(
            "ERROR",
            log_reason,
            log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
          );
          continue;
        }

        ///////////////////////////////////////////
        log_step = "Step 2_10: Check Governance";
        ///////////////////////////////////////////
        _check_Governance();

        //*****************************************
        log_step = "Step 2_2_2: Check Exists";
        //*****************************************
        var itemID = findItemInternalId(sh_proHandle);
        if (itemID) {
          var itemRec = nlapiLoadRecord("inventoryitem", itemID);
          log_keyInfo_round_pro = log_keyInfo_round_pro + "(itemID)" + itemID;
          nlapiLogExecution(
            "DEBUG",
            "Existing Item, Update",
            log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
          );
          continue;
        } else {
          var itemRec = nlapiCreateRecord("inventoryitem", {
            recordmode: "dynamic",
          });
          nlapiLogExecution(
            "DEBUG",
            "New Item, Create",
            log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
          );
        }

        //*****************************************
        log_step = "Step 2_2_3: Set Field Values";
        //*****************************************
        var sh_pro_published = !arrJSON_shopifyProduct["published_at"]
          ? "F"
          : "T";
        var sh_pro_req_shipping = arrJSON_shopifyProduct["variants"][0][
          "requires_shipping"
        ]
          ? "T"
          : "F";
        var sh_pro_taxable = arrJSON_shopifyProduct["variants"][0]["taxable"]
          ? "T"
          : "F";
        var item_displayname =
          arrJSON_shopifyProduct["title"].length > 60
            ? arrJSON_shopifyProduct["title"].slice(0, 60)
            : arrJSON_shopifyProduct["title"];
        var sh_pro_gift_card = "F";

        nlapiLogExecution("DEBUG", "Retrieve Shopify Product");
        nlapiLogExecution("DEBUG", "Handle", arrJSON_shopifyProduct["handle"]);
        nlapiLogExecution("DEBUG", "Title", arrJSON_shopifyProduct["title"]);
        nlapiLogExecution(
          "DEBUG",
          "Body (HTML)",
          arrJSON_shopifyProduct["body_html"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Vendor / Brand",
          arrJSON_shopifyProduct["vendor"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Type",
          arrJSON_shopifyProduct["product_type"]
        );
        nlapiLogExecution("DEBUG", "Tags", arrJSON_shopifyProduct["tags"]);
        nlapiLogExecution("DEBUG", "Published", sh_pro_published);
        nlapiLogExecution(
          "DEBUG",
          "Variant SKU",
          arrJSON_shopifyProduct["variants"][0]["sku"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Grams",
          arrJSON_shopifyProduct["variants"][0]["grams"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Inventory Tracker",
          arrJSON_shopifyProduct["variants"][0]["inventory_management"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Inventory Qty",
          arrJSON_shopifyProduct["variants"][0]["inventory_quantity"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Inventory Policy",
          arrJSON_shopifyProduct["variants"][0]["inventory_policy"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Fulfillment Service",
          arrJSON_shopifyProduct["variants"][0]["fulfillment_service"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Price",
          arrJSON_shopifyProduct["variants"][0]["price"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Compare At Price",
          arrJSON_shopifyProduct["variants"][0]["compare_at_price"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Requires Shipping",
          arrJSON_shopifyProduct["variants"][0]["requires_shipping"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Taxable",
          arrJSON_shopifyProduct["variants"][0]["taxable"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Barcode",
          arrJSON_shopifyProduct["variants"][0]["barcode"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Weight",
          arrJSON_shopifyProduct["variants"][0]["weight"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Weight Unit",
          arrJSON_shopifyProduct["variants"][0]["weight_unit"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Variant Tax Code",
          arrJSON_shopifyProduct["variants"][0]["tax_code"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Image Src",
          arrJSON_shopifyProduct["image"]["src"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Image Position",
          arrJSON_shopifyProduct["image"]["position"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Image Alt Text",
          arrJSON_shopifyProduct["image"]["alt"]
        );
        nlapiLogExecution(
          "DEBUG",
          "Shopify Product ID",
          arrJSON_shopifyProduct["id"]
        );

        itemRec.setFieldValue("itemid", arrJSON_shopifyProduct["handle"]);
        itemRec.setFieldValue("displayname", item_displayname);
        itemRec.setFieldValue(
          "custitem_sh_handle",
          arrJSON_shopifyProduct["handle"]
        );
        itemRec.setFieldValue(
          "custitem_sh_title",
          arrJSON_shopifyProduct["title"]
        );
        itemRec.setFieldValue(
          "custitem_sh_body_html",
          arrJSON_shopifyProduct["body_html"]
        );
        itemRec.setFieldValue(
          "custitem_sh_brand",
          arrJSON_shopifyProduct["vendor"]
        );
        itemRec.setFieldValue(
          "custitem_sh_type",
          arrJSON_shopifyProduct["product_type"]
        );
        itemRec.setFieldValue(
          "custitem_sh_tags",
          arrJSON_shopifyProduct["tags"]
        );
        itemRec.setFieldValue("custitem_sh_published", sh_pro_published);
        itemRec.setFieldValue("custitem_sh_gift_card", sh_pro_gift_card);
        itemRec.setFieldValue(
          "custitem_sh_nz_id",
          arrJSON_shopifyProduct["id"].toString()
        );
        if (arrJSON_shopifyProduct["options"].length >= 1) {
          itemRec.setFieldValue(
            "custitem_sh_opt1_name",
            arrJSON_shopifyProduct["options"][0]["name"]
          );
          itemRec.setFieldValue(
            "custitem_sh_opt1_val",
            arrJSON_shopifyProduct["options"][0]["values"][0]
          );
        }
        if (arrJSON_shopifyProduct["options"].length >= 2) {
          itemRec.setFieldValue(
            "custitem_sh_opt2_name",
            arrJSON_shopifyProduct["options"][1]["name"]
          );
          itemRec.setFieldValue(
            "custitem_sh_opt2_val",
            arrJSON_shopifyProduct["options"][1]["values"][0]
          );
        }
        if (arrJSON_shopifyProduct["options"].length >= 3) {
          itemRec.setFieldValue(
            "custitem_sh_opt3_name",
            arrJSON_shopifyProduct["options"][2]["name"]
          );
          itemRec.setFieldValue(
            "custitem_sh_opt3_val",
            arrJSON_shopifyProduct["options"][2]["values"][0]
          );
        }
        if (arrJSON_shopifyProduct["options"].length >= 1) {
          itemRec.setFieldValue(
            "upccode",
            arrJSON_shopifyProduct["variants"][0]["barcode"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_sku",
            arrJSON_shopifyProduct["variants"][0]["sku"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_grams",
            arrJSON_shopifyProduct["variants"][0]["grams"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_inv_tracker",
            arrJSON_shopifyProduct["variants"][0]["inventory_management"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_inv_qty",
            arrJSON_shopifyProduct["variants"][0]["inventory_quantity"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_inv_policy",
            arrJSON_shopifyProduct["variants"][0]["inventory_policy"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_ful_service",
            arrJSON_shopifyProduct["variants"][0]["fulfillment_service"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_price",
            arrJSON_shopifyProduct["variants"][0]["price"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_compare_price",
            arrJSON_shopifyProduct["variants"][0]["compare_at_price"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_req_shipping",
            sh_pro_req_shipping
          );
          itemRec.setFieldValue("custitem_sh_var_taxable", sh_pro_taxable);
          itemRec.setFieldValue(
            "custitem_sh_var_barcode",
            arrJSON_shopifyProduct["variants"][0]["barcode"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_weight_unit",
            arrJSON_shopifyProduct["variants"][0]["weight_unit"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_tax_code",
            arrJSON_shopifyProduct["variants"][0]["tax_code"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_weight",
            arrJSON_shopifyProduct["variants"][0]["weight"]
          );
        }
        if (arrJSON_shopifyProduct["image"]) {
          itemRec.setFieldValue(
            "custitem_sh_img_src",
            arrJSON_shopifyProduct["image"]["src"]
          );
          itemRec.setFieldValue(
            "custitem_sh_img_position",
            arrJSON_shopifyProduct["image"]["position"]
          );
          itemRec.setFieldValue(
            "custitem_sh_img_alt_text",
            arrJSON_shopifyProduct["image"]["alt"]
          );
          itemRec.setFieldValue(
            "custitem_sh_var_img",
            arrJSON_shopifyProduct["image"]["src"]
          );
        }
        itemRec.setFieldValue("cogsaccount", "216");
        itemRec.setFieldValue("assetaccount", "215");
        itemRec.setFieldValue("salestaxcode", "5");

        //*****************************************
        log_step = "Step 2_2_4: Submit Record";
        //*****************************************
        var submittedItemID = nlapiSubmitRecord(itemRec, true);
        log_keyInfo_round_pro =
          log_keyInfo_round_pro + "(submittedItemID)" + submittedItemID;
        nlapiLogExecution(
          "DEBUG",
          "Submitted successfully",
          log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
        );

        ///////////////////////////////////////////
        log_step = "Step 2_2_10: Check Governance";
        ///////////////////////////////////////////
        _check_Governance();
      }
    }

    nlapiLogExecution(
      "DEBUG",
      "Script Ended Successfully",
      log_runningDateTime
    );
    return true;
  } catch (e) {
    nlapiLogExecution("DEBUG", "Exception " + e.message);
    nlapiLogExecution("DEBUG", "Exception " + e.name);
    nlapiLogExecution("DEBUG", "Exception " + e.toString());
    log_step = "Step 10: ERROR";
    log_reason = e.toString();
    _log_registry(
      log_scriptName,
      log_runningDateTime,
      log_step,
      log_reason,
      log_keyInfo + log_keyInfo_round + log_keyInfo_round_pro
    );
  }
}
