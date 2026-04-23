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
        Schema::create('data_barang', function (Blueprint $table) {
            $table->id();
            $table->integer('id_toko');
            $table->integer('id_supplier');
            $table->string('barcode')->unique();
            $table->string('nama');
            $table->integer('qty');
            $table->integer('harga_modal');
            $table->integer('harga_umum');
            $table->integer('harga_member');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_barang');
    }
};
