<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->text('keterangan_barang');
            $table->decimal('total_utang', 15, 2);
            $table->string('status')->default('belum_lunas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utangs');
    }
};
