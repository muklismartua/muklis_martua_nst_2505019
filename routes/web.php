<?php

use App\Http\Controllers\TokoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TokoController::class, 'index'])->name('toko.index');
Route::post('/barang', [TokoController::class, 'storeBarang'])->name('barang.store');
Route::post('/transaksi', [TokoController::class, 'storeTransaksi'])->name('transaksi.store');
Route::post('/utang', [TokoController::class, 'storeUtang'])->name('utang.store');
Route::patch('/utang/{utang}/lunas', [TokoController::class, 'lunasUtang'])->name('utang.lunas');
