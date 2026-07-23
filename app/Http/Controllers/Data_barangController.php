<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data_barang;
use App\Models\PembelianBarang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Import_data_barang;
use App\Exports\Export_data_barang;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;

class Data_barangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Halaman utama data barang (tanpa data koleksi)
     */
    public function data_barang(Request $request)
    {
        $suppliers = Supplier::all();
        $kategori_list = ['umum', 'member', 'sparepart', 'aksesoris'];

        return view('barang.data_barang', compact('suppliers', 'kategori_list'));
    }

    public function data_barang_json(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $kategori = $request->get('kategori');

        $isAdmin = Gate::allows('isAdmin');

        // Kolom yang bisa diurutkan (disesuaikan berdasarkan role)
        if ($isAdmin) {
            $columns = ['checkbox', 'barcode', 'kategori', 'nama', 'qty', 'harga_modal', 'harga_umum', 'harga_member', 'aksi'];
        } else {
            $columns = ['checkbox', 'barcode', 'kategori', 'nama', 'qty', 'harga_umum'];
        }

        $query = Data_barang::query();

        // Jika BUKAN admin (karyawan), paksa filter hanya kategori 'umum'
        if (!$isAdmin) {
            $query->where('kategori', 'umum');
        } else if ($kategori) {
            // Jika Admin, gunakan filter kategori dari request
            $query->where('kategori', $kategori);
        }

        // Pencarian global
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('barcode', 'like', "%{$searchValue}%")
                    ->orWhere('nama', 'like', "%{$searchValue}%")
                    ->orWhere('kategori', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = Data_barang::count();
        $filteredRecords = $query->count();

        // Sorting
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        }

        // Pagination
        $data = $query->skip($start)->take($length)->get();

        // Format response
        $response = [];
        foreach ($data as $item) {

            // Badge kategori
            switch ($item->kategori) {
                case 'umum':
                    $badge = '<span class="badge badge-success">Umum</span>';
                    break;
                case 'member':
                    $badge = '<span class="badge badge-warning">Member</span>';
                    break;
                case 'sparepart':
                    $badge = '<span class="badge badge-primary">Sparepart</span>';
                    break;
                case 'aksesoris':
                    $badge = '<span class="badge badge-info">Aksesoris</span>';
                    break;
                default:
                    $badge = '<span class="badge badge-secondary">' . ucfirst($item->kategori) . '</span>';
            }

            // Data dasar untuk semua user
            $row = [
                'checkbox'   => '<input type="checkbox" class="sub_chk" data-id="' . $item->id . '">',
                'barcode'    => $item->barcode,
                'kategori'   => $badge,
                'nama'       => $item->nama,
                'qty'        => $item->qty,
                'harga_umum' => 'Rp ' . number_format($item->harga_umum, 0, ',', '.'),
            ];

            // Tambahkan kolom rahasia & aksi HANYA jika Admin
            if ($isAdmin) {
                $aksi = '<button type="button" class="btn btn-sm btn-success btn-tambah-stok" 
                        data-id="' . $item->id . '" 
                        data-nama="' . $item->nama . '" 
                        data-qty="' . $item->qty . '" 
                        data-harga_modal="' . $item->harga_modal . '" 
                        data-toggle="modal" data-target="#modal_tambah_stok">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <a href="' . route('edit_data_barang', $item->id) . '" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a href="' . route('hapus_data_barang', $item->id) . '" class="btn btn-sm btn-danger konfirmasi">
                    <i class="far fa-trash-alt"></i>
                </a>';

                $row['harga_modal']  = 'Rp ' . number_format($item->harga_modal, 0, ',', '.');
                $row['harga_member'] = 'Rp ' . number_format($item->harga_member, 0, ',', '.');
                $row['aksi']         = $aksi;
            }

            $response[] = $row;
        }

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $response,
        ]);
    }

    /**
     * Tambah data barang
     */
    public function tambah_data_barang(Request $request)
    {
        $this->validate($request, [
            'id_toko'      => ['required'],
            'id_supplier'  => ['required'],
            'barcode'      => ['required', 'unique:data_barang,barcode'],
            'kategori'     => ['required', 'in:umum,member,sparepart,aksesoris'],
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

    /**
     * View edit data barang
     */
    public function view_edit_data_barang($id)
    {
        $data = Data_barang::findOrFail($id);
        return view('layouts.component.view_edit_data_barang', compact('data'));
    }

    /**
     * Update data barang
     */
    public function update_data_barang(Request $request, $id)
    {
        $data = Data_barang::findOrFail($id);

        $validatedData = $request->validate([
            'barcode'      => ['required'],
            'kategori'     => ['required', 'in:umum,member,sparepart,aksesoris'],
            'nama'         => ['required'],
            'qty'          => ['required', 'numeric'],
            'harga_modal'  => ['required', 'numeric'],
            'harga_umum'   => ['required', 'numeric'],
            'harga_member' => ['required', 'numeric'],
        ]);

        $qty_lama = $data->qty;
        $data->update($validatedData);

        // Jika qty berubah, catat selisihnya
        $selisih_qty = $validatedData['qty'] - $qty_lama;
        if ($selisih_qty != 0) {
            $kode = 'ADJ-' . date('Ymd') . '-' . str_pad(PembelianBarang::count() + 1, 4, '0', STR_PAD_LEFT);
            PembelianBarang::create([
                'id_supplier'      => $data->id_supplier,
                'kode_pembelian'   => $kode,
                'tanggal_pembelian' => now(),
                'id_barang'        => $id,
                'qty'              => $selisih_qty,
                'harga_modal'      => $validatedData['harga_modal'],
            ]);
        }

        return redirect('/data_barang')->with('success', 'Data Barang Berhasil Di Update');
    }

    /**
     * Hapus data barang
     */
    public function hapus_data_barang($id)
    {
        $data = Data_barang::findOrFail($id);
        $data->pembelian()->delete();
        $data->delete();

        return redirect()->back()->with('success', 'Data Barang berhasil dihapus.');
    }

    /**
     * Hapus multiple
     */
    public function hapusMultiple(Request $request)
    {
        $ids = $request->ids;
        $idArray = explode(",", $ids);

        // Ambil data barang berdasarkan ID yang dipilih
        $dataBarang = Data_barang::whereIn('id', $idArray)->get();

        foreach ($dataBarang as $barang) {
            // Hapus relasi pembelian terlebih dahulu
            $barang->pembelian()->delete();
            // Hapus data barang
            $barang->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => "Data barang berhasil dihapus."
        ]);
    }

    /**
     * Import data barang
     */
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

    /**
     * Export data barang
     */
    public function export_data_barang()
    {
        return Excel::download(new Export_data_barang, 'data_barang.xlsx');
    }

    /**
     * Tambah stok barang
     */
    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'qty'         => 'required|integer|min:1',
            'harga_modal' => 'required|numeric|min:0',
        ]);

        $barang = Data_barang::findOrFail($id);

        $today = now()->format('Ymd');
        $lastPembelian = PembelianBarang::whereDate('tanggal_pembelian', today())
            ->orderByDesc('id')
            ->first();

        if ($lastPembelian) {
            $lastNumber = (int) substr($lastPembelian->kode_pembelian, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $kode = 'PO-' . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        PembelianBarang::create([
            'id_supplier'       => 1,
            'kode_pembelian'    => $kode,
            'tanggal_pembelian' => now(),
            'id_barang'         => $barang->id,
            'qty'               => $request->qty,
            'harga_modal'       => $request->harga_modal,
        ]);

        $barang->qty += $request->qty;
        $barang->harga_modal = $request->harga_modal;
        $barang->save();

        return redirect()->back()->with('success', "Stok barang '{$barang->nama}' berhasil ditambahkan!");
    }

    /**
     * Cetak barcode
     */
    public function cetakBarcode(Request $request)
    {
        if (!$request->ids) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih');
        }
        $idArray = explode(',', $request->ids);
        $data_barang = Data_barang::whereIn('id', $idArray)->get();
        return view('barang.print_barcode', compact('data_barang'));
    }
}
