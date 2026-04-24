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

        $member = Data_member::all();

        $nama_member = Data_member::find($transaksi->id_member);

        $transaksi->nama_member = $nama_member ? $nama_member->nama_member : "Tidak ada Member";

        $total = $this->calculateTotal($keranjang);

        // 🔥 TAMBAHAN PENTING
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        return view('transaksi.proses_transaksi', compact(
            'transaksi',
            'total',
            'keranjang',
            'member',
            'servis' // 🔥 WAJIB ADA
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

        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang !');
    }

    public function checkout(Request $request)
    {
        $id_transaksi = $request->input('id_transaksi');

        // ambil data keranjang
        $keranjang = Keranjang::where('id_transaksi', $id_transaksi)->get();

        if ($keranjang->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong!');
        }

        $total = $keranjang->sum('subtotal');

        // 🔥 pindahkan ke detail_transaksi
        foreach ($keranjang as $item) {
            DetailTransaksi::create([
                'id_transaksi' => $id_transaksi,
                'id_barang' => $item->id_barang,
                'nama_barang' => $item->nama,
                'harga' => $item->harga,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
            ]);
        }

        // 🔥 update transaksi jadi final
        Transaksi::where('id', $id_transaksi)->update([
            'total_belanja' => $total,
            'bayar' => $request->input('bayar'),
            'kembalian' => $request->input('kembalian'),
            'status' => 'selesai',
            'kasir' => auth()->user()->nama
        ]);

        // 🧹 hapus keranjang (aman)
        Keranjang::where('id_transaksi', $id_transaksi)->delete();

        // simpan untuk keperluan print
        session()->put('transaksi_id', $id_transaksi);

        return redirect('transaksi')->with('transaksi_sukses', 'Transaksi Berhasil !');
    }

    public function dataBarang(Request $request)
    {
        $dataBarang = Data_barang::select('*');
        if ($request->has('search') && !empty($request->search['value'])) {
            $keyword = $request->search['value'];
            $dataBarang->where(function ($query) use ($keyword) {
                $query->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('qty', 'like', "%{$keyword}%")
                    ->orWhere('harga_jual1', 'like', "%{$keyword}%")
                    ->orWhere('harga_jual2', 'like', "%{$keyword}%");
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
        $detailTransaksi = DetailTransaksi::where('id_transaksi', $transaksi->id)->get();

        foreach ($detailTransaksi as $detail) {
            $barang = Data_barang::findOrFail($detail->id_barang);
            $barang->qty += $detail->qty;
            $barang->save();
        }
        DetailTransaksi::where('id_transaksi', $transaksi->id)->delete();
        $transaksi->delete();
        return redirect()->back();
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

        return redirect()->back()->with('success', 'Item manual ditambahkan!');
    }
}
