<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        $nik = Auth::guard('karyawan')->user()->nik;
        $absensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $today)->first();
        $history = DB::table('presensi')->where('nik', $nik)->whereYear('tgl_presensi', date('Y'))->whereMonth('tgl_presensi', date('m'))->get();        
        
        return view('dashboard.index',compact('absensi','history'));
    }
}
