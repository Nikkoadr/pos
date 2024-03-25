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
        Schema::create('nota', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_transaksi', ['umum', 'member']);
            $table->string('kasir');
            $table->string('member')->nullable();
            $table->timestamp('tanggal_transaksi');
            $table->integer('total_belanja');
            $table->integer('bayar')->nullable();
            $table->integer('kembalian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota');
    }
};
