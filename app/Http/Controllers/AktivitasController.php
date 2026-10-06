<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function index(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggal_akhir = $request->tanggal_akhir ?? now()->toDateString();

        $logs = LogAktivitas::whereDate('created_at', '>=', $tanggal_awal)
            ->whereDate('created_at', '<=', $tanggal_akhir)
            ->when($request->filled('aksi'), fn($q) => $q->where('aksi', $request->aksi))
            ->when($request->filled('model'), fn($q) => $q->where('model', $request->model))
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($w) use ($request) {
                    $w->where('nama_user', 'like', '%' . $request->q . '%')
                        ->orWhere('keterangan', 'like', '%' . $request->q . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('aktivitas.index', compact('logs', 'tanggal_awal', 'tanggal_akhir'));
    }
}
