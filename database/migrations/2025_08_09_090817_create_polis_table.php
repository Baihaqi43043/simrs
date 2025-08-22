<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolisTable extends Migration
{
    public function up()
    {
        Schema::create('polis', function (Blueprint $table) {
            $table->bigIncrements('id'); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('kode_poli', 10)->unique();
            $table->string('nama_poli', 255);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index tambahan
            $table->index('kode_poli', 'idx_kode_poli');
            $table->index('is_active', 'idx_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('polis');
    }
}
