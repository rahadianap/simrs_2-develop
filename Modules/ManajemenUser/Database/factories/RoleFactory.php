<?php

namespace Modules\ManajemenUser\Database\factories;
use Modules\ManajemenUser\Entities\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\ManajemenUser\Entities\Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => 'CL2022-0002',
            'role_id' => $this->faker->unique()->numerify('CL2022-0002.RL-###'),
            'role_nama' => $this->faker->name(),
            'deskripsi' => $this->faker->text(80),
            'is_multi_dokter' => false,
            'is_ubah_tanggal' => false,
            'is_aktif' => true,
            'created_by' => $this->faker->username(),
        ];
    }
}

