<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date('Y-m-d');
        $presensi = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->count();
        return view('presensi.create', compact('presensi'));
    }

    public function store(Request $request)
    {
        
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date('Y-m-d');
        $lokasi = $request->lokasi;
        $image = $request->image;
        $jam = date('H:i:s');
        
        $presensi = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->count();
        if ($presensi > 0) {
           $ket = "out";
        } else {
           $ket = "in";
        }
        $folderPath = "public/uploads/absensi/";
        $formatName = $nik.'-'.$tgl_presensi."-".$ket;
        $image_parts = explode(";base64,", $image);       
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName.'.png';
        $file = $folderPath . $fileName;
        $data = [
            'nik' => $nik,
            'tgl_presensi' => $tgl_presensi,
            'foto_in' => $fileName,
            'lokasi_in' => $lokasi,
            'jam_in' => $jam,            
        ];
        if ($presensi > 0) {
            $data = [               
                'tgl_presensi' => $tgl_presensi,
                'lokasi_out' => $lokasi,
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'nik' => $nik,            
            ];
            $simpan = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data);
            if ($simpan) {
                echo "success|Terima kasih Hati hati dijalan|out";
                 Storage::disk('public')->put($file, $image_base64);
            } else {
               echo "error|Gagal Absen hubungi admin| out";
            }        } else {
            $simpan = DB::table('presensi')->insert($data);
            if ($simpan) {
                 echo "success|Selamat Bekerja|in";
                 Storage::disk('public')->put($file, $image_base64);
            } else {
                echo "error|Gagal Absen hubungi admin|in";
            }            
        }
    }
}