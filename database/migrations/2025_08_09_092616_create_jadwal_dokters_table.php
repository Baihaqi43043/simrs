<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalDoktersTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_dokters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dokter_id');
            $table->unsignedBigInteger('poli_id');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('kuota_pasien')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('cascade');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('cascade');

            $table->index(['dokter_id', 'hari'], 'idx_dokter_hari');
            $table->index(['poli_id', 'hari'], 'idx_poli_hari');
            $table->index('is_active', 'idx_active');
            $table->unique(['dokter_id', 'poli_id', 'hari', 'jam_mulai'], 'unique_dokter_poli_hari');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_dokters');
    }
}
