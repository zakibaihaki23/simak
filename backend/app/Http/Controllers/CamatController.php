<?php

namespace App\Http\Controllers;

use App\Camat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class CamatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instansi_id = $request->input('instansi_id');
        $waktu = $request->input('waktu');
        $periode = $request->input('periode');
        $is_verified = $request->input('is_verified');


        $camat = Camat::query();
        if (!$request->except('page')) {
            $camat = Camat::select('instansi.nama_instansi', 'db_kegiatan_camat.*')
                    ->leftJoin('instansi','instansi.id','=', 'db_kegiatan_camat.instansi_id')
                    ->orderBy('waktu', 'DESC')
                    ->paginate(10);
            $count = Camat::select()->get()->count();
        } else {
             $camat = Camat::select('instansi.nama_instansi', 'db_kegiatan_camat.*')
                    ->leftJoin('instansi','instansi.id','=', 'db_kegiatan_camat.instansi_id')
                    ->when($instansi_id, function ($query, $instansi_id) {
                        return $query->where('instansi_id',$instansi_id);
                    })
                    ->when($waktu, function ($query, $waktu) {
                        return $query->where(DB::raw("DATE_FORMAT(waktu, '%Y-%m-%d')"), $waktu);
                    })
                    ->when($periode, function ($query, $periode) {
                        return $query->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), $periode);
                    })
                    ->when($is_verified, function ($query, $is_verified) {
                        return $query->where('is_verified', $is_verified);
                    })
                    ->orderBy('periode', 'DESC')
                    ->paginate(10);
            $count = Camat::when($instansi_id, function ($query, $instansi_id) {
                        return $query->where('instansi_id',$instansi_id);
                    })
                    ->when($waktu, function ($query, $waktu) {
                        return $query->where(DB::raw("DATE_FORMAT(waktu, '%Y-%m-%d')"), $waktu);
                    })
                    ->when($periode, function ($query, $periode) {
                        return $query->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), $periode);
                    })
                    ->when($is_verified, function ($query, $is_verified) {
                        return $query->where('is_verified', $is_verified);
                    })
                    ->get()->count();
        }
        
        return response(['data' => $camat, 'total' => $count], 200);
    }

    public function indexCamat(Request $request)
    {
        $instansi_id = $request->input('instansi_id');
        $waktu = $request->input('waktu');
        $periode = $request->input('periode');
        $is_verified = $request->input('is_verified');

        $camat = Camat::query();
        if (!$request->except('page')) {
            $camat = Camat::select('instansi.nama_instansi', 'db_kegiatan_camat.*')
                    ->leftJoin('instansi','instansi.id','=', 'db_kegiatan_camat.instansi_id')
                    ->where('is_verified','!=', 3)
                    ->orderBy('is_verified', 'ASC')
                    ->paginate(10);
            $count = Camat::select()
                    ->where('is_verified','!=', 3)
                    ->get()
                    ->count();
        } else {
             $camat = Camat::select('instansi.nama_instansi', 'db_kegiatan_camat.*')
                    ->leftJoin('instansi','instansi.id','=', 'db_kegiatan_camat.instansi_id')
                    ->when($instansi_id, function ($query, $instansi_id) {
                        return $query->where('instansi_id',$instansi_id)
                        ->where('is_verified','!=',3);
                    })
                    ->when($waktu, function ($query, $waktu) {
                        return $query->where(DB::raw("DATE_FORMAT(waktu, '%Y-%m-%d')"), $waktu)
                        ->where('is_verified','!=',3);
                    })
                    ->when($periode, function ($query, $periode) {
                        return $query->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), $periode)
                        ->where('is_verified','!=',3);
                    })
                    ->when($is_verified, function ($query, $is_verified) {
                        return $query->where('is_verified', $is_verified);
                    })
                    ->orderBy('periode', 'DESC')
                    ->paginate(10);
            $count = Camat::when($instansi_id, function ($query, $instansi_id) {
                        return $query->where('instansi_id',$instansi_id)
                        ->where('is_verified','!=',3);
                    })
                    ->when($waktu, function ($query, $waktu) {
                        return $query->where(DB::raw("DATE_FORMAT(waktu, '%Y-%m-%d')"), $waktu)
                        ->where('is_verified','!=',3);
                    })
                    ->when($periode, function ($query, $periode) {
                        return $query->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), $periode)
                        ->where('is_verified','!=',3);
                    })
                    ->when($is_verified, function ($query, $is_verified) {
                        return $query->where('is_verified', $is_verified);
                    })
                    ->get()->count();
        }

            return response(['data' => $camat, 'total' => $count], 200);

       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCamat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instansi_id' => 'required',

        ]);
        $valid = Validator::make($request->all(), [
            'periode' =>  'unique_with:db_kegiatan_camat, waktu'
        ]);
        

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        if ($valid->fails()) {
            return response()->json(['errors' => $valid->errors()->all()], 400);
        }

        Camat::create($request->all());
        $response = ["message" => "Data berhasil disimpan"];
        return response($response, 201);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCamat(Request $request, $id)
    {
        $camat = Camat::find($id);
        $camat->instansi_id = $request->input('instansi_id');
        $camat->waktu = $request->input('waktu');
        $camat->tempat = $request->input('tempat');
        $camat->uraian_kegiatan = $request->input('uraian_kegiatan');
        $camat->periode = $request->input('periode');
        $camat->is_verified = $request->input('is_verified');

        $camat->save();
        $response = ["message"=>"Data Berhasil Diubah"];

        return response()->json($response);
    }

    public function validasiCamat(Request $request, $id)
    {
        $camat = Camat::find($id);
        $camat->is_verified = $request->input('is_verified');
        $camat->save();
        $response = ["message"=>"Data Berhasil diverifikasi"];

        return response()->json($response);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Akta  $akta
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $camat = Camat::select('instansi.id','instansi.nama_instansi', 'db_kegiatan_camat.*')
        ->leftJoin('instansi','instansi.id','=', 'db_kegiatan_camat.instansi_id')
        ->find($id);
        return response(['message' => 'Success', 'data' => $camat], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Akta  $akta
     * @return \Illuminate\Http\Response
     */
    public function edit(Akta $akta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Akta  $akta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Akta $akta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Akta  $akta
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Camat::destroy($id);
    }
    public function export(Request $request)
    {

        if ($request->input('instansi_id', 'periode')) {

            $camat = DB::table('db_kegiatan_camat')
                ->select('uraian_kegiatan', 'tempat', 'waktu')
                ->where('instansi_id', '=', $request->get('instansi_id'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified','=', 2)
                ->orderBy('waktu', 'DESC')
                ->get();
            $periode = DB::table('db_realisasi_pbb')
                ->select('periode')
                ->where('instansi_id', '=', $request->get('instansi_id'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified','=', 2)
                ->take(1)
                ->get();
            $instansi = DB::table('db_kegiatan_camat')
                ->select('db_kegiatan_camat.instansi_id','instansi.nama_instansi')
                ->leftJoin('instansi','instansi.id','db_kegiatan_camat.instansi_id')
                ->where('instansi_id', '=', $request->get('instansi_id'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified','=',2)
                ->take(1)
                ->get();

            foreach ($instansi as $kecamatan) {
                foreach ($periode as $per) {
                    
                    $pdf = PDF::loadView('reportKegiatanCamat', ['camat' => $camat, 'periode' => $periode,'kecamatan' => $instansi])->setPaper([0, 0, 600, 1000], 'landscape');
                    return $pdf->stream('Laporan_Kegiatan_Camat_' . $kecamatan->nama_instansi . '_' . $per->periode . '.pdf');
                }
            }
        }
        if (!$request->filled('instansi_id')) {
            $response = ["message" => 'Harap Pilih Instansi Terlebih Dahulu'];
            return response($response, 400);
        }
        if (!$request->filled('periode')) {
            $response = ["message" => 'Harap Pilih Periode Terlebih Dahulu'];
            return response($response, 400);
        }
    }
}
