<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('logs'))
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('content')->comment('内容');
            $table->string('level')->comment('Monolog\Logger::TYPE 各种错误代码类型');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
