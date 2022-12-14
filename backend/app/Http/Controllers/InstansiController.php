<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instansi;
use App\Kelurahan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InstansiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input()) {
            $instansi = Instansi::where('instansi', '=', 'Kecamatan')
            ->where('id','=', $request->id)
            ->with('kelurahan')
            ->orderBy('nama_instansi', 'asc')
            ->get();
             $count = Instansi::select()
            ->where('instansi', '=', 'Kecamatan')
            ->where('id','=', $request->id)
            ->count();
                    } 
        else {
            $instansi = Instansi::where('instansi', '=', 'Kecamatan')
            ->with('kelurahan')
            ->orderBy('nama_instansi', 'asc')
            ->get();
            $count = Instansi::select()
            ->where('instansi', '=', 'Kecamatan')
            ->count();
        }

        return response(['data' => $instansi,'total' => $count], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addInstansi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_instansi' => 'required|string|unique_with:instansi, nama_instansi',
            'lat' => ['regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'lng' => ['regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 400);
        }

        Instansi::create($request->all());
        $response = ["message" => "Data berhasil disimpan"];
        return response($response, 201);

    }

    public function addKelurahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'instansi_id' => 'required',
            'nama_kelurahan' => 'required|string|unique_with:kelurahan, instansi_id'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 400);
        }

        Kelurahan::create($request->all());
        $response = ["message" => "Data berhasil disimpan"];
        return response($response, 201);
    }
    public function totalData(Request $request)
    {
        if ($request->filled('periode')) {
            $realisasi = DB::table('db_realisasi_pbb')
                ->select(
                    'instansi_id',
                    DB::raw('COUNT(db_realisasi_pbb.id) as total_pbb')
                )
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified', '=', 2)
                ->groupBy(
                    'instansi_id'
                );
            $paten = DB::table('db_rekapitulasi_paten')
                ->select('instansi_id', DB::raw('COUNT(db_rekapitulasi_paten.id) as total_paten'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified', '=', 2)
                ->groupBy(
                    'instansi_id'
                );
            $kependudukan = DB::table('db_kependudukan')
                ->select('instansi_id', DB::raw('COUNT(db_kependudukan.id) as total_kependudukan'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified', '=', 2)
                ->groupBy(
                    'instansi_id'
                );
            $camat = DB::table('db_kegiatan_camat')
                ->select('instansi_id', DB::raw('COUNT(db_kegiatan_camat.id) as total_camat'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified', '=', 2)
                ->groupBy(
                    'instansi_id'
                );
            $bencana = DB::table('db_bencana_alam')
                ->select('instansi_id', DB::raw('COUNT(db_bencana_alam.id) as total_bencana'))
                ->where(DB::raw("DATE_FORMAT(periode, '%Y-%m')"), '=', $request->get('periode'))
                ->where('is_verified', '=', 2)
                ->groupBy(
                    'instansi_id'
                );
            $instansi = DB::table('instansi')
                ->select(
                    'instansi.id',
                    'instansi.nama_instansi',
                    'lat',
                    'lng',
                    'realisasi.total_pbb',
                    'paten.total_paten',
                    'kependudukan.total_kependudukan',
                    'camat.total_camat',
                    'bencana.total_bencana'
                )
                ->leftJoinSub($realisasi, 'realisasi', function ($join) {
                    $join->on('instansi.id', 'realisasi.instansi_id');
                })
                ->leftJoinSub($paten, 'paten', function ($join) {
                    $join->on('instansi.id', 'paten.instansi_id');
                })
                ->leftJoinSub($kependudukan, 'kependudukan', function ($join) {
                    $join->on('instansi.id', 'kependudukan.instansi_id');
                })
                ->leftJoinSub($camat, 'camat', function ($join) {
                    $join->on('instansi.id', 'camat.instansi_id');
                })
                ->leftJoinSub($bencana, 'bencana', function ($join) {
                    $join->on('instansi.id', 'bencana.instansi_id');
                })
                ->where('instansi', '=', 'Kecamatan')
                ->get();
            } 
            else {
                // $instansi = DB::table('db_realisasi_pbb')
                // ->select(DB::raw('count(*) as total_pbb'))
                // ->where('is_verified','=', 0)
                // ->get();
                // $realisasi = DB::table('db_realisasi_pbb')
                // ->select(
                //     DB::raw('count(*) as total_pbb')
                // )
                // ->where('is_verified' ,'=',2)
                // ->get();
                // $paten = DB::table('rekapitulasi_paten')
                // ->select(
                //     DB::raw('count(*) as total_paten')
                // )
                // ->where('is_verified' ,'=',2)
                // ->get();
                
                // $instansi = $realisasi->merge($paten);
            }
            return response()->json(['data' => $instansi]);
    }
    public function totalSemua(Request $request) {
        if($request->filled('periode')) {
            $realisasi = DB::table('db_realisasi_pbb')
                ->select(
                    DB::raw('count(*) as total_pbb')
                )
                ->where('is_verified' ,'=',2)
                ->where(DB::raw('YEAR(periode)'), '=', $request->periode)
                ->get();
                $paten = DB::table('db_rekapitulasi_paten')
                ->select(
                    DB::raw('count(*) as total_paten')
                )
                ->where('is_verified' ,'=',2)
                ->where(DB::raw('YEAR(periode)'), '=', $request->periode)
                ->get();
                $kependudukan = DB::table('db_kependudukan')
                ->select(
                    DB::raw('count(*) as total_kependudukan')
                )
                ->where('is_verified' ,'=',2)
                ->where(DB::raw('YEAR(periode)'), '=', $request->periode)
                ->get();
                $bencana = DB::table('db_bencana_alam')
                ->select(
                    DB::raw('count(*) as total_bencana')
                )
                ->where('is_verified' ,'=',2)
                ->where(DB::raw('YEAR(periode)'), '=', $request->periode)
                ->get();
                
                $instansi = $realisasi->merge($paten)->merge($kependudukan)->merge($bencana);

                return response()->json(['data' => $instansi]);
        }
    }

    public function kelurahan(Request $request)
    {
        if($request->input()) {
            $count = DB::table('kelurahan')
            ->where('instansi_id', '=', $request->get('instansi_id'))
            ->count();
        } else {
            $count = DB::table('kelurahan')
            ->count();
        }
        // if ($request->input()) {
        //     $kelurahan = Kelurahan::where('id_instansi', '=', $request->id_instansi)->get();
        //     $count = Kelurahan::where('id_instansi', '=', $request->id_instansi)->get()->count();
        // } 
        // if (!$request->input()) {
        //     $kelurahan = DB::table('kelurahan')->get();
        //     $count = DB::table('kelurahan')->get()->count();
        // }

        return response(['total' => $count], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
