<?php

/*
 * Custom PayPal API Class
 * @author: Hakuna Moni
 * @modified: 8/14/2015
 * @version: v1.0.0
 */

class PayPalAPI {
    
    // PayPal API endpoint url
    private $endpoint;
    
    // API Username
    private $api_user;
    
    // API Password
    private $api_pass;
    
    // API Signature
    private $api_signature;
        
    public function __construct($endpoint = "", $api_user = "", $api_pass = "", $api_signature = ""){
        
        if ($endpoint == ""){
            //$endpoint = "https://api-3t.paypal.com/nvp";
            $endpoint = "https://api-3t.sandbox.paypal.com/nvp";
        }        
        $this->endpoint = $endpoint;
        
        if ($api_user == ""){
            $api_user = "aaa";
        }
        $this->api_user = $api_user;
        
        if ($api_pass == ""){
            $api_pass = "aaa";
        }
        $this->api_pass = $api_pass;
        
        if ($api_signature == ""){
            $api_signature = "aaa";
        }
        $this->api_signature = $api_signature;
        
    }
    
    /*
     * Get response from PayPal API
     * @param string $query
     * @param string $method
     * @return mixed array
     */
    public function call($query = "", $method = ""){
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $nvpheader = "&VERSION=94&PWD=" . urlencode($this->api_pass) . "&USER=".urlencode($this->api_user) . "&SIGNATURE=" . urlencode($this->api_signature);
        
        $headers_array[] = "X-PP-AUTHORIZATION: " . $nvpheader;
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        $nvpStr = $nvpheader . $query;
        
        $nvpreq = "&METHOD=" . urlencode($method) . $nvpStr;
        curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);
        
        $response = curl_exec($ch);
        
        return $this->deformatNVP($response);
    }
    
    /*
     * Convert response data into array format
     * @param string $nvpstr
     * @return mixed array
     */    
    public function deformatNVP($nvpstr){

        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr)){
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
         }
        return $nvpArray;
    }
}
?>
