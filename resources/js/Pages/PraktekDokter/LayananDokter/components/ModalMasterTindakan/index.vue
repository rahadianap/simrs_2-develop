<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId">
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">MASTER PEMERIKSAAN</h2>
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div style="background-color:#fff;padding:0.5em;">
                <!-- <div>
                    <form action="" @submit.prevent="submitNewTindakan">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <label style="padding:0;margin:0;font-size:12px;">Nama Pemeriksaan</label>
                                <div>
                                    <input type="text" class="uk-input uk-form-small" v-model="actData.tindakan_nama" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label style="padding:0;margin:0;font-size:12px;">Harga</label>
                                <div>
                                    <input type="number" class="uk-input uk-form-small" v-model="actData.harga" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m" style="padding:1.4em 0.5em 0.5em 0.5em; text-align:left;">
                                <button class="uk-button uk-button-small uk-button-primary" type="submit" style="height:40px;border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Simpan</button>                                        
                            </div>
                        </div>
                    </form>
                </div> -->
                <div style="margin-top:1em;">
                    <form action="" @submit.prevent="retrieveMasterTindakan">
                        <div class="uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <input type="text" class="uk-input uk-form-small" v-model="filterTable.keyword" placeholder="kata pencarian" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;">
                            </div>
                            <div class="uk-width-expand@m">
                                <button type="submit"  style="height:35px;border-radius: 7px;border:none;font-size: 14px; text-transform: none;">Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="uk-width-1-1">
                    <table class="uk-table uk-table-striped">
                        <thead>
                            <tr>
                                <th style="font-size: 14px; font-weight: 500;">ID</th>
                                <th style="font-size: 14px; font-weight: 500;">Nama</th>
                                <th style="font-size: 14px; font-weight: 500;text-align:right;">Harga</th>
                                <th style="text-align:center;">...</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <input :disabled="isUpdate" type="text" class="uk-input uk-form-small" v-model="actData.tindakan_nama" placeholder="nama pemeriksaan" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                                </td>
                                <td style="text-align:right;">
                                    <input type="number" class="uk-input uk-form-small" v-model="actData.harga" style="font-weight:400; background-color: white;color:black;border:1px solid #aaa; border-radius:7px;" required>
                                </td>
                                <td style="text-align:center;">
                                    <button type="button" @click.prevent="submitNewTindakan" style="height:35px;border-radius: 7px;border:none;font-size: 14px; text-transform: none;background: rgba(30, 135, 240, 1);color:white">Simpan</button>                                        
                                </td>
                            </tr>
                            <tr v-if="actLists" v-for="act in actLists.data.data">
                                <td>{{ act.tindakan_id }}</td>
                                <td>{{ act.tindakan_nama }}</td>
                                <td style="text-align:right;">{{ formatCurrency(act.harga) }}</td>
                                <td style="text-align:center;">
                                    <button type="button" @click.prevent="updateTindakan(act)" style="height:35px;border-radius: 7px;border:none;font-size: 14px; text-transform: none;">Ubah</button>                                        
                                    <button type="button" @click.prevent="removeTindakan(act)" style="margin-left:5px; height:35px;border-radius: 7px;border:none;font-size: 14px; text-transform: none;">Hapus</button>                                        
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'modal-master-tindakan',
    data(){
        return {
            isLoading : false,
            modalId : 'modalMasterTindakan',
            isUpdate : false,
            actData : {
                tindakan_id : null,
                tindakan_nama : null,
                harga : null,
                is_aktif : true,
            },

            filterTable : {
                is_aktif : true, 
                keyword : '',
                per_page : 10,
                page : 1,
            }
        }
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['actLists']),
    },

    methods : {
        ...mapActions('praktekDokter',['createAct','updateAct','dataAct','listAct','deleteAct']),
        ...mapMutations(['CLR_ERRORS']),

        initialize(){
            
        },

        formatCurrency(value) {
          let val = (value/1).toFixed(2).replace('.', ',')
          return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        showModal(){
            this.retrieveMasterTindakan();
            UIkit.modal(`#${this.modalId}`).show();
        },

        retrieveMasterTindakan(){
            this.listAct(this.filterTable).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    //this.$refs.formDiagnosa.initialize();
                    console.log(this.actLists.data);
                }
                else {
                    alert(response.message);
                }
            })
        },

        closeModal(){

        },

        submitNewTindakan(){
            if(this.actData.tindakan_nama == null || this.actData.tindakan_nama == '') {
                alert('nama tindakan tidak boleh kosong');
                return false;
            }
            if(this.isUpdate) {
                this.updateAct(this.actData).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.filterTable.keyword = this.actData.tindakan_nama;
                        this.retrieveMasterTindakan();
                        this.clrActData();
                    }
                    else {
                        alert(response.message);
                    }
                })
            }
            else {
                this.createAct(this.actData).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.filterTable.keyword = this.actData.tindakan_nama;
                        this.retrieveMasterTindakan();
                        this.clrActData();
                    }
                    else {
                        alert(response.message);
                    }
                })
            }
            
        },

        updateTindakan(data) {
            if(data) {
                this.clrActData();
                this.actData.tindakan_id = data.tindakan_id;
                this.actData.tindakan_nama = data.tindakan_nama;
                this.actData.harga = data.harga;
                this.actData.is_aktif = data.is_aktif;
                this.isUpdate = true;
            }
        },

        clrActData(){
            this.isUpdate = false;
            this.actData.tindakan_id = null;
            this.actData.tindakan_nama = null;
            this.actData.harga = null;
            this.actData.is_aktif = true;
        },

        removeTindakan(data) {
            if(data) {
                this.deleteAct(data.tindakan_id).then((response) => {
                    this.isLoading = false;
                    if (response.success == true) {
                        this.filterTable.keyword = '';
                        this.retrieveMasterTindakan();
                    }
                    else {
                        alert(response.message);
                    }
                })
            }
        },

    }
}
</script>