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
        return view('data_barang', compact(['data_barang']));
    }
    public function tambah_data_barang(Request $request)
    {
        $this->validate($request, [
            'id_toko' => ['required'],
            'id_supplier' => ['required'],
            'nama_barang' => ['required'],
            'qty' => ['required', 'string'],
            'harga_modal' => ['required'],
            'harga_umum' => ['required',],
            'harga_grosir' => ['required',],
        ]);
        Data_barang::create([
            'id_toko'   => $request->id_toko,
            'id_supplier'   => $request->id_supplier,
            'nama'   => $request->nama_barang,
            'qty'   => $request->qty,
            'harga_modal'   => $request->harga_modal,
            'harga_jual1'   => $request->harga_umum,
            'harga_jual2'   => $request->harga_grosir,
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
            'nama' => ['required'],
            'qty' => ['required', 'numeric'],
            'harga_modal' => ['required', 'numeric'],
            'harga_jual1' => ['required', 'numeric'],
            'harga_jual2' => ['required', 'numeric'],
        ]);
        $data->update($validatedData);
        return redirect('data_barang')->with(['success' => 'Data Barang Berhasil Di Update']);
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
}
