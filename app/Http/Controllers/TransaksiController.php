<?php

namespace App\Http\Controllers;

use App\Models\Data_member;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Data_barang;
use App\Models\Keranjang;
use App\Models\Nota;
use App\Models\Detail_nota;
use Yajra\DataTables\DataTables;

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
        $transaksi = Transaksi::latest()->get();
        $member = Data_member::all();
        foreach ($transaksi as $data) {
            $nama_member = Data_member::find($data->id_member);
            $data->nama_member = $nama_member ? $nama_member->nama_member : "Tidak ada Member";
        }

        return view('buat_transaksi', compact('transaksi', 'member'));
    }


    public function buat_transaksi(Request $request)
    {
        Transaksi::create([
            'jenis_transaksi' => $request->jenis_transaksi,
            'id_member' => $request->id_member,
            'tanggal_transaksi' => Carbon::now(),
        ]);

        return redirect('transaksi')->with('sukses', 'transaksi berhasil dibuat');
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
        // Mengambil transaksi berdasarkan ID
        $transaksi = Transaksi::findOrFail($id);

        // Mengambil semua data barang
        $data_barang = Data_barang::all();

        // Mengambil semua entri keranjang untuk transaksi ini
        $keranjang = Keranjang::where('id_transaksi', $id)->get();

        // Mengambil semua data member
        $member = Data_member::all();

        // Mengambil nama member berdasarkan id_member dari transaksi
        $nama_member = Data_member::find($transaksi->id_member);

        // Menambahkan properti nama_member ke objek transaksi
        $transaksi->nama_member = $nama_member ? $nama_member->nama_member : "Tidak ada Member";

        // Menghitung total
        $total = $this->calculateTotal($keranjang);

        // Mengirimkan data ke tampilan
        return view('proses_transaksi', compact('transaksi', 'total', 'keranjang', 'data_barang', 'member'));
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

        $harga = $request->id_member ? $produk->harga_jual2 : $produk->harga_jual1; // Tentukan harga berdasarkan keberadaan id_member
        $subtotal = $harga * $qty; // Hitung subtotal berdasarkan harga yang sudah ditentukan dan jumlah

        if ($keranjang) {
            // Jika item dengan id_barang dan id_transaksi yang sama sudah ada,
            // edit item tersebut
            $keranjang->qty += $qty;
            $keranjang->subtotal = $harga * $keranjang->qty; // Perbarui subtotal dengan harga yang baru ditentukan
            $keranjang->save();
        } else {
            // Jika tidak ada item dengan id_barang dan id_transaksi yang sama,
            // tambahkan item baru ke keranjang
            $keranjangItem = [
                'id_transaksi' => $request->id_transaksi,
                'id_barang' => $produk->id,
                'nama' => $produk->nama,
                'qty' => $qty,
                'harga' => $harga, // Gunakan harga yang sudah ditentukan
                'subtotal' => $subtotal, // Gunakan subtotal yang sudah dihitung
            ];
            Keranjang::create($keranjangItem);
        }

        return redirect()->back()->with('success', 'Keranjang berhasil ditambah dan diperbaharui!');
    }
    public function edit_qty(Request $request)
    {
        // Ambil data dari formulir
        $id = $request->input('id');
        $new_qty = $request->input('qty');

        // Temukan item keranjang yang sesuai
        $item = Keranjang::findOrFail($id);

        // Temukan produk yang terkait
        $produk = Data_barang::find($item->id_barang);

        // Hitung perbedaan antara qty baru dan qty lama
        $diff_qty = $new_qty - $item->qty;

        // Update qty pada item keranjang
        $item->qty = $new_qty;
        $item->save();

        // Update qty pada data_barang
        $produk->qty -= $diff_qty; // Mengurangi qty pada data_barang
        $produk->save();

        // Hitung ulang subtotal
        $new_subtotal = $item->harga * $new_qty;

        // Update subtotal pada item keranjang
        $item->subtotal = $new_subtotal;
        $item->save();

        return redirect()->back()->with('success', 'Qty berhasil diperbarui !');
    }

    public function hapus_keranjang($id)
    {
        $item = Keranjang::findOrFail($id);
        $produk = Data_barang::find($item->id_barang);
        $produk->qty += $item->qty;
        $produk->save();
        $item->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang !');
    }

    public function checkout(Request $request)
    {
        $id_transaksi = $request->input('id_transaksi');
        $keranjang = Keranjang::where('id_transaksi', $id_transaksi)->get();
        $total = $keranjang->sum('subtotal');

        $nota = new Nota();
        $nota->jenis_transaksi = $request->input('jenis_transaksi');
        $nota->member = $request->input('member');
        $nota->tanggal_transaksi = Carbon::now();
        $nota->kasir = Auth()->user()->nama;
        $nota->total_belanja = $total;
        $nota->bayar = $request->input('bayar');
        $nota->kembalian = $request->input('kembalian');
        $nota->save();

        // Buat detail nota
        foreach ($keranjang as $item) {
            Detail_nota::create([
                'id_nota' => $nota->id,
                'id_barang' => $item->id_barang,
                'nama_barang' => $item->nama,
                'harga' => $item->harga,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
            ]);
        }

        // Hapus keranjang dan transaksi setelah proses checkout
        Keranjang::where('id_transaksi', $id_transaksi)->delete();
        Transaksi::where('id', $id_transaksi)->delete();

        // Simpan id nota dalam session untuk referensi
        session()->put('transaksi_id', $nota->id);

        return redirect('transaksi')->with('transaksi_sukses', 'Transaksi Berhasil !');
    }
}
