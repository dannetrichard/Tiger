<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = "insert into `categories`(`id`,`created_at`,`updated_at`,`category_id`,`category_name`,`parent_id`) values
            ('1','2017-10-28 07:12:56','2017-10-28 07:12:56','50013194','毛呢外套','16'),
            ('2','2017-10-28 07:12:58','2017-10-28 07:12:58','50008900','棉衣/棉服','16')";
            
        DB::insert($sql);  
            
    }
}
