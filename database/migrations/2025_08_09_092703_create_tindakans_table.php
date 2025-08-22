<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTindakansTable extends Migration
{
    public function up()
    {
        Schema::create('tindakans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('kunjungan_id');
            $table->string('kode_tindakan', 20);
            $table->string('nama_tindakan', 255);
            $table->string('kategori_tindakan', 100)->nullable();
            $table->integer('jumlah')->default(1);
            $table->decimal('tarif_satuan', 10, 2)->default(0);
            $table->decimal('total_biaya', 12, 2)->storedAs('jumlah * tarif_satuan');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('dikerjakan_oleh')->nullable();
            $table->dateTime('tanggal_tindakan')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('status_tindakan', ['rencana', 'sedang_dikerjakan', 'selesai', 'batal'])->default('rencana');
            $table->timestamps();

            $table->foreign('kunjungan_id')->references('id')->on('kunjungans')->onDelete('cascade');
            $table->foreign('dikerjakan_oleh')->references('id')->on('dokters')->onDelete('set null');

            $table->index('kunjungan_id', 'idx_kunjungan_id');
            $table->index('kode_tindakan', 'idx_kode_tindakan');
            $table->index('tanggal_tindakan', 'idx_tanggal_tindakan');
            $table->index('status_tindakan', 'idx_status_tindakan');
            $table->index('dikerjakan_oleh', 'idx_dikerjakan_oleh');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tindakans');
    }
}
