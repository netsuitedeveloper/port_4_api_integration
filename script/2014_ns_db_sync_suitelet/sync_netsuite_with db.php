<?php
    require_once "database.php";

    function curl_get_contents($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    
    $fp = fopen('cookie.ini', 'w+');

    $sql = "SELECT id FROM tbl_wms_order_dispatch ORDER BY id DESC LIMIT 1";
    $result=mysql_query($sql);
    $row = mysql_fetch_object($result);
    $id_arr=mysql_fetch_array($result);
    $last_id = $row->id;
    
    echo $last_id;
    
    if (empty($last_id))
        $last_id = 11999730;

    $url = 'https://forms.na1.netsuite.com/app/site/hosting/scriptlet.nl?script=835&deploy=1&compid=3692243&h=9584870079a3bf35db18&last_intid='.$last_id;
    $raw_data = curl_get_contents($url);

    //$raw_data = file_get_contents("https://forms.na1.netsuite.com/app/site/hosting/scriptlet.nl?script=835&deploy=1&compid=3692243&h=9584870079a3bf35db18&last_intid=11999730");
    //$raw_data = str_replace('\r',"",$raw_data);
    //$raw_data = str_replace('\n',"",$raw_data);
    $raw_data = str_replace("'","&#39;",$raw_data);
    $raw_data = stripslashes($raw_data);

    $pos =strpos($raw_data,']',0);
    $raw_data = substr($raw_data,0,$pos+1);

    fwrite($fp, $raw_data);    

    $raw_data = json_decode($raw_data);    

    $index  = 1;
    foreach($raw_data as $item){
        if (!in_array($item->id, $id_arr)){            

            $sql = "INSERT INTO tbl_wms_order_dispatch SET id=".$item->id.", order_number='".$item->ordernum."', shipping_address='".$item->addr."', shipping_method='".$item->method."', status='".$item->status."', order_type='".$item->type."', order_priority='".intval($item->priority)."'";
            mysql_query($sql);

            $sql = "INSERT INTO tbl_wms_order_dispatch_items SET order_id='".$item->ordernum."', sku='".$item->sku."', quantity=".intval($item->qty).", location=".intval($item->location).", status='".$item->status."'";
            mysql_query($sql);

            $id_arr[] = $item->id;

        }else{
            $sql = "INSERT INTO tbl_wms_order_dispatch_items SET order_id='".$item->ordernum."', sku='".$item->sku."', quantity=".intval($item->qty).", location=".intval($item->location).", status='".$item->status."'";
            mysql_query($sql);
        }

        fwrite($fp, $item->id);

        $index++;
        //if($index==200) exit;
    }
    fclose($fp);

    echo count($raw_data);
?>
