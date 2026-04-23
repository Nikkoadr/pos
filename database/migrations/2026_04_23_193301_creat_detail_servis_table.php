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
        Schema::create('detail_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nota')->constrained('nota')->onDelete('cascade');
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
            $table->string('pin')->nullable();
            $table->string('sandi')->nullable();
            $table->string('pola')->nullable();
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
