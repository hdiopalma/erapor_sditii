<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapor_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->foreignId('siswa_mapel_id');
            $table->foreignId('siswa_ilman_waa_ruuhan_id');
            $table->foreignId('siswa_ibadah_harian_id');
            $table->foreignId('siswa_hadist_id');
            $table->foreignId('siswa_doa_id');
            $table->foreignId('profil_sekolah_id');
            $table->foreignId('periode_id');
            $table->foreignId('format_id');
            $table->string('tempat');
            $table->timestamp('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rapor_siswas');
    }
};
