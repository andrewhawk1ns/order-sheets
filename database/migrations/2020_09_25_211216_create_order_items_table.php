<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('quantity')->default(1);
            $table->bigInteger('refund')->default(0);
            $table->integer('resend_amount')->default(0);

            $table->foreignId('order_id');
            $table->foreignId('product_id');

            foreach (['product_id', 'refund', 'created_at', 'updated_at'] as $index) {
                $table->index($index);
            }

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
