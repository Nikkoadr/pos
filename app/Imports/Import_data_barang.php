<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Data_barang;

class Import_data_barang implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Bersihkan format rupiah
     */
    private function cleanRupiah($value)
    {
        if (!$value) {
            return 0;
        }
        $value = (string) $value;
        $value = preg_replace('/[^0-9]/', '', $value);
        return (int) $value;
    }

    /**
     * Bersihkan dan validasi kategori
     */
    private function cleanKategori($value)
    {
        if (!$value) {
            return 'umum';
        }
        $kat = strtolower(trim($value));
        // Daftar kategori yang diizinkan
        $allowed = ['umum', 'vocer', 'sparepart', 'aksesoris'];
        return in_array($kat, $allowed) ? $kat : 'umum';
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Data_barang([
            'id_toko'       => 1,
            'id_supplier'   => 1,
            'barcode'       => $row['barcode'],
            'nama'          => $row['nama_barang'],
            'kategori'      => $this->cleanKategori($row['kategori'] ?? 'umum'),
            'qty'           => $row['qty'],
            'harga_modal'   => $this->cleanRupiah($row['harga_modal']),
            'harga_umum'    => $this->cleanRupiah($row['harga_umum']),
            'harga_member'  => $this->cleanRupiah($row['harga_member']),
        ]);
    }

    /**
     * Aturan Validasi untuk setiap baris di Excel
     */
    public function rules(): array
    {
        return [
            'barcode'      => ['required', 'distinct', 'unique:data_barang,barcode'],
            'nama_barang'  => ['required', 'string', 'max:255'],
            'kategori'     => ['nullable', 'string'], // tidak wajib, akan di-clean di model
            'qty'          => ['required', 'numeric', 'min:0'],
            'harga_modal'  => ['required'],
            'harga_umum'   => ['required'],
        ];
    }

    /**
     * Kustomisasi pesan error (Opsional)
     */
    public function customValidationMessages()
    {
        return [
            'barcode.required'    => 'Kolom barcode wajib diisi.',
            'barcode.distinct'    => 'Barcode tidak boleh duplikat di dalam file Excel.',
            'barcode.unique'      => 'Barcode sudah terdaftar di database.',
            'nama_barang.required' => 'Nama barang tidak boleh kosong.',
            'qty.numeric'         => 'Jumlah (QTY) harus berupa angka.',
        ];
    }
}
