<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
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
    public function index()
    {
        $karyawan = User::all();
        return view('users.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        // VALIDASI
        $request->validate([
            'nama' => 'required',
            'role' => 'required|in:admin,karyawan',
            'nomor_hp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // SIMPAN USER
        User::create([
            'nama' => $request->nama,
            'role' => $request->role,
            'nomor_hp' => $request->nomor_hp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $karyawan = User::findOrFail($id);
        return view('users.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'role' => 'required|in:admin,karyawan',
            'nomor_hp' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $karyawan = User::findOrFail($id);

        $karyawan->nama = $request->nama;
        $karyawan->role = $request->role;
        $karyawan->nomor_hp = $request->nomor_hp;
        $karyawan->email = $request->email;

        if ($request->password) {
            $karyawan->password = Hash::make($request->password);
        }

        $karyawan->save();

        return redirect('/data_karyawan')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $karyawan = User::findOrFail($id);

        $karyawan->delete();

        return back()->with('success', 'Karyawan berhasil dihapus');
    }
}
