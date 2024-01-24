<?php

namespace Modules\MasterData\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\MasterData\Entities\TemplateMaster;
use DB;

class TemplateMasterDataTableSeeder extends Seeder
{
    protected $clientId = 'CL2022-0002';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Model::unguard();
        // $this->call("OthersTableSeeder");
        DB::connection('dbcentral')->table('tb_template_master')->insert([
            ['template_id'=>'TMD-CL2022-0001-001','template_nama'=>'MASTER DATA COA','deskripsi'=>'Format excel master data COA','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
            ['template_id'=>'TMD-CL2022-0001-002','template_nama'=>'MASTER DATA UNIT','deskripsi'=>'Format excel master data unit','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
            ['template_id'=>'TMD-CL2022-0001-003','template_nama'=>'MASTER DATA PENJAMIN','deskripsi'=>'Format excel master data penjamin','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
            ['template_id'=>'TMD-CL2022-0001-004','template_nama'=>'MASTER DATA SEDIAAN','deskripsi'=>'Format excel master data sediaan','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
            ['template_id'=>'TMD-CL2022-0001-005','template_nama'=>'MASTER DATA SIGNA','deskripsi'=>'Format excel master data signa','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
            ['template_id'=>'TMD-CL2022-0001-006','template_nama'=>'MASTER DATA SATUAN','deskripsi'=>'Format excel master data satuan','is_aktif'=>true,'client_id'=>$this->clientId,'created_by'=>'Admin'],
        ]);
    }
}
