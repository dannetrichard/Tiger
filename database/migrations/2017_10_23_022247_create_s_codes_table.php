<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('number');
            $table->date('effective_time');
            $table->string('c');
            $table->string('destination');
            $table->unsignedInteger('e');
            $table->decimal('weight',5,2);
            $table->string('shop')->nullable();
            $table->decimal('price',5,2);
            
            $table->enum('express_status',[0,201,202,203,204,205,206,207])->default(0);
            $table->string('express_msg')->nullable();            
            
            $table->string('express_type')->nullable();
            $table->text('express_list')->nullable();
            $table->enum('express_deliverystatus',[1,2,3,4])->default(1);
            
            $table->string('tb_trade_id')->nullable();
            $table->boolean('repeat')->default(0);   
            $table->boolean('record')->default(0);                     
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_codes');
    }
}
