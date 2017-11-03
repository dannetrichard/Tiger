<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;

use App\SCode;

class SCodeController extends Controller
{
    public function import(){
        
        Excel::batch(storage_path('upload/SCode'), function($reader) {

           
            $results = $reader->noHeading()->ignoreEmpty()->toArray();
            
            array_pop($results );
            
            $keys=[ 'number',
                    'effective_time',
                    'c',
                    'destination',
                    'e',
                    'weight',
                    'shop',
                    'price',];     
            $keys1=[ 'number',
                    'effective_time',
                    'c',
                    'destination',
                    'e',
                    'weight',
                    'price',];       
                                        
            foreach($results as $k => $v){

                if(count($v) == 8){
                    $record = array_combine($keys,$v);
                }else{
                    $record = array_combine($keys1,$v);  
                }
                
                
                
                
                //查重复
                $s_codes = SCode::where('number',$record['number'])->get();
                
                if(!$s_codes->isEmpty()){
                    foreach($s_codes->toArray() as $value){
                        SCode::where('number',$value['number'])->update(['repeat'=>1]);
                    }; 
                    $record['repeat'] = 1;  
                }
                
                
                
                $s_code = SCode::create($record);   
                
            }
            
        });
        
        
        
    }

    public function list(){
        dd(SCode::where('repeat',1)->get()->toArray());
   }
}
