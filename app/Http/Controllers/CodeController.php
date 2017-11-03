<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Code;

class CodeController extends Controller
{
  public function import(Request $request){
    
    $number = $request->input('number');
    
    $quantity = $request->input('quantity');
    
    $data = express_query($number);
    
    if($data['status']==0){
 
        $code = Code::updateOrCreate(['number' => $number], ['quantity' => $quantity]);
                 
    }
        
    return $data;
     
  }    
  
  public function code_today(){
        return Code::where('created_at','>',date('Y-m-d', time()))->count();
  }
}
