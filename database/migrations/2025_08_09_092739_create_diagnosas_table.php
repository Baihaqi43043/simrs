<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagnosasTable extends Migration
{
    public function up()
    {
        Schema::create('diagnosas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kunjungan_id');
            $table->enum('jenis_diagnosa', ['utama', 'sekunder'])->default('utama');
            $table->string('kode_icd', 10);
            $table->string('nama_diagnosa', 500);
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('didiagnosa_oleh')->nullable();
            $table->dateTime('tanggal_diagnosa')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();

            $table->foreign('kunjungan_id')->references('id')->on('kunjungans')->onDelete('cascade');
            $table->foreign('didiagnosa_oleh')->references('id')->on('dokters')->onDelete('set null');

            $table->index('kunjungan_id', 'idx_kunjungan_id');
            $table->index('kode_icd', 'idx_kode_icd');
            $table->index('jenis_diagnosa', 'idx_jenis_diagnosa');
            $table->index('didiagnosa_oleh', 'idx_didiagnosa_oleh');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diagnosas');
    }
}
