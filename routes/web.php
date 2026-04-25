<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Data_barangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ServisController;
use App\Http\Controllers\ArsipController;

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
Route::get('/setting', [HomeController::class, 'setting'])->name('setting');
Route::put('/update_setting_{id}', [HomeController::class, 'update_setting'])->name('update_setting');

Route::get('/data_barang', [Data_barangController::class, 'data_barang'])->name('data_barang');
Route::put('/tambah_data_barang', [Data_barangController::class, 'tambah_data_barang'])->name('tambah_data_barang');
Route::get('/view_edit_data_barang_{id}', [Data_barangController::class, 'view_edit_data_barang'])->name('view_edit_data_barang');
Route::put('/update_data_barang_{id}', [Data_barangController::class, 'update_data_barang'])->name('update_data_barang');
Route::post('/import_data_barang', [Data_barangController::class, 'import_data_barang'])->name('import_data_barang');
Route::get('/export_data_barang', [Data_barangController::class, 'export_data_barang'])->name('export_data_barang');
Route::get('/hapus_data_barang_{id}', [Data_barangController::class, 'hapus_data_barang'])->name('hapus_data_barang');

Route::get('/data_member', [MemberController::class, 'data_member'])->name('data_member');
Route::get('/search/member', [MemberController::class, 'search'])->name('search');
Route::post('/tambah_data_member', [MemberController::class, 'tambah_data_member'])->name('tambah_data_member');
Route::get('/view_edit_data_member_{id}', [MemberController::class, 'view_edit_data_member'])->name('view_edit_data_member');
Route::put('/update_data_member_{id}', [MemberController::class, 'update_data_member'])->name('update_data_member');
Route::get('/hapus_data_member_{id}', [MemberController::class, 'hapus_data_member'])->name('hapus_data_member');
Route::post('/edit_data_member_{id}', [MemberController::class, 'edit_data_member'])->name('edit_data_member');

Route::get('/transaksi', [TransaksiController::class, 'transaksi'])->name('transaksi');
Route::post('/buat_transaksi', [TransaksiController::class, 'buat_transaksi'])->name('buat_transaksi');
Route::get('/proses_transaksi_{id}', [TransaksiController::class, 'proses_transaksi'])->name('proses_transaksi');
Route::get('/data-barang', [TransaksiController::class, 'dataBarang'])->name('data-barang');
Route::post('/tambah_keranjang', [TransaksiController::class, 'tambah_keranjang'])->name('tambah_keranjang');
Route::post('/edit_qty', [TransaksiController::class, 'edit_qty'])->name('edit_qty');
Route::delete('/hapus_keranjang_{id}', [TransaksiController::class, 'hapus_keranjang'])->name('hapus_keranjang');
Route::post('/checkout', [TransaksiController::class, 'checkout'])->name('checkout');

Route::delete('/hapus_transaksi/{id}', [TransaksiController::class, 'hapus_transaksi'])->name('hapus_transaksi');
Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan');
Route::POST('/laporan_filter', [LaporanController::class, 'filter'])->name('laporan.filter');

Route::post('/scan-barang', [TransaksiController::class, 'scanBarang']);

Route::get('/servis', [ServisController::class, 'index'])->name('servis');
Route::post('/update_status_servis', [ServisController::class, 'updateStatusServis'])->name('updateStatusServis');
Route::get('/transaksi_servis_{id}', [ServisController::class, 'transaksiServis']);
Route::post('/servis/store/{id}', [ServisController::class, 'store_servis']);
Route::get('/cetak_transaksi_servis/{id}', [ServisController::class, 'proses_servis']);
Route::post('/tambah_manual', [TransaksiController::class, 'tambah_manual']);

Route::get('/pembayaran/servis/{id}', [ServisController::class, 'pembayaran_servis']);
Route::post('/selesaikan_servis', [ServisController::class, 'selesaikan_servis'])->name('selesaikan_servis');

Route::prefix('arsip')->group(function () {
    Route::get('/', [ArsipController::class, 'index'])->name('arsip.index');
    Route::get('/data', [ArsipController::class, 'data_arsip'])->name('arsip.data');
    Route::get('/detail/{id}', [ArsipController::class, 'show'])->name('arsip.show');
});
