<?php 
function read_dir ($dir){
    if(!is_dir($dir)) return false;     
    $handle = opendir($dir);
    if($handle){
        while(($fl = readdir($handle)) !== false){
            $temp = $dir.DIRECTORY_SEPARATOR.$fl;
            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
            if(is_dir($temp) && $fl!='.' && $fl != '..'){
                $data[] = read_all($temp);
            }else{
                if($fl!='.' && $fl != '..'){
    
                    $data[] = $temp;
                }
            }
        }
    }
    array_multisort($data,SORT_DESC);
    return $data;
}

function express_query($number){

        $host = 'http://jisukdcx.market.alicloudapi.com';
        $path = '/express/query';
        $method = 'GET';
        $appcode = '8d2eb17a4b2244ddb95ca61a01848814';
        $headers = array();
        array_push($headers, 'Authorization:APPCODE ' . $appcode);
        $querys = 'number='.$number.'&type=auto';
        $bodys = '';
        $url = $host . $path . '?' . $querys;
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        return json_decode(curl_exec($curl),true);
        
}


function time_difference($date1){
    
    $date2=date('y-m-d H:i:s');    
                
    $hour=floor((strtotime($date1)-strtotime($date2))/3600);  
 
    return $hour;
    
}

function curl_json($url) {
    $status = true;
    while ($status) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	    $output = curl_exec($ch);
	    $curl_errno = curl_errno($ch);
	    $curl_error = curl_error($ch);
	    curl_close($ch);
	    if ($curl_errno == 0) {
	    	$status = false;
	    } else {
	    	echo $curl_error;
	    	echo "\n";
	    }
    }
    return $output;
}
function curl_array($url) {
    $status = true;
    while ($status) {
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    	$output = curl_exec($ch);
    	$curl_errno = curl_errno($ch);
    	$curl_error = curl_error($ch);
    	curl_close($ch);
    	if ($curl_errno == 0) {
    		$status = false;
    	} else {
    	    echo $curl_error;
    	    echo "\n";
    	}
    }
    return json_decode($output, true);
}
function curl_array_post($url, $post_data) {
    $status = true;
    while ($status) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	    $output = curl_exec($ch);
	    $curl_errno = curl_errno($ch);
	    $curl_error = curl_error($ch);
	    curl_close($ch);
	    if ($curl_errno == 0) {
	    	$data = json_decode($output, true);
	    	if(isset($data['data'][0]['create'])){
	    			if($data['data'][0]['create']>1000){
	    					$status = false;
	    			}
	    	}	    	
	    } else {
	    	echo $curl_error;
	    	echo "\n";
	    }
    }
    return $data;
}
?>