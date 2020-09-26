<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->timestamps();
            $table->string('title', 100)->default('');
            $table->string('vendor', 50)->nullable();
            $table->string('type', 25)->nullable();
            $table->string('size', 20)->nullable();
            $table->float('price')->default(0);
            $table->string('handle', 75)->nullable();
            $table->integer('inventory_quantity')->default(0);
            $table->string('sku', 30)->nullable();
            $table->string('design_url', 255)->nullable();
            $table->enum('published_state', ['inactive', 'active'])->default('active');

            foreach (['title', 'vendor', 'type', 'sku', 'size', 'published_state', 'created_at', 'updated_at'] as $index) {
                $table->index($index);
            }
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
