<?php

namespace App\Http\Controllers;

use App\Models\IbadahHarian1;
use App\Models\SiswaIbadahHarian;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\SubKelas;
use App\Http\Requests\StoreIbadahHarianRequest;
use App\Http\Requests\UpdateIbadahHarianRequest;

use Yajra\DataTables\DataTables;
use Yajra\DataTables\Utilities\Request;

class IbadahHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data_guru = Guru::all();
        $periode = Periode::where('status','aktif')->first();
        
        $data_kelas = Kelas::all()->except(7);

        $kelas_id = $request->kelas_id;
        if ($kelas_id == null) {
            $siswa = IbadahHarian1::where('periode_id', $periode->id)->get();
        } else {
            $siswa = IbadahHarian1::where('kelas_id', $kelas_id)->where('periode_id', $periode->id)->get();
        }

        

        return view('dataIbadahHarian.indexIbadahHarian', compact('siswa', 'data_kelas', 'kelas_id', 'data_guru'));
        
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
     * @param  \App\Http\Requests\StoreIbadahHarianRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //kelas_ibadah_harian_tambah,tambah_ibadah_harian_1,tambah_ibadah_harian_2,tambah_ibadah_harian_guru_1,tambah_ibadah_harian_guru_2 etc
        $semester = Periode::where('status', 'aktif')->first()->id;
        //validation
        $fields = [];
        $fields[] = 'kelas_ibadah_harian_tambah';
        $messages = [];
        $messages['kelas_ibadah_harian_tambah.required'] = 'Kolom kelas_ibadah_harian_tambah tidak boleh kosong!';
        $validator_rules = [];
        $validator_rules['kelas_ibadah_harian_tambah'] = 'required';

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'tambah_ibadah_harian_') !== false && strpos($key, 'tambah_ibadah_harian_guru_') === false) {
                $fields[] = $key;
            }
        }
        foreach ($fields as $key) {
            $messages[$key.'.required'] = 'Kolom '.$key.' tidak boleh kosong!';
            $validator_rules[$key] = 'required';
            if (strpos($key, 'tambah_ibadah_harian_') !== false && strpos($key, 'tambah_ibadah_harian_guru_') === false) {
                $index = str_replace('tambah_ibadah_harian_', '', $key);
                $messages['tambah_ibadah_harian_guru_'.$index.'.required'] = 'Kolom tambah_ibadah_harian_guru_'.$index.' tidak boleh kosong!';
                $validator_rules['tambah_ibadah_harian_guru_'.$index] = 'required';
            }
        }

        $request->validate($validator_rules, $messages);


        $kelas_id = $request->input('kelas_ibadah_harian_tambah');
        $new_ibadah_harian = [];
        $new_ibadah_harian_guru = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'tambah_ibadah_harian_guru_') !== false) {
                $new_ibadah_harian_guru[str_replace('tambah_ibadah_harian_guru_', '', $key)] = $value;
            }
            else if (strpos($key, 'tambah_ibadah_harian_') !== false) {
                $new_ibadah_harian[str_replace('tambah_ibadah_harian_', '', $key)] = $value;
            }
        }

        $berhasil = 0;
        $processed = 0;
        $new_ibadah_harian_id = [];
        foreach ($new_ibadah_harian as $key => $value) {
            $ibadah_harian = new IbadahHarian1;
            $ibadah_harian->kelas_id = $kelas_id;
            $ibadah_harian->nama_kriteria = $value;
            $ibadah_harian->guru_id = $new_ibadah_harian_guru[$key];
            $ibadah_harian->periode_id = $semester;
            if ($ibadah_harian->save()) {
                $berhasil++;
                $new_ibadah_harian_id[] = $ibadah_harian->id;
            }
            $processed++;
        }

        $sub_kelas_id = SubKelas::where('kelas_id', $kelas_id)->where('periode_id', $semester)->pluck('id')->toArray();

        // Add siswaIbadahHarian with nilai 0 for all siswa in kelas_id
        $siswas = Siswa::whereIn('sub_kelas_id', $sub_kelas_id)->get();
        foreach ($siswas as $siswa) {
            foreach ($new_ibadah_harian_id as $value) {
                $siswaIbadahHarian = new SiswaIbadahHarian;
                $siswaIbadahHarian->siswa_id = $siswa->id;
                $siswaIbadahHarian->ibadah_harian_1_id = $value;
                $siswaIbadahHarian->profil_sekolah_id = 1;
                $siswaIbadahHarian->periode_id = $semester;
                $siswaIbadahHarian->rapor_siswa_id = 1;
                $siswaIbadahHarian->penilaian_deskripsi_id = 5;
                if ($siswaIbadahHarian->save()) {
                    $berhasil++;
                }
                $processed++;
            }
        }

        if ($berhasil > 0 && $berhasil == $processed) {
            return response()->json(['success' => 'Data berhasil disimpan!', 'status' => '200']);
        } else {
            return response()->json(['error' => 'Data gagal disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IbadahHarian  $ibadahHarian
     * @return \Illuminate\Http\Response
     */
    public function show(IbadahHarian1 $dataIbadahHarian)
    {
        $data_ibadah_harian = IbadahHarian1::with('kelas','periode','guru')->where('id', $dataIbadahHarian->id)->first();
        $data_kelas = Kelas::all()->except(7);
        $data_guru = Guru::all();
        $data_periode = Periode::all();
        return view('dataIbadahHarian.showIbadahHarian', compact('data_ibadah_harian', 'data_kelas', 'data_guru', 'data_periode'));
        //return response()->json($data_ibadah_harian);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\IbadahHarian  $ibadahHarian
     * @return \Illuminate\Http\Response
     */
    public function edit(IbadahHarian $ibadahHarian)
    {
        //
    }

    public function update(IbadahHarian1 $dataIbadahHarian, UpdateIbadahHarianRequest $request)
    {
        $semester = Periode::where('status', 'aktif')->first()->id;
        $validator_rules = [];
        if ($dataIbadahHarian->kelas_id != $request->kelas_id) {
            $validator_rules['nama_kriteria'] = 'required|unique:ibadah_harians_1,nama_kriteria,' . $dataIbadahHarian->id . ',id,kelas_id,' . $request->kelas_id;
        }
        elseif ($dataIbadahHarian->nama_kriteria != $request->nama_kriteria) {
            $validator_rules['nama_kriteria'] = 'required|unique:ibadah_harians_1,nama_kriteria,' . $dataIbadahHarian->id;
        }
        else {
            $validator_rules['nama_kriteria'] = 'required';
        }
        $validator_rules['guru_id'] = 'required';
        $validator_rules['kelas_id'] = 'required';

        $messages = [];
        $messages['nama_kriteria.required'] = 'Nama nilai tidak boleh kosong!';
        $messages['nama_kriteria.unique'] = 'Nama nilai sudah ada di kelas ini!';
        $messages['guru_id.required'] = 'Guru tidak boleh kosong!';
        $messages['kelas_id.required'] = 'Kelas tidak boleh kosong!';

        $request->validate($validator_rules, $messages);

        $dataIbadahHarian->nama_kriteria = $request->nama_kriteria;
        $dataIbadahHarian->guru_id = $request->guru_id;
        
        if($dataIbadahHarian->kelas_id != $request->kelas_id){
            $dataIbadahHarian->kelas_id = $request->kelas_id;
            $siswa_ibadah_harian = SiswaIbadahHarian::where('ibadah_harian_1_id', $dataIbadahHarian->id)->get();
            foreach ($siswa_ibadah_harian as $value) {
                $value->delete();
            }
            $sub_kelas_id = SubKelas::where('kelas_id', $request->kelas_id)->where('periode_id', $semester)->pluck('id')->toArray();
            $siswas = Siswa::whereIn('sub_kelas_id', $sub_kelas_id)->get();
            foreach ($siswas as $siswa) {
                $siswaIbadahHarian = new SiswaIbadahHarian;
                $siswaIbadahHarian->siswa_id = $siswa->id;
                $siswaIbadahHarian->ibadah_harian_1_id = $dataIbadahHarian->id;
                $siswaIbadahHarian->profil_sekolah_id = 1;
                $siswaIbadahHarian->periode_id = $semester;
                $siswaIbadahHarian->rapor_siswa_id = 1;
                $siswaIbadahHarian->penilaian_deskripsi_id = 5; // Nilai -Kosong-
                $siswaIbadahHarian->save();
            }
        }

        try {
            $dataIbadahHarian->save();
            return response()->json(['success' => 'Data berhasil disimpan!', 'status' => '200']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Data gagal disimpan!']);
        }
    }

    /**
     * Update data ibadah harian dari halaman siswa ibadah harian
     *
     * @param  \App\Http\Requests\UpdateIbadahHarianRequest  $request
     * @param  \App\Models\IbadahHarian  $ibadahHarian
     * @return \Illuminate\Http\Response
     */
    public function update_data_ibadah_harian(Request $request)
    {
        //return response()->json($request->all());
        $ibadah_harian_fields = [];
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'ibadah_harian_') !== false || strpos($key, 'delete_') !== false) {
                $ibadah_harian_fields[$key] = $value;
            }
        }

        // Update IbadahHarian if containt ibadah_harian_(id) and delete if containt delete_(id)
        $berhasil = 0;
        $processed = 0;
        foreach ($ibadah_harian_fields as $field => $value) {
            if (strpos($field, 'ibadah_harian_') !== false) {
                $id = str_replace('ibadah_harian_', '', $field);
                $ibadah_harian = IbadahHarian1::find($id);
                $ibadah_harian->nama_kriteria = $value;
                if ($ibadah_harian->save()) {
                    $berhasil++;
                }
                $processed++;
            } else if (strpos($field, 'delete_') !== false) {
                $id = str_replace('delete_', '', $field);
                $ibadah_harian = IbadahHarian1::find($id);
                if ($ibadah_harian->delete()) {
                    $berhasil++;
                }
                $processed++;
            }
        }

        if ($berhasil > 0 && $berhasil == $processed) {
            return response()->json(['success' => 'Data berhasil disimpan!', 'status' => '200']);
        } else {
            return response()->json(['error' => 'Data gagal disimpan!']);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IbadahHarian  $ibadahHarian
     * @return \Illuminate\Http\Response
     */
    public function destroy(IbadahHarian1 $dataIbadahHarian)
    {
        try {
            $dataIbadahHarian->delete();
            return response()->json(['success' => 'Data berhasil dihapus!', 'status' => '200']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Data gagal dihapus!']);
        }
    }

    public function getTable(Request $request){
        if ($request->ajax()) {
            $periode = Periode::where('status','aktif')->first();
            if ($request->kelas_id == null) {
                $data = IbadahHarian1::with('kelas','periode','guru')->where('periode_id',$periode->id)->get();
            } else {
                $data = IbadahHarian1::with('kelas','periode','guru')->where('kelas_id', $request->kelas_id)->where('periode_id',$periode->id)->get();
            }
            
            return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a href="'. route('dataIbadahHarian.show', $row) .'" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Detail" class="btn btn-sm btn-success mx-1 shadow detail"><i class="fas fa-sm fa-fw fa-eye"></i> Detail</a>';
                $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-sm btn-danger mx-1 shadow delete"><i class="fas fa-sm fa-fw fa-trash"></i> Delete</a>';
                
                return $btn;
            })
            ->editColumn('periode', function ($row) {
                return 'Semester '. $row->periode->semester.' ('.$row->periode->tahun_ajaran.')';
            })
            ->rawColumns(['action'])
            ->make(true);
        }
    }
}
