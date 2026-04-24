<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksiServis;

class ServisController extends Controller
{
    public function transaksiServis($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        return view('servis.buat_servis', compact('transaksi', 'servis'));
    }

    public function store_servis(Request $request, $id)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'nama' => 'required',
            'nohp' => 'required',
            'merk' => 'required',
            'kerusakan' => 'required',
        ]);

        // 🔥 cek apakah sudah ada data servis
        $servis = DetailTransaksiServis::where('id_transaksi', $id)->first();

        if ($servis) {
            // ================= UPDATE =================
            $servis->update([
                'tanggal_masuk' => $request->tanggal_masuk,
                'nama' => $request->nama,
                'nohp' => $request->nohp,
                'alamat' => $request->alamat,
                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'kerusakan' => $request->kerusakan,
                'kondisi' => $request->kondisi,
                'pin' => $request->pin,
                'sandi' => $request->sandi,
                'pola' => $request->pola,
            ]);

            $message = 'Servis berhasil diupdate!';
        } else {
            // ================= CREATE =================
            DetailTransaksiServis::create([
                'id_transaksi' => $id,
                'kode_servis' => 'SRV-' . date('YmdHis'),

                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_dikerjakan' => null,
                'tanggal_diambil' => null,

                'nama' => $request->nama,
                'nohp' => $request->nohp,
                'alamat' => $request->alamat,

                'merk' => $request->merk,
                'tipe' => $request->tipe,
                'kerusakan' => $request->kerusakan,

                'kondisi' => $request->kondisi,
                'pin' => $request->pin,
                'sandi' => $request->sandi,
                'pola' => $request->pola,
            ]);

            $message = 'Servis berhasil ditambahkan!';
        }

        return redirect('/proses_transaksi_' . $id)
            ->with('success', $message);
    }
}
