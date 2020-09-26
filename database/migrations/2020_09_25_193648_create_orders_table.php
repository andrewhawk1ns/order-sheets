<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->float('total_price')->default(0);
            $table->string('fulfillment_status', 25)->nullable();
            $table->timestamp('fulfilled_date')->nullable();
            $table->enum('order_status', ['pending', 'active', 'done', 'cancelled', 'resend'])->nullable();
            $table->integer('customer_order_count')->nullable();

            foreach (['order_number', 'customer_id', 'fulfillment_status', 'created_at', 'updated_at'] as $index) {
                $table->index($index);
            }

            $table->foreignId('order_number');
            $table->foreignId('customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
