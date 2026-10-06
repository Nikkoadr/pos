<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LogAktivitas;
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
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function index()
    {
        $karyawan = User::all();
        return view('users.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'role' => 'required|in:admin,karyawan',
            'nomor_hp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        User::create([
            'nama' => $request->nama,
            'role' => $request->role,
            'nomor_hp' => $request->nomor_hp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        LogAktivitas::catat('tambah', 'user', null, "Tambah karyawan {$request->nama} ({$request->role})");
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
        $berubah = $karyawan->getChanges();
        unset($berubah['updated_at'], $berubah['password']);
        LogAktivitas::catat('ubah', 'user', $karyawan->id, "Ubah karyawan {$karyawan->nama}", $berubah);
        return redirect('/data_karyawan')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $karyawan = User::findOrFail($id);
        LogAktivitas::catat('hapus', 'user', $karyawan->id, "Hapus karyawan {$karyawan->nama} ({$karyawan->role})");
        $karyawan->delete();
        return back()->with('success', 'Karyawan berhasil dihapus');
    }
}
