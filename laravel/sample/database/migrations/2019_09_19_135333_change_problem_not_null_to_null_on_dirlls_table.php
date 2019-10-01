<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProblemNotNullToNullOnDirllsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drills', function (Blueprint $table) {
            //
            for ($i = 1; $i <= 9; ++$i) {
                $table->string('problem'.$i)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drills', function (Blueprint $table) {
            //
            for ($i = 1; $i <= 9; ++$i) {
                $table->string('problem'.$i)->nullable(false)->change();
            }
        });
    }
}
