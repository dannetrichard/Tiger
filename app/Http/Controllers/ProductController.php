<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;

use Image;

use Carbon\Carbon;

use Zipper;

use App\Product;

use App\Shop;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function last($shop_names=[]){
        if($shop_names==[]){
            $shops = Shop::all()->toArray(); 
        }else{
            $shops = Shop::whereIn('shop_name',$shop_names)->get()->toArray(); 
        } 
        if($shops==[]){
            dd('error:no shop!');
        }
        foreach($shops as $shop){
            $items = $this->shop_item_search($shop['shop_id']);
            if(!$items){
                continue;
            }
            echo $shop['shop_name'];
            echo "\n";
            $created = $this->created($items[0]['item_id']);
            echo "\t";
            echo "last update";
            echo "\t";
            echo $created;
            echo "\n";
        }
               
    }
    public function clear(){
        Storage::deleteDirectory('picture');
        Storage::deleteDirectory('description'); 
        DB::delete('delete from products');    
    }
    public function import($shop_names=[],$from=1,$to=0){
        dd(dorequest(['125.112.193.140','35614'],'http://hws.m.taobao.com/cache/wdetail/5.0?id=545074571829'));
        if($from<=$to){
            dd('error:from<to!');    
        }        
        if($shop_names==[]){
            $shops = Shop::all()->toArray(); 
        }else{
            $shops = Shop::whereIn('shop_name',$shop_names)->get()->toArray(); 
        }
        if($shops==[]){
            dd('error:no shop!');
        }
        
        $f = Carbon::tomorrow()->subDays($from);
        if($to==0){
            $t = Carbon::now();
        }else{
           $t = Carbon::tomorrow()->subDays($to); 
        }
        
        
                
        Storage::makeDirectory('picture');
        Storage::makeDirectory('description');   
        foreach($shops as $shop){

            $items = $this->shop_item_search($shop['shop_id']);
            if(!$items){
                continue;
            }
            
            echo $shop['shop_name'];
            echo "\n";
            foreach($items as $item){
                /*
                $problem = ['560335821671','560782619647','560519364684','560241628507','560127812652'];
                
                if(in_array($item['item_id'], $problem)){
                    continue;
                }
                */
                //记录已存在
                if(Product::where('num_id',$item['item_id'])->get()->isNotEmpty()){
                    echo "\t";
                    echo "exsist";
                    echo "\t";
                    echo $item['item_id'];
                    echo "\n";
                    if(session('tips',1)==1){
                        session(['tips'=>0]);
                        continue;
                    } 
                    break;    
                }
                //限制from,to
                
                if($this->time_threshold($item['item_id'],$f,$t)){
                    echo "\t";
                    echo 'no use';
                    echo "\t";
                    echo $item['item_id'];
                    echo "\n";         
                    if(session('tips')==1){
                        session(['tips'=>0]);
                        continue;
                    }        
                    break;
                }
                          
        
                    echo "\t";
                    echo "new";
                    echo "\t";
                    echo $item['item_id'];
                    echo "\n";               
                $detail = $this->wdetail($item['item_id']);
                $this->combine_data($item,$shop['shop_name']);     
            }
            
        }
        
        if($shop_names!=[]){
            $directory = implode(' | ',$shop_names);    
        }else{
            $directory = 'all'; 
        }
        $directory = $directory.' from '.$f->toDateString().' to '.$t->toDateString();        
        $this->export($directory);
    }
    public function export($directory){
        echo "exporting...";
        echo "\n";

        Storage::makeDirectory($directory);
        Storage::makeDirectory($directory.'/data');
        Storage::makeDirectory($directory.'/description');
            
        $products = Product::get(['picture_copy','description']);
        if($products->isEmpty()){
            dd('no product!');    
        }
              
        foreach($products->toArray() as $product){
            $pictures = json_decode($product['picture_copy']);
            foreach($pictures as $pic){
                $from = 'picture/'.$pic;
                $a = explode('.',$pic);
                $to = $directory.'/data/'.$a[0].'.TBI';
                if(!Storage::exists($from)){return false; }
                if(!Storage::exists($to)){
                    Storage::copy($from,$to);
                }
            } 
    
            $description_pictures = json_decode($product['description']);
            foreach($description_pictures as $description_pic){
                $from = 'description/'.$description_pic;
                $to = $directory.'/description/'.$description_pic;
                if(!Storage::exists($from)){return false; }
                if(!Storage::exists($to)){
                    Storage::copy($from,$to);
                }                  
            }                
        }

        $keys= [
                    'title',
                    'cid',
                    'seller_cids',
                    'location_state',
                    'location_city',
                    'price',
                    'num',
                    'approve_status',
                    'has_showcase',
                    'list_time',
                    'description',
                    'cateProps',
                    'postage_id',
                    'has_discount', 
                    'picture',
                    'skuProps',
                    'outer_id',
                    'sub_stock_type',
                    'item_weight',
                    'sell_promise',
                    'subtitle',
                    'cpv_memo',
                    'input_custom_cpv',
                    ];         
        
        $products = Product::get($keys);

        foreach($products->toArray() as $k=>$product){
            $pics = json_decode($product['description']);
            $s = '';
            foreach($pics as $pic){
                $s = $s.'<IMG src="FILE:///E:\\csv\\'.$directory.'\\description\\'.$pic.'" align=middle>';
            } 
            $products[$k]['description'] = $s;
        }
      
        
        Excel::create('data', function($excel)use($products) {
            $excel->sheet('Sheet1', function($sheet)use($products) {        

                $sheet->fromArray($products);
                $sheet->prependRow(1,['version 1.00']);
                $sheet->prependRow(3,['宝贝名称','宝贝类目','店铺类目']);
            });
        })->store('csv',storage_path('app/'.$directory));          
        
           
        $files = storage_path('app/'.$directory);
        Zipper::make('storage/app/'.$directory.'.zip')->add($files)->close();
        Storage::deleteDirectory($directory);
        Storage::deleteDirectory('picture');
        Storage::deleteDirectory('description'); 
        DB::delete('delete from products');
        echo "\t";  
        echo "file name";
        echo "\t";  
        echo $directory.'.zip';
        echo "\n";
        echo "come on! Fighting now!";   
        echo "\n";       
    }
    public function shop_item_search($shopId='112753614'){
            $url = 'http://api.s.m.taobao.com/search.json?m=shopitemsearch&n=40&page=1&sort=oldstarts&shopId='.$shopId;     
            $data = curl_array($url); 
            $items = $data['itemsArray'];  
            $totalPage = $data['totalPage'];
            if($totalPage  > 1){ 
                    for ($page=2; $page<=$totalPage; $page++) {                   
                        $url = 'http://api.s.m.taobao.com/search.json?m=shopitemsearch&n=40&page='.$page.'&sort=oldstarts&shopId='.$shopId;                  
                        $data = curl_array($url);  
                        try{
                           $items = array_merge($items,$data['itemsArray']);  
                        }catch(\Exception $e){                            
                           $page--;
                        }                    
                                            
                    }                  
            }            
            //截图最近100个
            if(isset($items[0])){
                $limit = 30;
                $items = array_slice($items,0,$limit);     
                return $items;           
            }else{
                return false;    
            }

            
    }
    
    public function time_threshold($item_id,$f,$t){

        $created = $this->created($item_id);
        echo "\t";
        echo $created;
        $c = Carbon::parse($created);
        if($c->gt($t)){
            session(['tips'=>1]);    
        }
        
        if($c->gt($f)&&$c->lte($t)){
            return false;    
        }
        return true;
    }    
    public function pre_combine($detail){
		//处理skuProps、ppathIdmap并返回
        $skuProps=$detail['skuModel']['skuProps'];
        $ppathIdmap = array_flip($detail['skuModel']['ppathIdmap']);
        foreach($skuProps as $j=>$skuProp){
            $input_value = -1001;
            foreach($skuProp['values'] as $k=>$value){
                //名称和属性分离
                $a = explode(' ',$value['name']);
                if(count($a)==2){
                    $skuProps[$j]['values'][$k]['name']=$a[0];
                    $skuProps[$j]['values'][$k]['memo']=$a[1];
                }
                //自定义属性更改值  
                $props = \App\Prop::where('value',$value['valueId'])->get();                
                if($props->isEmpty()){
                     foreach($ppathIdmap as $i=>$ppath){
                        $ppathIdmap[$i] = str_replace($value['valueId'],$input_value,$ppath);
                     }
                     $skuProps[$j]['values'][$k]['valueId'] = $input_value;
                     $input_value = $input_value -1;     
                }
            }
        }      
        $detail['skuModel']['skuProps'] = $skuProps;
        $detail['skuModel']['ppathIdmap'] = $ppathIdmap;
        return $detail;
    }
    public function wdetail($id='555372768612'){
        $url = 'http://hws.m.taobao.com/cache/wdetail/5.0?id=' . $id;
		$detail = curl_array($url) ['data'];
		
		return $detail;
    }   
    public function outer_id($title_processed,$shop_name){

        $instock        = $title_processed['instock']==1        ? '-已出货'                               : '';
        $recommend      = $title_processed['recommend']==1      ? '-主推'                                 : '';
        $owners         = $title_processed['owners']!==1        ? '-实拍'                                 : '';        
        $limit_price    = $title_processed['limit_price']!=0    ? '-限价'.$title_processed['limit_price'] : '';
        $dealer_price   = $title_processed['dealer_price']!=0   ? '-P'.$title_processed['dealer_price']   : '';
        $article_number = '-'.$title_processed['article_number'];
        $presale        = $title_processed['presale']==1        ? '-预售'                                 : '';

        if(($title_processed['presale']==1)&&($title_processed['presale_date']!='')){
            $presale =  $presale.'到'.$title_processed['presale_date'];
        }

        $outer_id = $shop_name.$article_number.$dealer_price.$limit_price.$instock.$recommend.$owners.$presale;   
        return $outer_id;      
    }
    public function seller_cids($cid){
        //cid=>seller_cids
        $cid_list = [
                    '50008900'=>'1340125112',
                    '50013194'=>'1340125113',
                    '50008898'=>'1294330315',
                    '162103'=>'1255457751',
                    '50008901'=>'1294330316',
                    ];
        if(isset($cid_list[$cid])){
            $seller_cids = $cid_list[$cid];
        }else{
            $seller_cids = null;    
        }
        return $seller_cids; 
    }
    public function desc($url){
        $output = curl_json($url);
        preg_match_all('/https?\:\/\/img.alicdn.com\/(.+?)(\.jpg|\.png|\.gif)/', $output, $match);
        $picture = [];
        if (isset($match[0][0])) {       
            foreach($match[0] as $value){     
                $name = $this->description_picture($value);
                if($name){
                    $picture[] = $name;
                }
            }
        }
        return $picture;
    }   
    public function description_picture($url){
        //注意链接里面含有多个.
        $a = explode('/',$url);
        $s = array_pop($a);
        $a = explode('.',$s);
        //格式
        $s = array_pop($a);
        //图片名
        $name = implode('',$a);
        //图片名md5
        $name = md5($name);
        //图片名加格式
        $name = $name.'.'.$s;
        if(!Storage::exists('description/'.$name)){
            $path = storage_path('app/description/'.$name);
            try{

                $image = Image::make($url)->save($path); 
            }catch(\Exception $e){
                return false;    
            } 
        }
        return $name; 
    }
    public function cateProps($detail){
        $s='';
        $skuProps = $detail['skuModel']['skuProps'];
        $categoryId =$detail['itemInfoModel']['categoryId'];
        
        if(isset($detail['props'][0])){
            foreach($detail['props'] as $prop){
                if($prop['name']=='品牌'){
                    $s = $s.'20000:29534;';
                }else{
                    $pp = \App\Prop::where('category_id',$categoryId)->where('key_label',$prop['name'])->where('text',$prop['value'])->first();
                    if($pp){
                       $s = $s.$pp->key_id.':'.$pp->value.';';
                    }                
                }
            }
        }
        
        foreach($skuProps as $skuProp){
            foreach($skuProp['values'] as $value){
                $s = $s.$skuProp['propId'].':'.$value['valueId'].';';
            }
        }
        
        return $s;
    }
    public function pic_data($item,$detail){
        
        $s = ''; 
        $picture = [];
        $picsPath = $detail['itemInfoModel']['picsPath'];

        foreach($picsPath as $key => $value){
            $name =  $this->picture($value);       
            if($name){
                $picture[]= $name[1];
                $s = $s.$name[0].':1:'.$key.':|;';                
            }
        }

        if(isset($item['uprightImg'])){
          $name = $this->picture($item['uprightImg']);
          if($name){
            $picture[] = $name[1];
            $s = $s.$name[0].':1:5:|;';    
          }
        }

        $color=$detail['skuModel']['skuProps'][1];
 
        foreach($color['values'] as $value){
            if(isset($value['imgUrl'])){
                $name = $this->picture($value['imgUrl']);
                if($name){
                    $picture[] = $name[1];
                    $s = $s.$name[0].':2:0:'.$color['propId'].':'.$value['valueId'].'|;';   
                }
            } 
        }
        
        return ['picture'=>$s,'picture_copy'=>json_encode($picture)];
    }
    public function picture($url){
        //注意链接里面含有多个.
        $a = explode('/',$url);
        $s = array_pop($a);
        $a = explode('.',$s);
        //格式
        $s = array_pop($a);
        //图片名
        $name = implode('',$a);
        //图片名md5
        $name = md5($name);
        //保存路径

        if(!Storage::exists('picture/'.$name.'.'.$s)){
            $path = storage_path('app/picture/'.$name.'.'.$s);
            try{
                Image::make($url)->flip('h')->save($path);
            }catch(\Exception $e){
                return false;
            }
        }
        return [$name,$name.'.'.$s];  
    }
    public function skuProps($detail){
        $s = '';
        $skus = json_decode($detail['apiStack'][0]['value'],true)['data']['skuModel']['skus'];
        $ppathIdmap = $detail['skuModel']['ppathIdmap'];
 
        foreach($skus as $key => $value){
            
            $price = intval(array_pop($value['priceUnits'])['price']);
            $quantity = $value['quantity'];
            $props = $ppathIdmap[$key];
            $s = $s.$price.':'.$quantity.'::'.$props.';';
            
        }
        return $s;        
    }
    public function cpv_memo($detail){
        
        $s='';
        $skuProps=$detail['skuModel']['skuProps'];
        foreach($skuProps as $skuProp){
            foreach($skuProp['values'] as $value){
                if(isset($value['memo'])){
                    $s = $s.$skuProp['propId'].':'.$value['valueId'].':'.$value['memo'].';';
                }                
            }
        }        
        return $s;        
    }
    public function input_custom_cpv($detail){
        
        $s='';
        $skuProps=$detail['skuModel']['skuProps'];
        foreach($skuProps as $skuProp){
            foreach($skuProp['values'] as $value){
                
                if($value['valueId']<0){
                    $s = $s.$skuProp['propId'].':'.$value['valueId'].':'.$value['name'].';';
                }
     
            }
        }        
        return $s;   
    }
    public function combine_data($item,$shop_name){
        $detail = $this->wdetail($item['item_id']);
        
        if(!isset($detail['skuModel']['skuProps'][0]['values'][0])){
            return false;
        }
        if(!isset($detail['skuModel']['skuProps'][1]['values'][0])){
            return false;
        }

            
       $detail = $this->pre_combine($detail);		    
     

        /*********************************title\title_copy****************************************/
        $title = $detail['itemInfoModel']['title']; 
        $title_processed = $this->name_process($title);
        
        $data['title'] = $title_processed['name'];
        $data['title_copy'] = $title;

       /*********************************outer_id         ****************************************/
 
        $data['outer_id'] = $this->outer_id($title_processed,$shop_name);        
        
        /********************************cid              ****************************************/
        $data['cid'] = $detail['itemInfoModel']['categoryId'];
        /********************************seller_cids      ****************************************/
        $data['seller_cids'] = $this->seller_cids($detail['itemInfoModel']['categoryId']);
        /********************************
        location_state
        location_city 
        price    
        num 
        approve_status立刻上架1定时上架、放入仓库2
        has_showcase橱窗推荐  
        list_time定时上架时间
        ****************************************/
        $data['location_state'] = '浙江';
        $data['location_city'] = '杭州';
        $data['price'] = $item['price'];
        $data['num'] = $item['quantity'];        
        $data['approve_status'] = 2;
        $data['has_showcase'] = 1;      
        $data['list_time'] = null;
        /*********************************description****************************************/
        $description = $this->desc($detail['descInfo']['fullDescUrl']);
        $data['description'] = json_encode($description); 
        /*********************************cateProps****************************************/
   
        $data['cateProps'] = $this->cateProps($detail);
        
        /*********************************postage_id****************************************/
        
        $data['postage_id'] = '10149716421';
        
        /*********************************has_discount****************************************/
        
        $data['has_discount'] = 1;//会员打折
        
        
       /*********************************picture****************************************/

        $name = $this->pic_data($item,$detail);
        $data['picture'] = $name['picture'];
        $data['picture_copy']= $name['picture_copy'];
  
       /*********************************video****************************************/
       
       $data['video'] = null;
       
       /*********************************skuprops****************************************/
       
        $data['skuProps'] = $this->skuProps($detail);
        
        /*********************************num_id****************************************/
        
        $data['num_id'] = $item['item_id'];
 
        /*********************************cpv_memo****************************************/
      
        $data['cpv_memo'] = $this->cpv_memo($detail);

        /*********************************input_custom_cpv****************************************/

        $data['input_custom_cpv'] = $this->input_custom_cpv($detail);       

        Product::updateOrCreate(['num_id'=>$data['num_id']],$data);  


    } 
    public function name_process($name){
			$name_moved = preg_replace('/( |　|	|	|\s|\n|\r|\t|2017)*/','', $name);	
			$dealer_price = 0;
			$limit_price = 0;
			$article_number ='';
			$owners = 0;
			$recommend = 0;
			$instock=0;
			$presale = 0;
			$presale_date = '';
            
			//------------------货号、价格
			$regexs= [
									'#\d+([A-za-z]+\d+)(-p|\/p|p|-f|\/f|f)(\d*)#i',
									'#\d+([A-za-z]+\d+)(-p|\/p|p|-f|\/f|f) #i',
									'#\w+(-|\/)(([A-za-z]*\d+)|(\(.+\)))(-p|\/p|p|-f|\/f|f)(\d*)#i',
									'#([A-za-z]*\d+)(-p|\/p|p|-f|\/f|f)(\d*)#i',
									'#([A-za-z]*\d+)(-p|\/p|p|-f|\/f|f)(\d*)#i',
									'#p(\d+)#i',
									];
			if(preg_match($regexs[0], $name_moved, $matches)){
							$article_number = $matches[1];
							$dealer_price = $matches[3];
							$name_moved = preg_replace($regexs[0],'',$name_moved);
			}elseif(preg_match($regexs[1], $name_moved, $matches)){
							$article_number = $matches[1];
							$name_moved = preg_replace($regexs[1],'',$name_moved);
			}elseif(preg_match($regexs[2], $name_moved, $matches)){
							$article_number = $matches[2];
							$dealer_price = $matches[6];
							$name_moved = preg_replace($regexs[2],'',$name_moved);
			}elseif(preg_match($regexs[3], $name_moved, $matches)){
							$article_number = $matches[1];
							$dealer_price = $matches[3];
							$name_moved = preg_replace($regexs[3],'',$name_moved);
			}elseif(preg_match($regexs[4], $name_moved, $matches)){
							$dealer_price = $matches[2];
							$name_moved = preg_replace($regexs[4],'',$name_moved);
			}elseif(preg_match($regexs[5], $name_moved, $matches)){
							$dealer_price = $matches[1];
							$name_moved = preg_replace($regexs[5],'',$name_moved);
			}
			
					
            //------------------预售
			
			$regexs = [	'#(\d+)月(\d+)(号)*出货#',
									'#(\d+)(号|日)(出货|大货)(！|!)*#',
									'#提前出货(\d+)号#',
									];
			
			if(preg_match($regexs[0], $name_moved, $matches) && ($matches[1] == date('m') || $matches[1] == date('m') +1) && $matches[2]<=31){			   
				$presale = 1;
				$presale_date = date('Y').'-'.str_pad($matches[1],2,'0',STR_PAD_LEFT).'-'.str_pad($matches[2],2,'0',STR_PAD_LEFT);
				$name_moved = preg_replace($regexs[0],'',$name_moved);		
			}elseif(preg_match($regexs[1], $name_moved, $matches)&& $matches[1]<=31){
				$presale = 1;
				$presale_date = date('Y').'-'.date('m').'-'.str_pad($matches[1],2,'0',STR_PAD_LEFT);
				$name_moved = preg_replace($regexs[1],'',$name_moved);				
			}elseif(preg_match($regexs[2], $name_moved, $matches)&& $matches[1]<=31){
				$presale = 1;
				$presale_date = date('Y').'-'.date('m').'-'.str_pad($matches[1],2,'0',STR_PAD_LEFT);
				$name_moved = preg_replace($regexs[2],'',$name_moved);				
			}	
						
			if($presale==1&& strtotime(date("Y-m-d")) < strtotime($presale_date)){
			    $product['presale'] = $presale;			
			    $product['presale_date'] = $presale_date;				
			}			
			//------------------控价
			$regex = '#(控|控价|控价最低|价格不低于|售价不低于|卖价不低于|控价不能低于|严格控价不得低于|最低售价不得低于|最低价格|最低限价|限价|控价不低于|控价最低卖价)(\d+)(\+|元|块)*#';
			if(preg_match($regex, $name_moved, $matches)){
				$limit_price = $matches[2];
				$name_moved = preg_replace($regex,'',$name_moved);
			}				
			
			$name_moved = preg_replace('/(【|】|（|）|\(|\))*/','', $name_moved);
			//------------------实拍
			
			$regex = '#(模特)*实拍-*(！|!)*#';
			if(preg_match($regex, $name_moved, $matches)){
				$owners = 1;
				$name_moved = preg_replace($regex,'',$name_moved);		
			}				
			
			//------------------主推
			
			$regex = '#(大货主推款|大货主推|主推款|主推爆款|爆款主推|主推|爆款)(！|!)*#';   
			if(preg_match($regex, $name_moved, $matches)){
				$recommend = 1;
				$name_moved = preg_replace($regex,'',$name_moved);		
			}					
			//------------------现货
			
			$regex = '#大货已出(！|!)*|已出货|现货-*|大量现货#';
			if(preg_match($regex, $name_moved, $matches)){
				$instock = 1;
				$name_moved = preg_replace($regex,'',$name_moved);		
			}	
				
				
	      
			
            $data = ['name'=>$name_moved,
                        'instock'=>$instock,
                        'recommend'=>$recommend,
                        'owners'=>$owners,
                        'limit_price'=>$limit_price,
                        'dealer_price'=>$dealer_price,
                        'article_number'=>$article_number,
                        'presale'=>$presale,
                        'presale_date'=>$presale_date,
            ];
                
	        return $data;					      						    
    }
    
    public function created($id) {
		    $url = 'https://www.taodaxiang.com/shelf/index/get';
		    $post_data = array('pattern' => '1', 'wwid' => '', 'goodid' => $id, 'page' => '1');
		    $data = curl_array_post($url, $post_data);
		    $created = date("Y-m-d H:i:s", $data['data'][0]['create']);
		    return $created;
		    
    } 


}    