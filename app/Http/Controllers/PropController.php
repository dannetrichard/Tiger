<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class PropController extends Controller
{
    public function import(){
        $files = Storage::files('props');
        foreach($files as $file){
            $this->props($file);
        }
    }
    public function props($file){
        
        $contents = Storage::get($file);
        $contents = preg_replace('/( |\r|\n|\t)*/','', $contents);
        
        /***********************************category_id*****************************/
        preg_match_all('/"catId":(.+?),"mainCatId"/', $contents, $match);
        $category_id = $match[1][0];
        //parent_id
        preg_match_all('/"mainCatId":(.+?),"userId"/', $contents, $match);
        $parent_id = $match[1][0];
        //category_name
        preg_match_all('/"catPath":"类目：(.+?)","canChangeCat"/', $contents, $match);
        $a = explode('\u003E\u003E',$match[1][0]);
        $category_name = array_pop($a);
  
        \App\Category::updateOrCreate(['category_id'=>$category_id],['category_name'=>$category_name,'parent_id'=>$parent_id]);        
        
        /***********************************color size****************************/
        preg_match_all('/"label":"宝贝规格","type":"struct","items":\[(.+?),{"id":"sku","comid":"sku"/', $contents, $match);
        
        $data = json_decode('['.$match[1][0].']',true);
        
        /**********color*************/
        $key_id = str_replace(['wrap_','prop_'],'',$data[0]['id']);
        $key_label = $data[0]['label'];
        $key_type = $data[0]['type'];
        
        foreach($data[0]['groups'] as $group){
            foreach($group['colors'] as $option){
                    $data1 = ['category_id'=>$category_id,'key_id'=>$key_id,'key_label'=>$key_label,'key_type'=>$key_type,'value'=>$option['value'],'text'=>$option['text']];   
                    $data2 = ['category_id'=>$category_id,'key_id'=>$key_id,'value'=>$option['value']];
                    \App\Prop::updateOrCreate($data1,$data2);            
            }    
        }
        
        /**********size**************/
        $key_id = str_replace(['wrap_','prop_'],'',$data[1]['id']);
        $key_label = $data[1]['label'];
        $key_type = $data[1]['type'];
        
        foreach($data[1]['items'] as $item){
            if(isset($item['options'])){
                foreach($item['options'] as $option){
                        $data1 = ['category_id'=>$category_id,'key_id'=>$key_id,'key_label'=>$key_label,'key_type'=>$key_type,'value'=>$option['value'],'text'=>$option['text']];   
                        $data2 = ['category_id'=>$category_id,'key_id'=>$key_id,'value'=>$option['value']];
                        \App\Prop::updateOrCreate($data1,$data2);            
                }                 
            }
        }        
  
        /***********************************props****************************/ 
        preg_match_all('/请认真准确填写！","pos":"top"}],"items":(.+?),"async":{"spu"/', $contents, $match);
        $props = json_decode('{"props":'.$match[1][0].'}',true)['props'];
        
        foreach($props as $prop){ 
            $key_id = str_replace(['wrap_','prop_'],'',$prop['id']);
            $key_label = $prop['label']; 
            $key_type = $prop['type'];
            if($key_id=='20000'){
                $data1 = ['category_id'=>$category_id,'key_id'=>$key_id,'key_label'=>$key_label,'key_type'=>$key_type,'value'=>'29534','text'=>'other/其他'];   
                $data2 = ['category_id'=>$category_id,'key_id'=>$key_id,'value'=>'29534'];
                \App\Prop::updateOrCreate($data1,$data2);
            }else{
                if(isset($prop['items'][0]['options'])){     
                    foreach($prop['items'][0]['options'] as $option){
                        $data1 = ['category_id'=>$category_id,'key_id'=>$key_id,'key_label'=>$key_label,'key_type'=>$key_type,'value'=>$option['value'],'text'=>$option['text']];   
                        $data2 = ['category_id'=>$category_id,'key_id'=>$key_id,'value'=>$option['value']];
                        \App\Prop::updateOrCreate($data1,$data2);
                    }
                }
            }
  
        }
           
    }
}
