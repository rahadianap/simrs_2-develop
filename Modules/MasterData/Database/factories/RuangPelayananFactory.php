<?php

namespace Modules\MasterData\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MasterData\Entities\RuangPelayanan;

class RuangPelayananFactory extends Factory
{
    // protected $model = \Modules\MasterData\Entities\RuangPelayanan::class;
    protected $model = RuangPelayanan::class;

    public function definition()
    {
        $ruang_id = $this->faker->numerify(['MRP-' . '########']);
        $unit_id = $this->faker->text(3);
        $ruang_nama = $this->faker->text(10);
        $is_utama = $this->faker->firstName() . $this->faker->lastName();
        $kelas_standar = $this->faker->randomElement('Rawat Jalan','IGD');
        $harga_id = $this->faker->boolean();
        $lokasi = $this->faker->boolean();
        $deskripsi = $this->faker->boolean();
        $is_aktif = '1';
        $client_id = $this->faker->numerify('CI-' . '########');
        $created_by = $this->faker->userName();
        $updated_by = '-';
        // $created_at = $this->faker->lastName();
        // $updated_at = $this->faker->lastName();
        // $deleted_at = $this->faker->lastName();
        
        return [
            'ruang_id' => $ruang_id,
            'unit_id' => $unit_id,
            'ruang_nama' => $ruang_nama,
            'is_utama' => $is_utama,
            'kelas_standar' => $kelas_standar,
            'harga_id' => $harga_id,
            'lokasi' => $lokasi,
            'deskripsi' => $deskripsi,
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

