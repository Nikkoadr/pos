<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data_member;

class MemberController extends Controller
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

    public function data_member()
    {
        $data_member = Data_member::all();
        return view('data_member', compact('data_member'));
    }
    public function tambah_data_member(Request $request)
    {
        $this->validate($request, [
            'id_toko' => ['required'],
            'nama_member' => ['required'],
            'nomor_hp' => ['required', 'string'],
            'alamat' => ['required'],
        ]);
        Data_member::create([
            'id_toko'   => $request->id_toko,
            'nama_member'   => $request->nama_member,
            'nomor_hp'   => $request->nomor_hp,
            'alamat'   => $request->alamat,

        ]);
        return redirect()->back()->with(['success' => 'Data berhasil ditambahkan!']);
    }
    public function view_edit_data_member($id)
    {
        $data = Data_member::findOrFail($id);
        return view('layouts.component.view_edit_data_member', compact('data'));
    }

    public function update_data_member(Request $request, $id)
    {
        $data = Data_member::findOrFail($id);
        $validatedData = $request->validate([
            'nama_member' => ['required'],
            'nomor_hp' => ['required'],
            'alamat' => ['required'],
        ]);
        $data->update($validatedData);
        return redirect('data_member')->with(['success' => 'Data Barang Berhasil Di Update']);
    }
    public function hapus_data_member($id)
    {
        $data = Data_member::findOrFail($id);
        $data->delete();
        return redirect()->back()->with(['success' => 'Data Barang Berhasil di Hapus']);
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');
        $members = Data_member::where('nama_member', 'like', '%' . $keyword . '%')->get();

        return response()->json($members);
    }
}
