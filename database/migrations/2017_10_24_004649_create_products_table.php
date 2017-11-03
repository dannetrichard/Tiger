<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            
           
            $table->string('title')->nullable();
            $table->unsignedInteger('cid')->nullable();
            $table->string('seller_cids')->nullable();//店铺分类
            $table->string('location_state')->default('浙江');
            $table->string('location_city')->default('杭州');
            $table->decimal('price',5,2)->nullable();
            $table->unsignedInteger('num')->nullable();
			$table->unsignedInteger('approve_status')->default(1);//1立刻上架2定时上架			
			$table->boolean('has_showcase')->default(1);//橱窗推荐		
			$table->dateTime('list_time')->nullable();//上架时间
			$table->text('description')->nullable();//宝贝描述
			$table->text('cateProps')->nullable();//宝贝属性
			$table->string('postage_id')->nullable();//邮费模板ID
			$table->boolean('has_discount')->default(1);//店铺vip		
			$table->text('picture')->nullable();
			$table->string('video')->nullable();//做记录
			$table->text('skuProps')->nullable();
			$table->string('outer_id')->nullable();
			$table->string('num_id')->unique();					
			$table->string('sub_stock_type')->default(2);//库存计数方式
			$table->decimal('item_weight',5,1)->default(1);
			$table->string('sell_promise')->default(1);//退换货承诺
			$table->string('newprepay')->default(1);//7天退货
			$table->string('subtitle')->nullable();
			$table->string('cpv_memo')->nullable();//属性值备注
			$table->string('input_custom_cpv')->nullable();//自定义属性

            $table->text('title_copy')->nullable();
            $table->text('picture_copy')->nullable();
/*            
            
            
            
            $table->string('title');
            $table->unsignedInteger('cid');
            $table->string('seller_cids')->nullable();
            $table->enum('stuff_status',[1,2,3,4])->default(1);
            $table->string('location_state')->default('浙江');
            $table->string('location_city')->default('杭州');
            $table->enum('item_type',[1,2,3,4])->default(1);
            $table->decimal('price',5,2);
            $table->decimal('auction_increment',5,2)->nullable();
            $table->unsignedInteger('num');
            $table->unsignedInteger('valid_thru')->default(0);
            $table->unsignedInteger('freight_payer')->default(0);
            $table->float('post_fee');
            $table->float('ems_fee');
			$table->decimal('express_fee',5,2)->default(0);				
			$table->boolean('has_invoice')->default(0);				
			$table->boolean('has_warranty')->default(0);	
			$table->enum('approve_status',[1,2,3,4])->default(1);	
			$table->boolean('has_showcase')->default(0);		
			$table->dateTime('list_time')->nullable();	
			$table->text('description');
			$table->string('cateprops');
			$table->unsignedInteger('postage_id');
			$table->boolean('has_discount')->default(0);	
			$table->dateTime('modified')->default(\DB::raw('CURRENT_TIMESTAMP'));	
			$table->string('upload_fail_msg')->default(200);	
			$table->string('picture_status');
			$table->unsignedInteger('auction_point')->default(0);
			$table->string('picture');
			$table->string('video')->nullable();
			$table->string('skuprops');
			$table->string('inputpids')->nullable();
			$table->string('inputvalues')->nullable();
			$table->string('outer_id')->nullable();
			$table->string('propalias')->nullable();
			$table->unsignedInteger('auto_fill')->default(0);
			$table->string('num_id')->default(0);
			$table->Integer('local_cid')->default(-1);
			$table->unsignedInteger('navigation_type')->default(1);
			$table->string('user_name')->nullable();
			$table->unsignedInteger('syncstatus');
			$table->boolean('is_lighting_consigment');	
			$table->boolean('is_xinpin')->default(1);	
			$table->string('foodparame')->nullable();
			$table->string('features');
			$table->unsignedInteger('buyareatype')->default(0);
			$table->Integer('global_stock_type')->default(-1);
			$table->string('global_stock_country')->nullable();
			$table->string('sub_stock_type')->default(2);
			$table->string('item_size')->default('nulk:0.000000');
			$table->decimal('item_weight',5,2)->default(1);
			$table->string('sell_promise')->default(0);
			$table->string('custom_design_flag')->nullable();
			$table->text('wireless_desc')->nullable();
			$table->string('barcode')->nullable();
			$table->string('sku_barcode')->nullable();
			$table->string('newprepay')->nullable();
			$table->string('subtitle')->nullable();
			$table->string('cpv_memo')->nullable();
			$table->string('input_custom_cpv')->nullable();
			$table->string('qualification')->default('%7B%20%20%7D');
			$table->string('add_qualification')->default(0);
            $table->string('o2o_bind_service')->default(1);
            $table->string('tmall_extend')->nullable();
            $table->string('product_combine')->nullable();
            $table->string('tmall_item_prop_combine')->nullable();
            $table->string('taoschema_extend')->nullable();*/
		
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
