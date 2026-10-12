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
        $tableName = defined('App\Constants\Tables::MASTER_TEMPLATE_CATEGORIES')
            ? Tables::MASTER_TEMPLATE_CATEGORIES
            : 'master_template_categories';

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
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
        $tableName = defined('App\Constants\Tables::MASTER_TEMPLATE_CATEGORIES')
            ? Tables::MASTER_TEMPLATE_CATEGORIES
            : 'master_template_categories';

        Schema::dropIfExists($tableName);
    }
};
