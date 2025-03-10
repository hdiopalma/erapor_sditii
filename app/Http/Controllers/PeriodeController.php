<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Http\Requests\StorePeriodeRequest;
use App\Http\Requests\UpdatePeriodeRequest;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Utilities\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $periode = Periode::all();
        return view('/periode/indexPeriode', compact('periode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('periode.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePeriodeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePeriodeRequest $request)
    {

        //validate
        $validator = $request->validate([
            'tahun_ajaran' => [ // Tahun ajaran harus unique berdasarkan semester
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('periodes')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->get('semester'));
                })->ignore($request->id)
            ],
            'semester' => ['required', 'numeric', 'between:1,2']
        ],
        [
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi!',
            'tahun_ajaran.regex' => 'Format tahun ajaran tidak sesuai!',
            'tahun_ajaran.unique' => 'Tahun ajaran sudah ada untuk semester ini!',
            'semester.required' => 'Semester harus diisi!',
            'semester.numeric' => 'Semester harus berupa angka!',
            'semester.between' => 'Semester harus bernilai 1 atau 2!'
        ]
    );
        $periode = Periode::create([
            'tahun_ajaran' => $request->get('tahun_ajaran'),
            'semester' => $request->get('semester')
        ]);
        if ($periode) {
            return response()->json(['success' => 'Data berhasil disimpan!']);
        } else {
            return response()->json(['errors' => 'Data gagal disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function show(Periode $dataPeriode)
    {
        return view('/periode/showPeriode', compact('dataPeriode'));
        //return response()->json($dataPeriode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function edit(Periode $periode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePeriodeRequest  $request
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePeriodeRequest $request, Periode $dataPeriode)
    {
        $validator = $request->validate([
            'tahun_ajaran' => [ // Tahun ajaran harus unique berdasarkan semester
                'required',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('periodes')->where(function ($query) use ($request) {
                    return $query->where('semester', $request->get('semester'));
                })->ignore($dataPeriode->id)
            ],
            'semester' => ['required', 'numeric', 'between:1,2'],
            //status must aktif or tidak aktif
            'status' => ['required', 'string', 'max:255', 'in:aktif,tidak aktif']
        ],
        [
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi!',
            'tahun_ajaran.regex' => 'Format tahun ajaran tidak sesuai!',
            'tahun_ajaran.unique' => 'Tahun ajaran sudah ada untuk semester ini!',
            'semester.required' => 'Semester harus diisi!',
            'semester.numeric' => 'Semester harus berupa angka!',
            'semester.between' => 'Semester harus bernilai 1 atau 2!',
            'status.required' => 'Status harus diisi!',
            'status.string' => 'Status harus berupa string!',
            'status.max' => 'Status maksimal 255 karakter!',
            'status.in' => 'Status harus aktif atau tidak aktif!'
        ]
        );

        //dd($request->all());

        $dataPeriode->tahun_ajaran = $request->get('tahun_ajaran');
        $dataPeriode->semester = $request->get('semester');
        $dataPeriode->status = $request->get('status');
        $dataPeriode->save();
        //set all status to tidak aktif
        if ($dataPeriode->status == 'aktif') {
            $periode = Periode::where('id', '!=', $dataPeriode->id)->update(['status' => 'tidak aktif']);
        }

        if ($dataPeriode) {

            return response()->json(['success' => 'Data berhasil disimpan!']);
        } else {
            return response()->json(['errors' => 'Data gagal disimpan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Periode  $periode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Periode $periode)
    {
        //
    }

    public function getTable(Request $request){
        if ($request->ajax()) {
            $data = Periode::all();
            return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<a href="'. route('dataPeriode.show', $row) .'" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Detail" class="btn btn-sm btn-success mx-1 shadow detail"><i class="fas fa-sm fa-fw fa-eye"></i> Detail</a>';
                $btn .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-sm btn-danger mx-1 shadow delete"><i class="fas fa-sm fa-fw fa-trash"></i> Delete</a>';
                return $btn;
            })
            //modify status column
            ->editColumn('status', function ($row) {
                if ($row->status == 'aktif') {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
        }
    }
}
