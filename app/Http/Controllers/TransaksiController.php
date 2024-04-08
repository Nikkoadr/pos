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
        $transaksi = Transaksi::findOrFail($id);
        $keranjang = Keranjang::where('id_transaksi', $id)->get();
        $member = Data_member::all();
        $nama_member = Data_member::find($transaksi->id_member);
        $transaksi->nama_member = $nama_member ? $nama_member->nama_member : "Tidak ada Member";
        $total = $this->calculateTotal($keranjang);
        return view('proses_transaksi', compact('transaksi', 'total', 'keranjang', 'member'));
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
        $harga = $request->id_member ? $produk->harga_jual2 : $produk->harga_jual1;
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
        Keranjang::where('id_transaksi', $id_transaksi)->delete();
        Transaksi::where('id', $id_transaksi)->delete();
        session()->put('transaksi_id', $nota->id);
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
}
