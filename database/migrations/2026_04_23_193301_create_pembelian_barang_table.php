<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembelian_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_supplier')
                ->constrained('supplier')
                ->onDelete('restrict');
            $table->string('kode_pembelian')->unique();
            $table->date('tanggal_pembelian');
            $table->foreignId('id_barang')
                ->constrained('data_barang')
                ->onDelete('restrict');
            $table->integer('qty');
            $table->decimal('harga_modal', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembelian_barang');
    }
};
