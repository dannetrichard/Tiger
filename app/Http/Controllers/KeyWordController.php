<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class KeyWordController extends Controller
{
    
    public function sug($q=['棉衣','毛呢'],$area=['c2c','wireless']){
    	
    		foreach($q as $v1){

    			foreach($area as $v2){
    				
    				$url = 'https://suggest.taobao.com/sug?code=utf-8&q='.$v1.'&_ksTS=1509502426522_2704&callback=&k=1&area='.$v2.'&bucketid=3';
    				//$url = 'http://suggest.taobao.com/sug?code=utf-8&q='.$v1.'&area='.$v2;

    				$response[$v1][$v2] = curl_array($url);
    				
    			}
    			
    		}
    	
    		dd($response);  
    }

}
