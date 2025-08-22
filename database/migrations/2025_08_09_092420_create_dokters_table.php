<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoktersTable extends Migration
{
    public function up()
    {
        Schema::create('dokters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_dokter', 10)->unique();
            $table->string('nama_dokter', 255);
            $table->string('spesialisasi', 255);
            $table->string('no_telepon', 15)->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kode_dokter', 'idx_kode_dokter');
            $table->index('spesialisasi', 'idx_spesialisasi');
            $table->index('is_active', 'idx_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokters');
    }
}
