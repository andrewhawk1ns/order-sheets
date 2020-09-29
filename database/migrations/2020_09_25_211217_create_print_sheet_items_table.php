<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintSheetItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('status', ['pass', 'reject', 'complete'])->default('pass');
            $table->string('image_url', 255);
            $table->string('size', 255);
            $table->integer('x_pos');
            $table->integer('y_pos');
            $table->integer('width');
            $table->integer('height');
            $table->string('identifier', 255);

            $table->foreignId('print_sheet_id');
            $table->foreignId('order_item_id');

            $table->index('id');
            $table->index('status');

            $table->foreign('print_sheet_id')->references('id')->on('print_sheets');
            $table->foreign('order_item_id')->references('id')->on('order_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_sheet_items');
    }
}
