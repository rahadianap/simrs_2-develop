<?php

namespace Modules\MasterData\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MasterData\Entities\BedPelayanan;

class BedPelayananFactory extends Factory
{
    // protected $model = \Modules\MasterData\Entities\BedPelayanan::class;
    protected $model = BedPelayanan::class;

    public function definition()
    {
        $bed_id = $this->faker->numerify(['MBP-' . '########']);
        $ruang_id = $this->faker->text(3);
        $no_bed = $this->faker->text(10);
        $jns_kelamin_bed = $this->faker->firstName() . $this->faker->lastName();
        $pasien_id = $this->faker->randomElement('Rawat Jalan','IGD');
        $reg_id = $this->faker->boolean();
        $trx_id = $this->faker->boolean();
        $tgl_masuk = $this->faker->boolean();
        $status = $this->faker->text('-');
        $is_tersedia = $this->faker->text('-');
        $tgl_rencana_pulang = $this->faker->text('-');
        $is_aktif = '1';
        $client_id = $this->faker->numerify('CI-' . '########');
        $created_by = $this->faker->userName();
        $updated_by = '-';
        // $created_at = $this->faker->lastName();
        // $updated_at = $this->faker->lastName();
        // $deleted_at = $this->faker->lastName();
        
        return [
            'bed_id' => $bed_id,
            'ruang_id' => $ruang_id,
            'no_bed' => $no_bed,
            'jns_kelamin_bed' => $jns_kelamin_bed,
            'pasien_id' => $pasien_id,
            'reg_id' => $reg_id,
            'trx_id' => $trx_id,
            'tgl_masuk' => $tgl_masuk,
            'status' => $status,
            'is_tersedia' => $is_tersedia,
            'tgl_rencana_pulang' => $tgl_rencana_pulang,
            'is_aktif' => $is_aktif,
            'client_id' => $client_id,
            'created_by' => $created_by,
            'updated_by' => $updated_by,
            // 'created_at' => $created_at,
            // 'updated_at' => $updated_at,
            // 'deleted_at' => $deleted_at,
        ];
    }
}