<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('report_date');
            $table->string('project_code');
            $table->string('location');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_overnight')->default(false);
            $table->timestamps();
        });

        Schema::create('report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->enum('status', ['Selesai', 'Dalam Proses', 'Tertunda', 'Bermasalah']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_details');
        Schema::dropIfExists('reports');
    }
}; 