<?php

use App\Constants\Tables;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = defined('App\Constants\Tables::MASTER_FIELD_TYPES')
            ? Tables::MASTER_FIELD_TYPES
            : 'master_field_types';

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type_key', 50)->unique();
            $table->string('category', 50)->default('basic'); // basic, choice, advanced, layout, media
            $table->string('icon', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->json('default_config')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = defined('App\Constants\Tables::MASTER_FIELD_TYPES')
            ? Tables::MASTER_FIELD_TYPES
            : 'master_field_types';

        Schema::dropIfExists($tableName);
    }
};
