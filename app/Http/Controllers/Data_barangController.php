<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data_barang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Import_data_barang;
use App\Exports\Export_data_barang;

class Data_barangController extends Controller
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

    public function data_barang()
    {
        $data_barang = Data_barang::all();
        return view('barang.data_barang', compact(['data_barang']));
    }

    public function tambah_data_barang(Request $request)
    {
        $this->validate($request, [
            'id_toko' => ['required'],
            'id_supplier' => ['required'],
            'barcode' => ['required'],
            'nama_barang' => ['required'],
            'qty' => ['required', 'string'],
            'harga_modal' => ['required'],
            'harga_umum' => ['required',],
            'harga_member' => ['required',],
        ]);
        Data_barang::create([
            'id_toko'   => $request->id_toko,
            'id_supplier'   => $request->id_supplier,
            'barcode'   => $request->barcode,
            'nama'   => $request->nama_barang,
            'qty'   => $request->qty,
            'harga_modal'   => $request->harga_modal,
            'harga_umum'   => $request->harga_umum,
            'harga_member'   => $request->harga_member,
        ]);
        return redirect()->back()->with(['success' => 'Data berhasil ditambahkan!']);
    }

    public function view_edit_data_barang($id)
    {
        $data = Data_barang::findOrFail($id);
        return view('layouts.component.view_edit_data_barang', compact('data'));
    }

    public function update_data_barang(Request $request, $id)
    {
        $data = Data_barang::findOrFail($id);
        $validatedData = $request->validate([
            'barcode' => ['required'],
            'nama' => ['required'],
            'qty' => ['required', 'numeric'],
            'harga_modal' => ['required', 'numeric'],
            'harga_umum' => ['required', 'numeric'],
            'harga_member' => ['required', 'numeric'],
        ]);
        $data->update($validatedData);
        return redirect('/data_barang')->with(['success' => 'Data Barang Berhasil Di Update']);
    }

    public function import_data_barang()
    {
        Excel::import(new Import_data_barang, request()->file('import'));
        return back()->with(['success' => 'Data Berhasil Diimport!']);
    }

    public function export_data_barang()
    {
        return Excel::download(new Export_data_barang, 'data_barang.xlsx');
    }

    public function hapus_data_barang($id)
    {
        $data = Data_barang::findOrFail($id);
        $data->delete();
        return redirect()->back()->with(['success' => 'Data Barang Berhasil di Hapus']);
    }
    public function hapusMultiple(Request $request)
    {
        $ids = $request->ids;
        $idArray = explode(",", $ids);
        Data_Barang::whereIn('id', $idArray)->delete();
        return response()->json([
            'status' => true,
            'message' => "Data barang berhasil dihapus."
        ]);
    }

    public function cetakBarcode(Request $request)
    {
        if (!$request->ids) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
        }
        $idArray = explode(',', $request->ids);
        $data_barang = Data_barang::whereIn('id', $idArray)->get();
        return view('barang.print_barcode', compact('data_barang'));
    }

    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah_tambah' => 'required|integer|min:1'
        ]);
        $barang = Data_barang::findOrFail($id);
        $barang->increment('qty', $request->jumlah_tambah);
        return redirect()->back()->with('success', 'Stok berhasil ditambahkan!');
    }
}
