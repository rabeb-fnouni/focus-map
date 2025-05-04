
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToStepsTable extends Migration
{
    public function up()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->string('category')->nullable()->after('title');
        });
    }

    public function down()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}