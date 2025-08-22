<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKunjungansTable extends Migration
{
    public function up()
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_kunjungan', 30)->unique();
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('dokter_id');
            $table->unsignedBigInteger('poli_id');
            $table->unsignedBigInteger('jadwal_dokter_id')->nullable();
            $table->date('tanggal_kunjungan');
            $table->time('jam_kunjungan')->nullable();
            $table->integer('no_antrian');
            $table->enum('jenis_kunjungan', ['baru', 'lama']);
            $table->enum('cara_bayar', ['umum', 'bpjs', 'asuransi'])->default('umum');
            $table->text('keluhan_utama')->nullable();
            $table->enum('status', ['menunggu', 'sedang_dilayani', 'selesai', 'batal'])->default('menunggu');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
            $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('restrict');
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('restrict');
            $table->foreign('jadwal_dokter_id')->references('id')->on('jadwal_dokters')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('no_kunjungan', 'idx_no_kunjungan');
            $table->index('tanggal_kunjungan', 'idx_tanggal_kunjungan');
            $table->index(['pasien_id', 'tanggal_kunjungan'], 'idx_pasien_tanggal');
            $table->index(['dokter_id', 'tanggal_kunjungan'], 'idx_dokter_tanggal');
            $table->index(['poli_id', 'tanggal_kunjungan'], 'idx_poli_tanggal');
            $table->index('status', 'idx_status');
            $table->index(['tanggal_kunjungan', 'poli_id', 'no_antrian'], 'idx_antrian');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kunjungans');
    }
}
