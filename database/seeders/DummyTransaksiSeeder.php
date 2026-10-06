<?php

namespace Database\Seeders;

use App\Models\Data_barang;
use App\Models\Data_member;
use App\Models\DetailTransaksi;
use App\Models\DetailTransaksiServis;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20261006);

        $kasir = User::first()->nama ?? 'Admin';
        $members = Data_member::all();
        $barangs = Data_barang::all();

        if ($barangs->isEmpty()) {
            $this->command->error('Tabel data_barang kosong, isi barang dulu.');
            return;
        }
        if ($members->isEmpty()) {
            $this->command->error('Tabel data_member kosong, isi member dulu.');
            return;
        }

        $umumPool = $barangs->where('kategori', 'umum');
        if ($umumPool->isEmpty()) {
            $umumPool = $barangs;
        }
        $memberPool = $barangs->where('kategori', 'member');
        if ($memberPool->isEmpty()) {
            $memberPool = $barangs;
        }
        $sparepartPool = $barangs->where('kategori', 'sparepart');
        if ($sparepartPool->isEmpty()) {
            $sparepartPool = $barangs;
        }

        $namaPelanggan = ['Budi Santoso', 'Siti Aminah', 'Andi Pratama', 'Dewi Lestari', 'Rudi Hartono', 'Nurhaliza', 'Agus Wijaya', 'Rina Marlina', 'Dedi Kurniawan', 'Fitri Handayani', 'Hendra Gunawan', 'Yuni Astuti'];
        $merkHp = ['Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'Infinix'];
        $tipeHp = ['A12', 'Redmi 12', 'A57', 'Y22', 'C55', 'Hot 30', 'A04', 'Note 12'];
        $kerusakan = ['LCD pecah', 'Baterai drop', 'Tidak bisa dicas', 'Mati total', 'Sinyal hilang', 'Kamera buram'];
        $kondisi = ['Mulus', 'Lecet pemakaian', 'Dent kecil'];
        $bayarLebih = [0, 0, 2000, 5000, 10000, 20000, 50000];

        // Tanggal dummy: sebar di 1-6 Okt 2026 (masuk filter default bulan ini)
        // + beberapa di September 2026 untuk demo filter rentang.
        $dates = [];
        for ($i = 0; $i < 24; $i++) {
            $dates[] = Carbon::create(2026, 10, mt_rand(1, 6), mt_rand(8, 20), mt_rand(0, 59), 0);
        }
        for ($i = 0; $i < 6; $i++) {
            $dates[] = Carbon::create(2026, 9, mt_rand(10, 28), mt_rand(8, 20), mt_rand(0, 59), 0);
        }
        shuffle($dates);

        $di = 0;
        $buatDetail = function ($pool, $pakaiHargaMember) {
            $b = $pool->values()[mt_rand(0, $pool->count() - 1)];
            $qty = mt_rand(1, 3);
            $harga = $pakaiHargaMember ? (float) $b->harga_member : (float) $b->harga_umum;
            return [
                'id_barang' => $b->id,
                'nama_barang' => $b->nama,
                'qty' => $qty,
                'harga_modal' => (float) $b->harga_modal,
                'harga_jual' => $harga,
            ];
        };
        $buatTransaksi = function ($jenis, $idMember, $tgl, $items) use ($kasir) {
            $total = 0;
            foreach ($items as $it) {
                $total += $it['harga_jual'] * $it['qty'];
            }
            $total = (int) round($total);
            $lebih = [0, 0, 2000, 5000, 10000, 20000, 50000][mt_rand(0, 6)];
            $trx = Transaksi::create([
                'jenis_transaksi' => $jenis,
                'id_member' => $idMember,
                'tanggal_transaksi' => $tgl,
                'status' => 'selesai',
                'kasir' => $kasir,
                'total_belanja' => $total,
                'bayar' => $total + $lebih,
                'kembalian' => $lebih,
                'created_at' => $tgl,
                'updated_at' => $tgl,
            ]);
            foreach ($items as $it) {
                DetailTransaksi::create([
                    'id_transaksi' => $trx->id,
                    'id_barang' => $it['id_barang'],
                    'nama_barang' => $it['nama_barang'],
                    'qty' => $it['qty'],
                    'harga_modal' => $it['harga_modal'],
                    'harga_jual' => $it['harga_jual'],
                    'status' => 'selesai',
                    'created_at' => $tgl,
                    'updated_at' => $tgl,
                ]);
            }
            return $trx;
        };

        DB::transaction(function () use (
            $dates, &$di, $buatDetail, $buatTransaksi,
            $umumPool, $memberPool, $sparepartPool, $members,
            $namaPelanggan, $merkHp, $tipeHp, $kerusakan, $kondisi
        ) {
            // 10x penjualan umum
            for ($i = 0; $i < 10; $i++) {
                $tgl = $dates[$di++];
                $n = mt_rand(1, 3);
                $items = [];
                for ($k = 0; $k < $n; $k++) {
                    $items[] = $buatDetail($umumPool, false);
                }
                $buatTransaksi('umum', null, $tgl, $items);
            }

            // 10x penjualan member
            for ($i = 0; $i < 10; $i++) {
                $tgl = $dates[$di++];
                $member = $members->values()[mt_rand(0, $members->count() - 1)];
                $n = mt_rand(1, 3);
                $items = [];
                for ($k = 0; $k < $n; $k++) {
                    $items[] = $buatDetail($memberPool, true);
                }
                $buatTransaksi('member', $member->id, $tgl, $items);
            }

            // 10x servis (status diambil = selesai, ikut arsip & laporan)
            for ($i = 0; $i < 10; $i++) {
                $tgl = $dates[$di++];
                $n = mt_rand(1, 2);
                $items = [];
                for ($k = 0; $k < $n; $k++) {
                    $items[] = $buatDetail($sparepartPool, false);
                }
                // 1 baris jasa manual ala item manual di kasir
                $jasa = mt_rand(25000, 150000);
                $jasa = (int) (round($jasa / 5000) * 5000);
                $items[] = [
                    'id_barang' => null,
                    'nama_barang' => 'Jasa Servis HP',
                    'qty' => 1,
                    'harga_modal' => 0,
                    'harga_jual' => $jasa,
                ];
                $trx = $buatTransaksi('servis', null, $tgl, $items);
                DetailTransaksiServis::create([
                    'id_transaksi' => $trx->id,
                    'kode_servis' => 'SRV-DMY-' . $tgl->format('Ymd') . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '-' . substr(md5(mt_rand()), 0, 4),
                    'tanggal_masuk' => $tgl->toDateString(),
                    'tanggal_dikerjakan' => $tgl->copy()->addDay()->toDateString(),
                    'tanggal_diambil' => $tgl->copy()->addDays(2)->toDateString(),
                    'nama' => $namaPelanggan[mt_rand(0, count($namaPelanggan) - 1)],
                    'nohp' => '08' . mt_rand(1111111111, 9999999999),
                    'alamat' => 'Jl. Dummy No. ' . mt_rand(1, 100) . ' Losarang',
                    'merk' => $merkHp[mt_rand(0, count($merkHp) - 1)],
                    'tipe' => $tipeHp[mt_rand(0, count($tipeHp) - 1)],
                    'kerusakan' => $kerusakan[mt_rand(0, count($kerusakan) - 1)],
                    'kondisi' => $kondisi[mt_rand(0, count($kondisi) - 1)],
                    'security' => '1234',
                    'waktu_pengambilan' => $tgl->copy()->addDays(2)->setTime(16, 0),
                    'status_servis' => 'diambil',
                    'created_at' => $tgl,
                    'updated_at' => $tgl,
                ]);
            }
        });

        $this->command->info('Dummy selesai: +10 umum, +10 member, +10 servis (status selesai).');
    }
}
