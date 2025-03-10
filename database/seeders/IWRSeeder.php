<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Kelas;
use App\Models\Guru;

class IWRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('ilman_waa_ruuhans')->insert([
        //     'guru_id' => '10',
        //     'pencapaian' => 'Ilman Waa Ruuhan',
        //     'kelas_id' => '1',
        // ]);

        $ilman = [
            'Ilman Waa Ruuhan',
        ];
        
        for ($i=0; $i < count($ilman); $i++) { 
            for ($j=1; $j <= Kelas::count()-1; $j++) { 
                DB::table('ilman_waa_ruuhans')->insert([
                    'pencapaian' => $ilman[$i],
                    'guru_id' => Guru::inRandomOrder()->first()->id,
                    'kelas_id' => $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'periode_id' => '1',
                ]);
            }
        }

    }
}