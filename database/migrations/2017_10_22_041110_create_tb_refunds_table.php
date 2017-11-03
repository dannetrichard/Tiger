<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
          
            $table->unsignedBigInteger('tid')->comment('订单编号'    );
            $table->unsignedBigInteger('refund_id')->comment('退款编号')->unique();
            $table->string('alipay_no')->comment('支付宝交易号'  );
            $table->date('pay_time')->comment('订单付款时间'  );
            $table->string('outer_id')->comment('商品编码'    );
            $table->date('refund_complete_time')->nullable()->comment('退款完结时间'  );
            $table->string('buyer_nick')->comment('买家会员名称'  );
            $table->decimal('payment',5,2)->comment('买家实际支付金额');
            $table->string('title')->comment('宝贝标题'    );
            $table->decimal('refund_fee',5,2)->comment('买家退款金额'  );
            $table->string('auto_refund')->nullable()->comment('手工退款系统退款');
            $table->string('has_good_return')->comment('是否需要退货'  );
            $table->date('created')->comment('退款的申请时间' );
            $table->date('timeout')->nullable()->comment('超时时间'    );
            $table->string('status')->comment('退款状态'    );
            $table->string('good_status')->comment('货物状态'    );
            $table->string('refund_ship_message')->nullable()->comment('退货物流信息'  );
            $table->string('send_ship_message')->comment('发货物流信息'  );
            $table->string('cs_status')->comment('客服介入状态'  );
            $table->string('seller_name')->comment('卖家真实姓名'  );
            $table->string('seller_address')->nullable()->comment('卖家退货地址'  );
            $table->string('seller_zip_code')->nullable()->comment('卖家邮编'    );
            $table->string('seller_phone')->nullable()->comment('卖家电话'    );
            $table->string('seller_mobile')->nullable()->comment('卖家手机'    );
            $table->unsignedBigInteger('sid')->nullable()->comment('退货物流单号'  );
            $table->string('company_name')->nullable()->comment('退货物流公司'  );
            $table->string('reason')->comment('买家退款原因'  );
            $table->string('desc')->nullable()->comment('买家退款说明'  );
            $table->date('good_return_time')->nullable()->comment('买家退货时间'  );
            $table->string('responsible_party')->nullable()->comment('责任方'     );
            $table->string('refund_phase')->comment('售中或售后'   );
            $table->string('seller_memo')->nullable()->comment('商家备注'    );
            $table->string('refund_duration')->nullable()->comment('完结时间'    );
            $table->string('part')->comment('部分退款全部退款');
            $table->string('verify_by')->nullable()->comment('审核操作人'   );
            $table->string('burden_of_proof_timeout')->nullable()->comment('举证超时'    );
            $table->string('instant_respond')->nullable()->comment('是否零秒响应'  );
            $table->string('action_by')->nullable()->comment('退款操作人'   );



            $table->enum('express_status',[0,201,202,203,204,205,206,207])->default(0);
            $table->string('express_msg')->nullable();            
            
            $table->string('express_type')->nullable();
            $table->text('express_list')->nullable();
            $table->enum('express_deliverystatus',[1,2,3,4])->default(1);
            
            //同意退款
            $table->boolean('agree_to_refund')->default(0);
            //丢件
            $table->boolean('lost')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_refunds');
    }
}
