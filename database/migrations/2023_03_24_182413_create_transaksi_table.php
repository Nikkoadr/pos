<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_transaksi', ['umum', 'member', 'servis']);
            $table->enum('status', ['aktif', 'proses', 'dibatalkan','selesai'])->default('aktif');
            $table->string('id_member')->nullable();
            $table->timestamp('tanggal_transaksi');
            $table->string('kasir');
            $table->integer('total_belanja');
            $table->integer('bayar')->nullable();
            $table->integer('kembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
