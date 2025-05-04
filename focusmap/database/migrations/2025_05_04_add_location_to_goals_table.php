
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationToGoalsTable extends Migration
{
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->string('location_name')->nullable()->after('description');
            $table->float('latitude')->nullable()->after('location_name');
            $table->float('longitude')->nullable()->after('latitude');
            $table->boolean('completed')->default(false)->after('progress');
            $table->timestamp('completed_at')->nullable()->after('completed');
        });
    }

    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn(['location_name', 'latitude', 'longitude', 'completed', 'completed_at']);
        });
    }
}