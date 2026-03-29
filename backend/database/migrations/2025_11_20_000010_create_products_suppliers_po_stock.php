<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){

        Schema::create('suppliers', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('reference_code')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('sku')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('product_type')->default('consumable');
            $table->string('unit')->default('unit');
            $table->bigInteger('cost_cents')->default(0);
            $table->bigInteger('price_cents')->default(0);
            $table->integer('reorder_threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('po_number')->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->date('expected_date')->nullable();
            $table->bigInteger('total_cents')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_lines', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('quantity',10,2)->default(0);
            $table->bigInteger('unit_cost_cents')->default(0);
            $table->bigInteger('line_total_cents')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_locations', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('location_type')->default('warehouse');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('from_location_id')->nullable()->index();
            $table->unsignedBigInteger('to_location_id')->nullable()->index();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->bigInteger('unit_cost_cents')->default(0);
            $table->string('movement_type')->index();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('stock_levels', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('location_id')->index();
            $table->decimal('quantity',10,2)->default(0);
            $table->bigInteger('last_cost_cents')->default(0);
            $table->timestamps();

            $table->unique(['product_id','location_id','property_id'],'ux_stock_product_location_property');
        });

    }

    public function down(){
        Schema::dropIfExists('stock_levels');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_locations');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
    }
};
?>