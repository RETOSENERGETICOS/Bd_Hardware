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

        Schema::create('dess', static function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('brands', static function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('sos', static function(Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->timestamps();
        });

        Schema::create('usr', static function(Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->timestamps();
        });

        Schema::create('devices', static function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
         });

        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('des_id')->nullable()->constrained();;
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('so_id')->constrained();
            $table->foreignId('usr_id')->constrained();
            $table->foreignId('device_id')->constrained();
            $table->string('serial_number')->unique()->nullable();
            $table->string('main_localization');
            $table->string('shelf_localization')->nullable();
            $table->string('shelf')->nullable();
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('dess');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('sos');
        Schema::dropIfExists('usrs');
        Schema::dropIfExists('devices');
    }
}
