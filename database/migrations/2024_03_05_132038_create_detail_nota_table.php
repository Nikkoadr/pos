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
        Schema::create('detail_nota', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->foreignId('id_nota')->constrained('nota')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('data_barang')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_nota');
    }
};
