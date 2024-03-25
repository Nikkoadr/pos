<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Data_barangController;
use App\Http\Controllers\Keranjang_umumController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransaksiController;
use App\Models\Transaksi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/home',     [HomeController::class, 'index'])->name('home');
Route::get('/data_karyawan', [HomeController::class, 'data_karyawan'])->name('data_karyawan');
Route::get('/data_supplier', [HomeController::class, 'data_supplier'])->name('data_supplier');
Route::get('/pengaturan', [HomeController::class, 'pengaturan'])->name('pengaturan');

Route::get('/data_barang', [Data_barangController::class, 'data_barang'])->name('data_barang');
Route::put('/tambah_data_barang', [Data_barangController::class, 'tambah_data_barang'])->name('tambah_data_barang');
Route::get('/view_edit_data_barang_{id}', [Data_barangController::class, 'view_edit_data_barang'])->name('view_edit_data_barang');
Route::put('/update_data_barang_{id}', [Data_barangController::class, 'update_data_barang'])->name('update_data_barang');
Route::post('/import_data_barang', [Data_barangController::class, 'import_data_barang'])->name('import_data_barang');
Route::get('/export_data_barang', [Data_barangController::class, 'export_data_barang'])->name('export_data_barang');
Route::get('/hapus_data_barang_{id}', [Data_barangController::class, 'hapus_data_barang'])->name('hapus_data_barang');

Route::get('/data_member', [MemberController::class, 'data_member'])->name('data_member');
Route::get('/search/member', [MemberController::class, 'search'])->name('search');

Route::get('/transaksi', [TransaksiController::class, 'transaksi'])->name('transaksi');
Route::post('/buat_transaksi', [TransaksiController::class, 'buat_transaksi'])->name('buat_transaksi');
Route::get('/proses_transaksi_{id}', [TransaksiController::class, 'proses_transaksi'])->name('proses_transaksi');
Route::post('/tambah_keranjang', [TransaksiController::class, 'tambah_keranjang'])->name('tambah_keranjang');
Route::post('/edit_qty', [TransaksiController::class, 'edit_qty'])->name('edit_qty');
Route::delete('hapus_keranjang_{id}', [TransaksiController::class, 'hapus_keranjang']);
Route::post('/checkout', [TransaksiController::class, 'checkout'])->name('checkout');

Route::get('/riwayat_transaksi', [NotaController::class, 'riwayat_transaksi'])->name('riwayat_transaksi');
Route::get('/nota_{id}', [NotaController::class, 'detail'])->name('detail');
Route::get('/hapus_nota_{id}', [NotaController::class, 'hapus_nota'])->name('hapus_nota');
Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan');
Route::POST('/laporan_filter', [LaporanController::class, 'filter'])->name('laporan.filter');


Route::get('/data-barang', [TransaksiController::class, 'dataBarang'])->name('data-barang');
