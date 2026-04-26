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
use App\Models\Setting;

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
            'status' => 'aktif',
            'kasir' => auth()->user()->nama,
            'total_belanja' => 0
        ]);

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
            $total += $item->harga_jual * $item->qty;
        }
        return $total;
    }

    public function proses_transaksi(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // ambil keranjang
        $keranjang = Keranjang::where('id_transaksi', $id)->get();

        // ambil semua member
        $member = Data_member::all();

        // ambil nama member
        $nama_member = null;
        if ($transaksi->id_member) {
            $m = Data_member::find($transaksi->id_member);
            $nama_member = $m ? $m->nama_member : null;
        }

        // ✅ hitung total dari harga_jual * qty
        $total = $keranjang->sum(function ($item) {
            return $item->harga_jual * $item->qty;
        });

        // data servis
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        return view('transaksi.proses_transaksi', compact(
            'transaksi',
            'total',
            'keranjang',
            'member',
            'servis',
            'nama_member'
        ));
    }

    public function tambah_keranjang(Request $request)
    {
        $produk = Data_barang::find($request->input('id'));
        $qty = $request->input('jumlah');

        if ($qty > $produk->qty) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        // Kurangi stok
        $produk->qty -= $qty;
        $produk->save();

        // Ambil harga
        $harga = $request->id_member ? $produk->harga_member : $produk->harga_umum;
        $harga_modal = $produk->harga_modal;

        // Cek keranjang
        $keranjang = Keranjang::where('id_barang', $produk->id)
            ->where('id_transaksi', $request->id_transaksi)
            ->first();

        if ($keranjang) {
            $keranjang->qty += $qty;
            $keranjang->harga_modal = $harga_modal;
            $keranjang->harga_jual = $harga;
            $keranjang->save();
        } else {
            Keranjang::create([
                'id_transaksi' => $request->id_transaksi,
                'id_barang' => $produk->id,
                'nama' => $produk->nama,
                'qty' => $qty,
                'harga_modal' => $harga_modal,
                'harga_jual' => $harga,
            ]);
        }

        return redirect()->back()->with('success', 'Keranjang berhasil ditambah!');
    }

    public function edit_qty(Request $request)
    {
        $id = $request->input('id');
        $new_qty = (int) $request->input('qty');

        $item = Keranjang::findOrFail($id);
        $produk = Data_barang::find($item->id_barang);

        // Hitung selisih qty
        $diff_qty = $new_qty - $item->qty;

        // ✅ Validasi stok kalau nambah qty
        if ($diff_qty > 0 && $diff_qty > $produk->qty) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi!');
        }

        // Update stok (balikin atau ngurangin)
        $produk->qty -= $diff_qty;
        $produk->save();

        // Update qty keranjang
        $item->qty = $new_qty;
        $item->save();

        return redirect()->back()->with('success', 'Qty berhasil diperbarui!');
    }

    public function scanBarang(Request $request)
    {
        $barang = Data_barang::where('barcode', $request->input('barcode'))->first();

        if (!$barang) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Tentukan harga (member / umum)
        $harga_jual = $request->id_member ? $barang->harga_member : $barang->harga_umum;
        $harga_modal = $barang->harga_modal;

        // Cek keranjang
        $cek = DB::table('keranjang')
            ->where('id_transaksi', $request->id_transaksi)
            ->where('id_barang', $barang->id)
            ->first();

        if ($cek) {

            // Validasi stok
            if ($barang->qty < 1) {
                return response()->json(['error' => 'Stok habis'], 400);
            }

            DB::table('keranjang')
                ->where('id', $cek->id)
                ->update([
                    'qty' => $cek->qty + 1,
                    'harga_jual' => $harga_jual,
                    'harga_modal' => $harga_modal
                ]);
        } else {

            if ($barang->qty < 1) {
                return response()->json(['error' => 'Stok habis'], 400);
            }

            DB::table('keranjang')->insert([
                'id_transaksi' => $request->id_transaksi,
                'id_barang' => $barang->id,
                'nama' => $barang->nama,
                'harga_jual' => $harga_jual,
                'harga_modal' => $harga_modal,
                'qty' => 1,
            ]);
        }

        // Kurangi stok
        $barang->qty -= 1;
        $barang->save();

        return response()->json(['success' => true]);
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
            'id_barang' => null,
            'nama' => $request->nama,
            'harga_jual' => $request->harga,
            'harga_modal' => 0, // karena manual biasanya jasa / tidak ada modal
            'qty' => $request->qty,
        ]);

        return redirect()->back()->with('success', 'Item manual ditambahkan!');
    }

    public function hapus_keranjang($id)
    {
        $item = Keranjang::findOrFail($id);

        if ($item->id_barang) {

            $produk = Data_barang::find($item->id_barang);

            if ($produk) {
                $produk->qty += $item->qty;
                $produk->save();
            }
        }

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

        // Hitung total dari harga_jual × qty
        $total = $keranjang->sum(function ($item) {
            return $item->harga_jual * $item->qty;
        });

        foreach ($keranjang as $item) {
            DetailTransaksi::create([
                'id_transaksi' => $id_transaksi,
                'id_barang'    => $item->id_barang,
                'nama_barang'  => $item->nama,
                'harga_jual'   => $item->harga_jual,
                'harga_modal'  => $item->harga_modal,
                'qty'          => $item->qty,
            ]);
        }

        $transaksi = Transaksi::findOrFail($id_transaksi);
        $transaksi->update([
            'total_belanja' => $total,
            'bayar'         => $request->input('bayar'),
            'kembalian'     => $request->input('kembalian'),
            'status'        => 'selesai',
            'kasir'         => auth()->user()->nama
        ]);

        $this->printNotaUmum($id_transaksi);

        Keranjang::where('id_transaksi', $id_transaksi)->delete();

        session()->put('transaksi_id', $id_transaksi);
        return redirect('transaksi')->with('sukses', 'Transaksi Berhasil!');
    }

    private function printNotaUmum($id)
    {
        $transaksi = Transaksi::find($id);
        $details = DetailTransaksi::where('id_transaksi', $id)->get();
        $tanggal = now()->format('d M Y H:i:s');

        $nama_member = "Umum";
        if ($transaksi->id_member) {
            $m = Data_member::find($transaksi->id_member);
            $nama_member = $m ? $m->nama_member : "Umum";
        }

        try {
            $connector = new WindowsPrintConnector(Setting::first()->nama_printer);
            $printer = new Printer($connector);
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $logoPath = public_path('assets/dist/img/logo_print.png');
            if (file_exists($logoPath)) {
                try {
                    $logo = EscposImage::load($logoPath, true);
                    $printer->bitImage($logo);
                } catch (\Exception $e) {
                    $printer->setTextSize(2, 2);
                    $printer->setEmphasis(true);
                    $printer->text("ANGEL CELL\n");
                    $printer->setTextSize(1, 1);
                    $printer->setEmphasis(false);
                }
            }

            $printer->text("Jalan Jangga-Terisi Desa Jangga\n");
            $printer->text($tanggal . "\n");

            $printer->text("-----------------------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $printer->text(sprintf("%-10s: %s\n", "Nota", "#" . $id));
            $printer->text(sprintf("%-10s: %s\n", "Kasir", auth()->user()->nama));
            $printer->text(sprintf("%-10s: %s\n", "Pelanggan", $nama_member));

            $printer->text("-----------------------------------------------\n");

            foreach ($details as $d) {

                $nama = strlen($d->nama_barang) > 30
                    ? substr($d->nama_barang, 0, 27) . '...'
                    : $d->nama_barang;

                $printer->text($nama . "\n");

                $subtotal = $d->harga_jual * $d->qty;

                $printer->text(sprintf(
                    "%d x %-10s %15s\n",
                    $d->qty,
                    number_format($d->harga_jual, 0, '.', '.'),
                    number_format($subtotal, 0, '.', '.')
                ));
            }

            $printer->text("-----------------------------------------------\n");
            // ================= TOTAL =================
            $printer->setJustification(Printer::JUSTIFY_RIGHT);

            // 1. Baris TOTAL (Besar & Tebal)
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2); 
            // Pada ukuran (2,2), lebar kertas jadi terbatas (sekitar 22-24 karakter)
            $printer->text("TOTAL: Rp " . number_format($transaksi->total_belanja, 0, '.', '.') . "\n");

            // 2. Baris BAYAR & KEMBALI (Ukuran Normal)
            $printer->setTextSize(1, 1);
            $printer->setEmphasis(false);

            // Gunakan %48s agar teks didorong sejauh 48 karakter ke kanan
            $printer->text(sprintf("%48s\n", "BAYAR: Rp " . number_format($transaksi->bayar, 0, '.', '.')));
            $printer->text(sprintf("%48s\n", "KEMBALI: Rp " . number_format($transaksi->kembalian, 0, '.', '.')));

            // ================= FOOTER =================
            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setEmphasis(true);
            $printer->text("TERIMA KASIH\n");
            $printer->setEmphasis(false);

            $printer->text("Atas Kunjungan Anda\n");
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

        $keranjang = Keranjang::where('id_transaksi', $transaksi->id)->get();

        foreach ($keranjang as $item) {

            if (!$item->id_barang) continue;

            $barang = Data_barang::find($item->id_barang);
            if (!$barang) continue;

            $barang->qty += $item->qty;
            $barang->save();
        }

        Keranjang::where('id_transaksi', $transaksi->id)->delete();
        DetailTransaksi::where('id_transaksi', $transaksi->id)->delete();
        DetailTransaksiServis::where('id_transaksi', $transaksi->id)->delete();

        $transaksi->delete();

        return redirect()->back()->with('sukses', 'Transaksi berhasil dihapus!');
    }
}
