<?php

namespace Modules\MasterData\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MasterData\Entities\UnitPelayanan;

class UnitPelayananFactory extends Factory
{
    // protected $model = \Modules\MasterData\Entities\UnitPelayanan::class;
    protected $model = UnitPelayanan::class;

    public function definition()
    {
        $unit_id = $this->faker->numerify(['MUP-' . '########']);
        $inisial = $this->faker->text(3);
        $unit_nama = $this->faker->text(10);
        $kepala_unit = $this->faker->firstName() . $this->faker->lastName();
        $jenis_registrasi = $this->faker->randomElement('Rawat Jalan','IGD');
        $is_unit_kiosk = $this->faker->boolean();
        $is_unit_external = $this->faker->boolean();
        $is_pilih_dokter = $this->faker->boolean();
        $icon_dir = $this->faker->text('-');
        $icon_url = $this->faker->text('-');
        $is_aktif = '1';
        $client_id = $this->faker->numerify('CI-' . '########');
        $created_by = $this->faker->userName();
        $updated_by = '-';
        // $created_at = $this->faker->lastName();
        // $updated_at = $this->faker->lastName();
        // $deleted_at = $this->faker->lastName();
        
        return [
            'unit_id' => $unit_id,
            'inisial' => $inisial,
            'unit_nama' => $unit_nama,
            'kepala_unit' => $kepala_unit,
            'jenis_registrasi' => $jenis_registrasi,
            'is_unit_kiosk' => $is_unit_kiosk,
            'is_unit_external' => $is_unit_external,
            'is_pilih_dokter' => $is_pilih_dokter,
            'icon_dir' => $icon_dir,
            'icon_url' => $icon_url,
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

