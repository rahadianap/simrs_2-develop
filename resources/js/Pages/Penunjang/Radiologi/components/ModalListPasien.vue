<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalListPasien" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <h2 class="uk-modal-title">Pilih Pasien</h2>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <base-table ref="listPasien"
                    :columns = "baseColumns" 
                    :config="configTable" 
                    :urlSearch="searchUrl">
                </base-table>
            </div>
        </div>
    </div>
</template>
<script>
import FormPasien from '@/Pages/MasterData/Pasien/components/FormPasien.vue';
import BaseTable from '@/Components/BaseTable';

export default {
    name : 'modal-list-pasien',
    components : { FormPasien, BaseTable },
    data() {
        return {
            configTable : {                
                isExpandable : false,
                filterType : 'SIMPLE', 
            },
            baseColumns :[],
            tbColumns : [             
                { 
                    name : 'pasien_id', 
                    title : 'ID', 
                    colType : 'link',
                    linkCallback :  this.pasienSelect,
                },   
                { 
                    name : 'no_rm', 
                    title : 'RM', 
                    colType : 'link',
                    linkCallback :  this.pasienSelect,
                },   
                { 
                    name : 'nama_lengkap', 
                    title : 'Nama Pasien', 
                    colType : 'text',
                },   
                { 
                    name : 'tempat_lahir', 
                    title : 'Tempat Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'tgl_lahir', 
                    title : 'Tgl. Lahir', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_kelamin', 
                    title : 'JK', 
                    colType : 'text',
                },   
                { 
                    name : 'jns_penjamin', 
                    title : 'Jenis Penjamin', 
                    colType : 'text',
                },
                { 
                    name : 'is_aktif', 
                    title : 'Aktif', 
                    colType : 'boolean', 
                    isSearchable : false,
                }                
            ], 

            tbPoliColumns : [             
                { name : 'reg_id', title : 'Reg. No', colType : 'link', linkCallback :  this.pasienSelect, },   
                { name : 'unit_id', title : 'Unit', colType : 'link', linkCallback :  this.pasienSelect, },   
                { name : 'pasien_id', title : 'Pasien ID', colType : 'link', linkCallback :  this.pasienSelect, },   
                { name : 'no_rm', title : 'RM', colType : 'link', linkCallback :  this.pasienSelect, },   
                { name : 'nama_lengkap', title : 'Nama Pasien', colType : 'text', },   
                { name : 'tempat_lahir', title : 'Tempat Lahir', colType : 'text', },   
                { name : 'tgl_lahir', title : 'Tgl. Lahir', colType : 'text', },
                { name : 'is_aktif', title : 'Aktif', colType : 'boolean', isSearchable : false, }                
            ], 
            
            urlPasien : '/patients',
            urlPasienPoli : '/patients',
            urlPasienIgd : '/patients',
            urlPasienInap : '/patients',
            urlPasienLuar : '/patients',

            searchUrl : ''
        }
    },

    methods : {
        showPasienList(tipe){
            if(tipe) {
                if(tipe == 'RAWAT_JALAN') { 
                    this.searchUrl = this.urlPasienPoli;
                    this.baseColumns = this.tbPoliColumns;
                    this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else if(tipe == 'MASTER_PASIEN') { 
                    this.searchUrl = this.urlPasien;
                    this.baseColumns = this.tbColumns;
                    this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else if(tipe == 'IGD') { 
                    this.searchUrl = this.urlPasienIgd;
                    this.baseColumns = this.tbPoliColumns;
                    this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else if(tipe == 'RAWAT_INAP') { 
                    this.searchUrl = this.urlPasienInap;
                    this.baseColumns = this.tbPoliColumns;
                    this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else if(tipe == 'PASIEN_LUAR') { 
                    this.searchUrl = this.urlPasienLuar;
                    this.baseColumns = this.tbColumns;
                    this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else if(tipe == 'RAD') { 
                    this.searchUrl = '/registrations/RAD/patients';
                    this.baseColumns = this.tbPoliColumns;
                    // this.$refs.listPasien.retrieveData();
                    UIkit.modal('#modalListPasien').show();
                }
                else { return false; }
            }
        },

        pasienSelect(data){
            this.$emit('listPasienSelected',data);
            UIkit.modal('#modalListPasien').hide();
        }
        
    }
}
</script>