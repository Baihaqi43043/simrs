<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasiensTable extends Migration
{
    public function up()
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_rm', 20)->unique();
            $table->string('nik', 16)->unique();
            $table->string('nama', 255);
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir', 255)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat')->nullable();
            $table->string('no_telepon', 15)->nullable();
            $table->string('no_telepon_darurat', 15)->nullable();
            $table->string('nama_kontak_darurat', 255)->nullable();
            $table->timestamps();

            $table->index('no_rm', 'idx_no_rm');
            $table->index('nik', 'idx_nik');
            $table->index('nama', 'idx_nama');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pasiens');
    }
}
