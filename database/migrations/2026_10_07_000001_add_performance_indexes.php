<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->index(['status', 'jenis_transaksi', 'tanggal_transaksi'], 'trx_laporan_idx');
            $table->index('tanggal_transaksi', 'trx_tanggal_idx');
        });
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->index('id_transaksi', 'dtrx_transaksi_idx');
        });
        Schema::table('detail_transaksi_servis', function (Blueprint $table) {
            $table->index('id_transaksi', 'dsrv_transaksi_idx');
        });
        Schema::table('keranjang', function (Blueprint $table) {
            $table->index('id_transaksi', 'krj_transaksi_idx');
        });
        Schema::table('data_barang', function (Blueprint $table) {
            $table->index('kategori', 'brg_kategori_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropIndex('trx_laporan_idx');
            $table->dropIndex('trx_tanggal_idx');
        });
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->dropIndex('dtrx_transaksi_idx');
        });
        Schema::table('detail_transaksi_servis', function (Blueprint $table) {
            $table->dropIndex('dsrv_transaksi_idx');
        });
        Schema::table('keranjang', function (Blueprint $table) {
            $table->dropIndex('krj_transaksi_idx');
        });
        Schema::table('data_barang', function (Blueprint $table) {
            $table->dropIndex('brg_kategori_idx');
        });
    }
};
