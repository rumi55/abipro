<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('numbering_id')->nullable();
            $table->unsignedBigInteger('sales_quote_id')->nullable();
            $table->string('trans_no', 128);
            $table->date('trans_date')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->date('due_date')->nullable();
            $table->string('description')->nullable();
            $table->double('subtotal')->nullable();
            $table->double('tax')->nullable();
            $table->double('total')->nullable();
            $table->double('discount')->nullable();
            $table->double('deposit')->nullable();
            $table->double('balance_due')->nullable();
            $table->boolean('include_tax')->default(true);
            $table->string('transaction_type_id', 8)->nullable();
            $table->string('status', 32)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('salesman_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
                        
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('customer_id')->references('id')->on('contacts');
            $table->foreign('salesman_id')->references('id')->on('contacts');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade');
            $table->foreign('numbering_id')->references('id')->on('numberings')->onDelete('set null');
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('set null');
            $table->foreign('sales_quote_id')->references('id')->on('sales_quotes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_orders');
    }
}
