<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\data_member;

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

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');
        $members = data_member::where('nama_member', 'like', '%' . $keyword . '%')->get();

        return response()->json($members);
    }
}
