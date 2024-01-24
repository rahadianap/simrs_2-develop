<template>
    <div class="uk-modal" uk-modal id="formCreateOpname" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitStockOpname" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:0.5em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">STOCK OPNAME BARU</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button hims-button-default uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-2-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Depo*</label>
                            <div class="uk-form-controls">
                                <select class="uk-select uk-form-small" :disabled="isUpdate" v-model="opname.depo_id" required style="border:1px solid #cc0202;color:black;">
                                    <option v-for="dt in depolists.data" :value="dt.depo_id">{{dt.depo_nama}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-3@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Rencana</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="opname.tgl_rencana" type="date" required style="border:1px solid #cc0202;color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai awal*</label>
                            <div class="uk-form-controls">
                                <select class="uk-select uk-form-small" v-model="opname.nilai_awal" style="border:1px solid #cc0202;color:black;" required>
                                    <option value="SISTEM">STOK AWAL SAMA DENGAN SISTEM</option>
                                    <option value="NOL">STOK AWAL NOL (0)</option>
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan*</label>
                            <div class="uk-form-controls">
                                <textarea class="uk-textarea" rows="4" style="font-size:14px;color:black;" v-model="opname.catatan" type="text" required>{{opname.catatan}}</textarea>
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'create-opname', 
    data() {
        return {
            isUpdate : true,
            kelompok : {
                client_id:null,
                kelompok_makanan_id:null,
                kelompok : null,
                catatan:null,
                is_aktif : true,
            },
            opname : {
                client_id : null,
                so_id : null,
                tgl_rencana : null,
                unit_id : null,
                depo_id : null,
                depo_nama : null,
                catatan : null,
                status : 'RENCANA',
                nilai_awal : 'SISTEM',
                is_aktif : true,
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('stockOpname',['depolists']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('stockOpname',['listDepoOpname','dataOpname','createOpname','updateDataOpname']),
        ...mapMutations(['CLR_ERRORS']),

        initialize() {
            this.listDepoOpname({per_page:'ALL'});
        },

        closeModal(){
            this.clearOpname();
            UIkit.modal('#formCreateOpname').hide();
            this.$emit('createClosed',true);
            return false;
        },

        submitStockOpname(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createOpname(this.opname).then((response) => {
                    if (response.success == true) {
                        alert("rencana stock opname berhasil dibuat.");
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }    
            else {
                this.updateDataOpname(this.opname).then((response) => {
                    if (response.success == true) {
                        alert("Rencana opname berhasil diubah.");
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }         
        },

        newOpname(){
            this.clearOpname();
            this.isUpdate = false;
            UIkit.modal('#formCreateOpname').show();
        },  

        clearOpname(){
            this.opname.client_id = null;
            this.opname.so_id = null;
            this.opname.tgl_rencana = null;
            this.opname.unit_id = null;
            this.opname.depo_id = null;
            this.opname.unit_nama = null;
            this.opname.depo_nama = null;
            this.opname.catatan = null;
            this.opname.status = 'RENCANA';
            this.opname.nilai_awal = 'SISTEM';
            this.opname.is_aktif = true;
            this.listDepoOpname({per_page:'ALL'});
        },

        fillOpname(data){
            this.opname.client_id = null;
            this.opname.so_id = data.so_id;
            this.opname.tgl_rencana = data.tgl_rencana;
            this.opname.unit_id = data.unit_id;
            this.opname.depo_id = data.depo_id;
            this.opname.unit_nama = data.unit_nama;
            this.opname.depo_nama = data.depo_nama;
            this.opname.nilai_awal = data.nilai_awal;
            this.opname.catatan = data.catatan;
            this.opname.is_aktif = true;
        },

        editOpname(id){
            this.clearOpname();            
            this.dataOpname(id).then((response)=>{
                if (response.success == true) {
                    this.fillOpname(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formCreateOpname').show();
                } else {
                    alert(response.message);
                }
            })
        }, 
    }
}
</script>

<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
} */
/* 
.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
    font-weight:bold;
} */

/* .hims-button-default {
    background-color: #fff;
    color: #333;
    border:2px solid #ccc;
    border-radius:0;
} */

/* .uk-button {
    border-radius:50px;
    border:2px solid #aaa;
    font-weight:400;
} */
/* 
.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
} */
/* 
.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */

</style>