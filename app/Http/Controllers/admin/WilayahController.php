<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    //
    public function get_kota_kecamatan(Request $request) {
        $id = $request->id;
        $data = Wilayah::where('id_induk_wilayah', $id)->get();
        return response()->json($data);
    }
}
