<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data_barang;
use App\Models\PembelianBarang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Import_data_barang;
use App\Exports\Export_data_barang;
use App\Models\Supplier;

class Data_barangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function data_barang(Request $request)
    {
        $query = Data_barang::query();

        // Filter kategori (jika ada)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $data_barang = $query->get();
        $suppliers = Supplier::all();
        $kategori_list = ['umum', 'vocer', 'sparepart', 'aksesoris'];

        return view('barang.data_barang', compact('data_barang', 'suppliers', 'kategori_list'));
    }

    public function tambah_data_barang(Request $request)
    {
        $this->validate($request, [
            'id_toko'      => ['required'],
            'id_supplier'  => ['required'],
            'barcode'      => ['required', 'unique:data_barang,barcode'],
            'kategori'     => ['required', 'in:umum,vocer,sparepart,aksesoris'],
            'nama_barang'  => ['required'],
            'qty'          => ['required', 'string'],
            'harga_modal'  => ['required'],
            'harga_umum'   => ['required'],
            'harga_member' => ['required'],
        ]);

        $barang = Data_barang::create([
            'id_toko'      => $request->id_toko,
            'id_supplier'  => $request->id_supplier,
            'barcode'      => $request->barcode,
            'kategori'     => $request->kategori,
            'nama'         => $request->nama_barang,
            'qty'          => $request->qty,
            'harga_modal'  => $request->harga_modal,
            'harga_umum'   => $request->harga_umum,
            'harga_member' => $request->harga_member,
        ]);

        // Catat sebagai pembelian stok awal
        $kode = 'PO-' . date('Ymd') . '-' . str_pad(PembelianBarang::count() + 1, 4, '0', STR_PAD_LEFT);
        PembelianBarang::create([
            'id_supplier'      => $request->id_supplier,
            'kode_pembelian'   => $kode,
            'tanggal_pembelian' => now(),
            'id_barang'        => $barang->id,
            'qty'              => $request->qty,
            'harga_modal'      => $request->harga_modal,
        ]);

        return redirect()->back()->with('success', 'Data barang berhasil ditambahkan!');
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
            'barcode'      => ['required'],
            'kategori'     => ['required', 'in:umum,vocer,sparepart,aksesoris'],
            'nama'         => ['required'],
            'qty'          => ['required', 'numeric'],
            'harga_modal'  => ['required', 'numeric'],
            'harga_umum'   => ['required', 'numeric'],
            'harga_member' => ['required', 'numeric'],
        ]);

        // Simpan nilai lama
        $qty_lama = $data->qty;

        // Update data
        $data->update($validatedData);

        // Jika qty berubah, catat selisihnya sebagai penyesuaian
        $selisih_qty = $validatedData['qty'] - $qty_lama;

        if ($selisih_qty != 0) {
            $kode = 'ADJ-' . date('Ymd') . '-' . str_pad(PembelianBarang::count() + 1, 4, '0', STR_PAD_LEFT);
            PembelianBarang::create([
                'id_supplier'      => $data->id_supplier,
                'kode_pembelian'   => $kode,
                'tanggal_pembelian' => now(),
                'id_barang'        => $id,
                'qty'              => $selisih_qty, // bisa negatif
                'harga_modal'      => $validatedData['harga_modal'],
            ]);
        }

        return redirect('/data_barang')->with('success', 'Data Barang Berhasil Di Update');
    }

    public function import_data_barang(Request $request)
    {
        $request->validate([
            'import' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'import.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'import.mimes'    => 'Format file harus .xlsx atau .xls',
            'import.max'      => 'Ukuran file maksimal adalah 10 Megabyte.'
        ]);

        try {
            Excel::import(new Import_data_barang, $request->file('import'));
            return back()->with('success', 'Data Berhasil Diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errors = implode(', ', $failure->errors());
                $errorMessages[] = "<li>Baris ke-" . $failure->row() . ": " . $errors . "</li>";
            }
            $htmlErrors = "<ul style='text-align: left; max-height: 250px; overflow-y: auto; padding-left: 20px; font-size: 14px;'>" . implode('', $errorMessages) . "</ul>";
            return back()->with('error_import', $htmlErrors);
        }
    }

    public function export_data_barang()
    {
        return Excel::download(new Export_data_barang, 'data_barang.xlsx');
    }

    public function hapus_data_barang($id)
    {
        $data = Data_barang::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Data Barang Berhasil di Hapus');
    }

    public function hapusMultiple(Request $request)
    {
        $ids = $request->ids;
        $idArray = explode(",", $ids);
        Data_barang::whereIn('id', $idArray)->delete();
        return response()->json([
            'status'  => true,
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

    /**
     * Tambah stok barang + catat pembelian
     */
    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'qty'         => 'required|integer|min:1',
            'harga_modal' => 'required|numeric|min:0',
        ]);

        $barang = Data_barang::findOrFail($id);

        $kode = 'PO-' . date('Ymd') . '-' . str_pad(PembelianBarang::count() + 1, 4, '0', STR_PAD_LEFT);
        PembelianBarang::create([
            'id_supplier'      => 1,
            'kode_pembelian'   => $kode,
            'tanggal_pembelian' => now(),
            'id_barang'        => $id,
            'qty'              => $request->qty,
            'harga_modal'      => $request->harga_modal,
        ]);

        $barang = Data_barang::findOrFail($id);
        $barang->qty += $request->qty;
        $barang->harga_modal = $request->harga_modal;
        $barang->save();

        return redirect()->back()->with('success', "Stok barang '{$barang->nama}' berhasil ditambahkan!");
    }
}
