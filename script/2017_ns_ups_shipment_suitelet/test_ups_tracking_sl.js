/**
 * Module Description
 *
 * Version    Date           Author           Remarks
 * 1.00       7 Sep 2017     Hakuna Moni
 *
 */

function suitelet(request, response) {
  try {
    response.write(
      "=========================UPS Tracking API=========================<br/><br/>"
    );

    var url = "https://wwwcie.ups.com/rest/Track";
    // var url = "https://onlinetools.ups.com/rest/Track";
    var apiUsername = "ttt";
    var apiPwd = "aaa";
    var apiKey = "ttt";
    var context = "Get UPS";
    var trackingNumber = "1Z28EY780390424648";

    response.write("API Request URL<br/>");
    response.write(url + "<br/>" + "<br/>");

    var Headers = new Array();
    Headers["Access-Control-Allow-Headers"] =
      "Origin, X-Requested-With, Content-Type, Accept";
    Headers["Access-Control-Allow-Methods"] = "POST";
    Headers["Access-Control-Allow-Origin"] = "*";
    Headers["Content-Type"] = "application/json";

    var arrUsernameToken = {
      Username: apiUsername,
      Password: apiPwd,
    };

    var arrServiceAccessToken = {
      AccessLicenseNumber: apiKey,
    };

    var arrUPSSecurity = {
      UsernameToken: arrUsernameToken,
      ServiceAccessToken: arrServiceAccessToken,
    };

    var arrTransactionReference = {
      CustomerContext: context,
    };

    var arrRequest = {
      RequestOption: "1",
      TransactionReference: arrTransactionReference,
    };

    var arrTrackRequest = {
      Request: arrRequest,
      InquiryNumber: trackingNumber,
    };

    var trackData = {
      UPSSecurity: arrUPSSecurity,
      TrackRequest: arrTrackRequest,
    };
    var jsonData = JSON.stringify(trackData);

    nlapiLogExecution("DEBUG", "UPS Tracking API", "Start - Successfuflly");
    nlapiLogExecution("DEBUG", "API Request", jsonData);
    response.write("API Request Body<br/>");
    response.write(jsonData + "<br/>" + "<br/>");

    var respUPS = nlapiRequestURL(url, jsonData, Headers, "POST");
    var responseCode = respUPS.getCode();

    nlapiLogExecution("DEBUG", "API Response Code", respUPS.getCode());
    nlapiLogExecution("DEBUG", "API Response Body", respUPS.getBody());
    response.write(
      "API Response Code : " + respUPS.getCode() + "<br/>" + "<br/>"
    );
    response.write("API Response Body" + "<br/>");
    response.write(respUPS.getBody() + "<br/>" + "<br/>");

    if (respUPS.getCode() == "200") {
      nlapiLogExecution("DEBUG", "UPS Tracking API", "End - Successfuflly");
      response.write(
        "========================End - Successfuflly=======================<br/><br/>"
      );
      return "Success";
    }

    nlapiLogExecution(
      "DEBUG",
      "UPS Tracking API",
      "End - Occurred some errors"
    );
    response.write("End - Occurred some errors" + "<br/>");
    return 0;
  } catch (e) {
    var err = createErrorLogRecord("Error", "Fatal Error: " + e);
    response.write(JSON.stringify(err));
    return null;
  }
}
