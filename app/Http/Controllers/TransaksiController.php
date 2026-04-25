<?php

namespace App\Http\Controllers;

use App\Models\Data_member;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Data_barang;
use App\Models\Keranjang;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\DetailTransaksiServis;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Exception;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function transaksi(Request $request)
    {
        // 🔥 hanya transaksi aktif
        $transaksi = Transaksi::where('status', 'aktif')->latest()->get();

        $member = Data_member::all();

        foreach ($transaksi as $data) {
            $nama_member = Data_member::find($data->id_member);
            $data->nama_member = $nama_member ? $nama_member->nama_member : "Tidak ada Member";
        }

        return view('transaksi.buat_transaksi', compact('transaksi', 'member'));
    }

    public function buat_transaksi(Request $request)
    {
        $transaksi = Transaksi::create([
            'jenis_transaksi' => $request->jenis_transaksi,
            'id_member' => $request->id_member,
            'tanggal_transaksi' => now(),
            'status' => 'aktif', // 🔥 penting
            'kasir' => auth()->user()->nama,
            'total_belanja' => 0
        ]);

        // 🔥 BEDAKAN FLOW
        if ($request->jenis_transaksi == 'servis') {
            return redirect('transaksi_servis_' . $transaksi->id);
        }

        return redirect('/proses_transaksi_' . $transaksi->id)
            ->with('sukses', 'Transaksi berhasil dibuat');
    }

    private function calculateTotal($keranjang)
    {
        $total = 0;
        foreach ($keranjang as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public function proses_transaksi(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $keranjang = Keranjang::where('id_transaksi', $id)->get();

        // Member list (kalau dipakai di dropdown)
        $member = Data_member::all();

        // Ambil nama member dengan aman
        $nama_member = Data_member::find($transaksi->id_member);
        $transaksi->nama_member = $nama_member ? $nama_member->nama_member : null;

        // Total transaksi biasa
        $total = $this->calculateTotal($keranjang);

        // 🔥 SERVIS (aman kalau tidak ada data)
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        // 🔥 TAMBAHAN: total servis dari keranjang (biar konsisten seperti transaksi biasa)
        $total_servis = $keranjang->sum('subtotal');

        return view('transaksi.proses_transaksi', compact(
            'transaksi',
            'total',
            'total_servis',
            'keranjang',
            'member',
            'servis'
        ));
    }

    public function scanBarang(Request $request)
    {
        $barang = Data_barang::where('barcode', $request->input('barcode'))->first();

        if (!$barang) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // cek sudah ada di keranjang
        $cek = DB::table('keranjang')
            ->where('id_transaksi', $request->id_transaksi)
            ->where('id_barang', $barang->id)
            ->first();

        if ($cek) {
            DB::table('keranjang')
                ->where('id', $cek->id)
                ->update([
                    'qty' => $cek->qty + 1,
                    'subtotal' => ($cek->qty + 1) * $barang->harga_umum
                ]);
        } else {
            DB::table('keranjang')->insert([
                'id_transaksi' => $request->id_transaksi,
                'id_barang' => $barang->id,
                'nama' => $barang->nama,
                'harga' => $barang->harga_umum,
                'qty' => 1,
                'subtotal' => $barang->harga_umum
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function tambah_keranjang(Request $request)
    {
        $produk = Data_barang::find($request->input('id'));
        $qty = $request->input('jumlah');
        if ($qty > $produk->qty) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }
        $produk->qty -= $qty;
        $produk->save();
        $keranjang = Keranjang::where('id_barang', $produk->id)
            ->where('id_transaksi', $request->id_transaksi)
            ->first();
        $harga = $request->id_member ? $produk->harga_member : $produk->harga_umum;
        $subtotal = $harga * $qty;
        if ($keranjang) {
            $keranjang->qty += $qty;
            $keranjang->subtotal = $harga * $keranjang->qty;
            $keranjang->save();
        } else {
            $keranjangItem = [
                'id_transaksi' => $request->id_transaksi,
                'id_barang' => $produk->id,
                'nama' => $produk->nama,
                'qty' => $qty,
                'harga' => $harga,
                'subtotal' => $subtotal,
            ];
            Keranjang::create($keranjangItem);
        }
        return redirect()->back()->with('success', 'Keranjang berhasil ditambah dan diperbaharui!');
    }

    public function edit_qty(Request $request)
    {
        $id = $request->input('id');
        $new_qty = $request->input('qty');
        $item = Keranjang::findOrFail($id);
        $produk = Data_barang::find($item->id_barang);
        $diff_qty = $new_qty - $item->qty;
        $item->qty = $new_qty;
        $item->save();
        $produk->qty -= $diff_qty;
        $produk->save();
        $new_subtotal = $item->harga * $new_qty;
        $item->subtotal = $new_subtotal;
        $item->save();
        return redirect()->back()->with('success', 'Qty berhasil diperbarui !');
    }

    public function hapus_keranjang($id)
    {
        $item = Keranjang::findOrFail($id);

        // 🔥 cek apakah ini barang dari database
        if ($item->id_barang) {

            $produk = Data_barang::find($item->id_barang);

            if ($produk) {
                $produk->qty += $item->qty;
                $produk->save();
            }
        }

        // 🔥 hapus item keranjang (manual / barang tetap dihapus)
        $item->delete();

        return redirect()->back()->with('sukses', 'Barang berhasil dihapus dari keranjang !');
    }

    public function checkout(Request $request)
    {
        $id_transaksi = $request->input('id_transaksi');
        $keranjang = Keranjang::where('id_transaksi', $id_transaksi)->get();

        if ($keranjang->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        $total = $keranjang->sum('subtotal');

        // 1. Pindahkan ke detail_transaksi
        foreach ($keranjang as $item) {
            DetailTransaksi::create([
                'id_transaksi' => $id_transaksi,
                'id_barang'    => $item->id_barang,
                'nama_barang'  => $item->nama,
                'harga'        => $item->harga,
                'qty'          => $item->qty,
                'subtotal'     => $item->subtotal,
            ]);
        }

        // 2. Update transaksi jadi final
        $transaksi = Transaksi::findOrFail($id_transaksi);
        $transaksi->update([
            'total_belanja' => $total,
            'bayar'         => $request->input('bayar'),
            'kembalian'     => $request->input('kembalian'),
            'status'        => 'selesai',
            'kasir'         => auth()->user()->nama
        ]);

        // 3. CETAK NOTA (Panggil fungsi private)
        $this->printNotaUmum($id_transaksi);

        // 4. Hapus keranjang
        Keranjang::where('id_transaksi', $id_transaksi)->delete();

        session()->put('transaksi_id', $id_transaksi);
        return redirect('transaksi')->with('sukses', 'Transaksi Berhasil!');
    }

    private function printNotaUmum($id)
    {
        $transaksi = Transaksi::find($id);
        $details = DetailTransaksi::where('id_transaksi', $id)->get();

        // Ambil info member jika ada
        $nama_member = "Umum";
        if ($transaksi->id_member) {
            $m = Data_member::find($transaksi->id_member);
            $nama_member = $m ? $m->nama_member : "Umum";
        }

        try {
            $connector = new WindowsPrintConnector("bener");
            $printer = new Printer($connector);

            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // Logo Toko
            $logoPath = public_path('assets/dist/img/logo.png');
            if (file_exists($logoPath)) {
                $logo = EscposImage::load($logoPath, false);
                $printer->bitImage($logo);
            } else {
                $printer->setEmphasis(true);
                $printer->text("ANGEL CELL\n");
                $printer->setEmphasis(false);
            }

            // Header
            $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
            $printer->text("Telp: 08xx-xxxx-xxxx\n");
            $printer->text(now()->format('d/m/Y H:i') . "\n");
            $printer->text("--------------------------------\n");

            // Info Transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(sprintf("%-10s: %s\n", "Nota", "#" . $id));
            $printer->text(sprintf("%-10s: %s\n", "Kasir", auth()->user()->nama));
            $printer->text(sprintf("%-10s: %s\n", "Pelanggan", $nama_member));
            $printer->text("--------------------------------\n");

            // Daftar Barang
            foreach ($details as $d) {
                $nama = strlen($d->nama_barang) > 30 ? substr($d->nama_barang, 0, 27) . '...' : $d->nama_barang;
                $printer->text("$nama\n");
                $printer->text(
                    sprintf(
                        "%d x %-10s %15s\n",
                        $d->qty,
                        number_format($d->harga, 0, '.', '.'),
                        number_format($d->subtotal, 0, '.', '.')
                    )
                );
            }

            $printer->text("--------------------------------\n");

            // Total, Bayar, Kembali
            $printer->setEmphasis(true);
            $printer->text(sprintf("%-15s %16s\n", "TOTAL", "Rp " . number_format($transaksi->total_belanja, 0, '.', '.')));
            $printer->setEmphasis(false);
            $printer->text(sprintf("%-15s %16s\n", "BAYAR", "Rp " . number_format($transaksi->bayar, 0, '.', '.')));
            $printer->text(sprintf("%-15s %16s\n", "KEMBALI", "Rp " . number_format($transaksi->kembalian, 0, '.', '.')));

            // Footer
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih Atas Kunjungan Anda\n");
            $printer->text("Barang yang sudah dibeli\n");
            $printer->text("tidak dapat ditukar/dikembalikan\n");

            $printer->feed(3);
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            Log::error("Gagal Cetak Nota Umum: " . $e->getMessage());
        }
    }

    public function dataBarang(Request $request)
    {
        $dataBarang = Data_barang::select('*');
        if ($request->has('search') && !empty($request->search['value'])) {
            $keyword = $request->search['value'];
            $dataBarang->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('qty', 'like', "%{$keyword}%")
                    ->orWhere('harga_umum', 'like', "%{$keyword}%")
                    ->orWhere('harga_member', 'like', "%{$keyword}%");
            });
        }
        return DataTables::of($dataBarang)
            ->addColumn('action', function ($data) {
                return
                    '<form method="post" action="/tambah_keranjang">' .
                    '<div class="row">' .
                    '<div class="col-md-8">' .
                    '<input type="hidden" name="_token" value="' . csrf_token() . '">' .
                    '<input type="hidden" name="id" value="' . $data->id . '">' .
                    '<input class="form-control" type="number" name="jumlah" min="1" max="' . $data->qty . '" value="1">' .
                    '</div>' .
                    '<div class="col-md-4">' .
                    '<button class="btn btn-info" type="submit"><i class="fa-solid fa-cart-plus"></i></button>' .
                    '</div>' .
                    '</div>' .
                    '</form>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function hapus_transaksi($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // 🔁 BALIKIN STOK DARI KERANJANG
        $keranjang = Keranjang::where('id_transaksi', $transaksi->id)->get();

        foreach ($keranjang as $item) {

            if (!$item->id_barang) continue;

            $barang = Data_barang::find($item->id_barang);
            if (!$barang) continue;

            $barang->qty += $item->qty;
            $barang->save();
        }

        // 🧹 HAPUS RELASI
        Keranjang::where('id_transaksi', $transaksi->id)->delete();
        DetailTransaksi::where('id_transaksi', $transaksi->id)->delete();
        DetailTransaksiServis::where('id_transaksi', $transaksi->id)->delete();

        // ❌ HAPUS TRANSAKSI
        $transaksi->delete();

        return redirect()->back()->with('sukses', 'Transaksi berhasil dihapus!');
    }

    public function tambah_manual(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'qty' => 'required|numeric|min:1',
            'id_transaksi' => 'required'
        ]);

        Keranjang::create([
            'id_transaksi' => $request->id_transaksi,
            'id_barang' => null, // 🔥 penting (karena bukan dari barang)
            'nama' => $request->nama,
            'harga' => $request->harga,
            'qty' => $request->qty,
            'subtotal' => $request->harga * $request->qty,
        ]);

        return redirect()->back()->with('sukses', 'Item manual ditambahkan!');
    }
}
