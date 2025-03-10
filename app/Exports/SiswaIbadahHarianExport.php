<?php

namespace App\Exports;

use App\Models\IbadahHarian1;
use App\Models\SiswaIbadahHarian;
use App\Models\Periode;
use App\Models\SubKelas;

use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SiswaIbadahHarianExport implements FromView, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $row_lenght, $column_length;
    private $sub_kelas_id;
    private $judul;
    private $nama_kelas;
    private $wali_kelas;
    private $tahun_ajaran;
    private $semester;
    private $tanggal;
    private $file_identifier;


    public function __construct($sub_kelas_id, $informasi)
    {
        $this->sub_kelas_id = $sub_kelas_id;
        $this->judul = $informasi['judul'];
        $this->nama_kelas = $informasi['nama_kelas'];
        $this->wali_kelas = $informasi['wali_kelas'];
        $this->tahun_ajaran = $informasi['tahun_ajaran'];
        $this->semester = $informasi['semester'];
        $this->tanggal = $informasi['tanggal'];
        $this->file_identifier = $informasi['file_identifier'];
    }

    public function view(): View
    {
        $periode = Periode::where('status','aktif')->first();
        $sub_kelas_id = $this->sub_kelas_id;
        $kelas_id = SubKelas::where('id', $sub_kelas_id)->first()->kelas_id;
        $data_ibadah_harian = IbadahHarian1::where('kelas_id', $kelas_id)->where('periode_id', $periode->id)->get();
        $column_length = count($data_ibadah_harian);
        $this->column_length = $column_length;

        $siswa_d = SiswaIbadahHarian::with('siswa','ibadah_harian_1','penilaian_deskripsi')->where('periode_id',$periode->id)->whereHas('siswa', function ($query) use ($sub_kelas_id) {
            $query->where('sub_kelas_id', $sub_kelas_id);
        })->get();

        
        $nilai_id = [];
        $modified_siswa_d = $siswa_d->groupBy(['siswa_id'])->map(function ($item) use (&$nilai_id) {
            $result = [];
            $result['siswa_id'] = $item[0]->siswa_id;
            $result['nama_siswa'] = $item[0]->siswa->nama_siswa;
            $result['nisn'] = $item[0]->siswa->nisn;
            foreach ($item as $ibadah_harian_siswa) {
                $result[$ibadah_harian_siswa->ibadah_harian_1->nama_kriteria] = $ibadah_harian_siswa->penilaian_deskripsi->deskripsi;
                if (!in_array($ibadah_harian_siswa->ibadah_harian_1->id, $nilai_id)) {
                    array_push($nilai_id, $ibadah_harian_siswa->ibadah_harian_1->id);
                }
            }
            return $result;
        });

        $this->row_lenght = count($modified_siswa_d);

        return view('siswaIbadahHarian.export_excel', [
            'siswa_d' => $modified_siswa_d,
            'judul' => $this->judul,
            'nama_kelas' => $this->nama_kelas,
            'wali_kelas' => $this->wali_kelas,
            'tahun_ajaran' => $this->tahun_ajaran,
            'semester' => $this->semester,
            'tanggal' => $this->tanggal,
            'file_identifier' => $this->file_identifier,
            'column_length' => $this->column_length,
            'nilai_id' => $nilai_id,
        ]);
    }

    //style overflow column
    public function styles(Worksheet $sheet)
    {

        

        $sheet->getStyle('D10:' . $this->getColumnIndex($this->column_length + 3) .'10')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D10:' . $this->getColumnIndex($this->column_length + 3) .'10')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D10:' . $this->getColumnIndex($this->column_length + 3) .'10')->getAlignment()->setVertical('center');
        $sheet->getStyle('D10:' . $this->getColumnIndex($this->column_length + 3) .'10')->getAlignment()->setShrinkToFit(true);
        $sheet->getStyle('A9:' . $this->getColumnIndex($this->column_length + 3) .'10')->getFont()->setBold(true);
        // Set Last Row to Bold
        $sheet->getStyle('A' . ($this->row_lenght + 11) . ':' . $this->getColumnIndex($this->column_length + 3) . ($this->row_lenght + 11))->getFont()->setBold(true);
        // Add border to range
        $sheet->getStyle('A9:' . $this->getColumnIndex($this->column_length + 3) . $this->row_lenght + 11)->getBorders()->getAllBorders()->setBorderStyle('thin');
        
        // Enable worksheet protection
        $sheet->getParent()->getActiveSheet()->getProtection()->setSheet(true);
        //Unprotect nilai cell
        $sheet->getStyle('D11:' . $this->getColumnIndex($this->column_length + 3) . $this->row_lenght + 10)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        //Set D11 to getColumnIndex($this->column_length + 3) . ($this->row_lenght + 10) as dropdown list
        

        //validation rule for nilai cell as integer between 0-100 and not empty only
        $startCell = 'D11'; // Starting cell for validation
        $endCell = $this->getColumnIndex($this->column_length + 3) . ($this->row_lenght + 10); // Ending cell for validation
        $validationRange = $startCell . ':' . $endCell;
        $validation = $sheet->getCell($startCell)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Nilai tidak valid');
        $validation->setError('Nilai harus dipilih dari daftar BT, MT, MB, MK, K');
        $validation->setPromptTitle('Pilih nilai');
        $validation->setPrompt('Pilih nilai dari daftar.'.PHP_EOL.'BT = Belum Terlihat'.PHP_EOL.'MT = Mulai Terlihat'.PHP_EOL.'MB = Mulai Berkembang'.PHP_EOL.'MK = Menjadi Kebiasaan'.PHP_EOL.'K = Kosong');
        $validation->setFormula1('"BT,MT,MB,MK,K"');
        $sheet->setDataValidation($validationRange, $validation);

        

        //A2-A6 Auto width cell
        $sheet->getColumnDimension('A')->setAutoSize(true);

    }

    private function getColumnIndex($index)
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }


}
