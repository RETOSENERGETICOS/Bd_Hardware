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

        Schema::create('groups', static function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('families', static function(Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->timestamps();
        });

        Schema::create('brands', static function(Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->timestamps();
        });

        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('description');
            $table->foreignId('group_id')->nullable()->constrained();
            $table->foreignId('family_id')->constrained();
            $table->foreignId('brand_id')->constrained();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->date('calibration_expiration')->nullable();
            $table->boolean('dispatchable')->default(false);
            $table->boolean('has_validation')->comment('sujeto a validacion')->nullable();
            $table->string('main_localization');
            $table->string('shelf_localization')->nullable();
            $table->string('shelf')->nullable();
            $table->string('measurement');
            $table->foreignId('user_id')->constrained();
            $table->decimal('min_stock')->nullable();
            $table->decimal('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tools');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('families');
        Schema::dropIfExists('brands');
    }
}
