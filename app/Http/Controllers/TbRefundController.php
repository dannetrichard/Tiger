<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;

use App\TbRefund;

use App\Code;

class TbRefundController extends Controller
{
    public function import(){
        
        $file_paths = read_dir(storage_path('upload/RefundList'));
       
        Excel::load($file_paths[0], function($reader) {
            
            $results = $reader->toArray();
     
            $keys=[
                    'tid',
                    'refund_id',
                    'alipay_no',
                    'pay_time', 
                    'outer_id', 
                    'refund_complete_time',
                    'buyer_nick',
                    'payment',
                    'title',
                    'refund_fee', 
                    'auto_refund',
                    'has_good_return', 
                    'created', 
                    'timeout', 
                    'status',
                    'good_status',
                    'refund_ship_message', 
                    'send_ship_message',
                    'cs_status',
                    'seller_name',
                    'seller_address',
                    'seller_zip_code',
                    'seller_phone',
                    'seller_mobile',
                    'sid',
                    'company_name',
                    'reason',
                    'desc',
                    'good_return_time',
                    'responsible_party',
                    'refund_phase',
                    'seller_memo',
                    'refund_duration',
                    'part',
                    'verify_by',
                    'burden_of_proof_timeout',
                    'instant_respond',
                    'action_by',];
            
            foreach($results as $k => $v){
                
                list($keys1, $values) = array_divide($v);  
                      
                $results2 = array_combine($keys,$values);  
                
                $results1['refund_id']  = array_pull($results2,'refund_id');
               
                foreach($results2 as $k1=>$v1){
                
                    if($v1==='null'||$v1==='\'null'){
                        
                        $results2[$k1] = null;
                        
                    }
                    
                } 

                $trade = TbRefund::updateOrCreate($results1,$results2);  
                
            }

        },'GBK');
        
        $this->refresh();
            
        dd('Congratulate!');
    }
    
    public function refresh()
    {
        
        $refund =  TbRefund::whereNotIn('express_deliverystatus',[3])->whereNotIn('express_status', [207])->where('good_status','已寄回')->get(); 
        
        foreach($refund as $key => $value){
                
                $data_get = express_query($value['sid']);
                
                $data = [];
                $data['express_status']=$data_get['status'];
                $data['express_msg']=$data_get['msg'];
                
                if($data['express_status']==0){
    
                    $result =  $data_get['result'];
                    $data['express_type'] = $result['type'];
                    $data['express_list'] = json_encode($result['list'],true);
                    $data['express_deliverystatus'] = $result['deliverystatus']; 

                    TbRefund::where('id', $value['id'])->update($data);   

                }elseif($data['express_status']==201||$data['express_status']==202||$data['express_status']==203||$data['express_status']==204||$data['express_status']==205||$data['express_status']==207){
                    
                    
                    TbRefund::where('id', $value['id'])->update($data);                   
                    
                }
                
        }
        
    }    

    public function agree_to_refund_check(){
        //agree_to_refund 全0
        TbRefund::where('agree_to_refund',1)->update(['agree_to_refund'=>0]);
        
        $refund = TbRefund::where('status','买家已经退货，等待卖家确认收货')->where('express_deliverystatus',3)->get();
        foreach($refund as $key => $val){
            
            $code = Code::where('number',$val['sid'])->get();
            
            if(!$code->isEmpty()){
                TbRefund::where('id',$val['id'])->update(['agree_to_refund'=>1]);
            }
            
        }        

    }
    
    public function agree_to_refund_list(){
     
        $this->agree_to_refund_check();
        
        $refund = TbRefund::where('agree_to_refund',1)->orderBy('created','desc')->get(['tid'])->toArray();     

        return $refund;            
    }    
    
    public function lost_check(){
        //lost 全0
        TbRefund::where('lost',1)->update(['lost'=>0]);    
            
        $refund = TbRefund::where('express_deliverystatus',3)->get();
       
        foreach($refund as $key => $val){
            
            //codes中的error表示收到的退货有没有问题
            $code = Code::where('number',$val['sid'])->get();
            
            if($code->isEmpty()){
                TbRefund::where('id',$val['id'])->update(['lost'=>1]);
            }
            
            
        }        
          
    }
    public function lost_list(){
        
        $this->lost_check();
        
        $refund = TbRefund::where('lost',1)->where('created','>','2017-09-01')->orderBy('express_type')->orderBy('created','desc')->get(['sid','express_list','express_type'])->toArray();     

        return $refund;
          
    }
      
    public function timeout_list(){
        
        $the_day_after_tomorrow = date("Y-m-d",strtotime("+2 day"));

        $refund = TbRefund::where('status','买家已经退货，等待卖家确认收货')->where('timeout','<',$the_day_after_tomorrow)->orderBy('good_return_time','desc')->get(['tid','timeout'])->toArray();     

        return $refund;            
    }

    public function has_good_return_list(){
        
        $refund = TbRefund::where('has_good_return','仅退款')->whereNotIn('good_status',['未发货'])->whereNotIn('status',['退款关闭','退款成功'])->orderBy('created','desc')->get(['tid','timeout'])->toArray();     

        return $refund;            
    
    }
    
    public function data(){
        
        $lists['agree_to_refund_list'] = $this->agree_to_refund_list();
        $lists['lost_list'] = $this->lost_list();
        $lists['timeout_list'] = $this->timeout_list();
        $lists['has_good_return_list'] = $this->has_good_return_list();
        
        //dd($lists);
        return view('home',$lists);
    }     
             
}
