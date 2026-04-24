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
        Schema::create('detail_transaksi_servis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_transaksi')
                ->constrained('transaksi')
                ->onDelete('restrict');

            $table->string('kode_servis')->unique();

            $table->date('tanggal_masuk');
            $table->date('tanggal_dikerjakan')->nullable();
            $table->date('tanggal_diambil')->nullable();

            $table->string('nama');
            $table->string('nohp');
            $table->string('alamat');

            $table->string('merk');
            $table->string('tipe');
            $table->text('kerusakan');

            $table->string('kondisi')->nullable();
            $table->string('security')->nullable();

            $table->integer('harga_modal')->default(0);
            $table->integer('harga_jual')->default(0);

            $table->enum('status_servis', ['masuk', 'proses', 'selesai', 'dibatalkan', 'diambil'])
                ->default('masuk');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
