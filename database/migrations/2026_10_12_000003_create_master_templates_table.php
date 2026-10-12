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
        $categoriesTable = defined('App\Constants\Tables::MASTER_TEMPLATE_CATEGORIES')
            ? Tables::MASTER_TEMPLATE_CATEGORIES
            : 'master_template_categories';

        $tableName = defined('App\Constants\Tables::MASTER_TEMPLATES')
            ? Tables::MASTER_TEMPLATES
            : 'master_templates';

        Schema::create($tableName, function (Blueprint $table) use ($categoriesTable) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 200)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->json('json');
            $table->string('preview_image', 255)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on($categoriesTable)
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = defined('App\Constants\Tables::MASTER_TEMPLATES')
            ? Tables::MASTER_TEMPLATES
            : 'master_templates';

        Schema::dropIfExists($tableName);
    }
};
