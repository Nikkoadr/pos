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
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();

            $table->string('nama_barang');

            $table->foreignId('id_transaksi')
                ->constrained('transaksi')
                ->onDelete('cascade');

            $table->foreignId('id_barang')
                ->nullable()
                ->constrained('data_barang')
                ->nullOnDelete();

            $table->integer('qty');
            $table->decimal('harga_modal', 10, 2);
            $table->decimal('harga_jual', 10, 2);

            $table->enum('status', ['dibatalkan', 'selesai'])->default('selesai');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
