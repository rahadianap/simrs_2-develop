<template>
    <div ref="modalMasterPasien" class="uk-modal uk-modal-container" uk-modal="bg-close:false;" :id="modalId" style="padding:1em;">
        <div class="uk-modal-dialog uk-modal-body uk-border-rounded">
            <div class="uk-grid uk-text-center">
                <header-page 
                    title="REFERENSI DOKTER"
                    style="text-align: center;margin-left:10px"
                    class="uk-width-expand@m"
                ></header-page>
                <!-- <div style="margin-top:2em; text-align:right; ">
                    <button class="uk-button uk-modal-close" @click="closeModal" style="height:40px; border-radius:7px; border:1px solid #666;">Tutup</button>
                </div> -->
                <div class="uk-width-auto" uk-tooltip="title:Tutup;pos: bottom-left;" style="cursor:pointer">
                    <font-awesome-icon @click="closeModal" :icon="['fas', 'xmark']" size="xl" style="color: #d4d4d4;"/>
                </div>
            </div>
            <hr>

        <!-- <div class="uk-grid uk-grid-small" style="padding:0;margin:0;">
            <p class="uk-width-expand@m" style="padding:0.2em;margin:0;font-style: italic; font-size:11px;">{{listData.deskripsi}}</p>
        </div>-->
            <div style="background-color:white;">
                <table class="uk-table uk-table-striped">
                    <thead>
                        <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;max-width:100px;">ID</th>
                        <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;">NAMA DOKTER</th>
                        <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;text-align:center;">...</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                                <input class="uk-input uk-text-uppercase" type="text" v-model="refData.dokter_id" disabled style="border-radius:7px;">
                            </td>
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                <input class="uk-input" type="text" v-model="refData.dokter_nama" required style="border-radius:7px;">
                            </td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                <a href="#" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;">
                                    <span uk-icon="icon:plus-circle;ratio:1;"></span> Simpan
                                </a>
                            </td>
                        </tr>
                        <tr v-if="isLoading">
                            <td style="text-align: center; background-color: #fff;padding:1em;" :colspan="4"> 
                                <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                    <span uk-spinner></span>
                                    sedang mengambil data
                                </p>
                            </td>
                        </tr>
                        <!-- <tr v-else-if="doctorLists.data && doctorLists.data.length > 0" v-for="dt in doctorLists.data" :key="dt in doctorLists"> -->
                        <tr v-else-if="doctorLists.data" v-for="dt in doctorLists.data" :key="dt in doctorLists">
                            <td style="padding:1em;margin:0;color:black;font-size:14px;max-width:100px;font-weight:500;">{{dt.dokter_id}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:14px;">{{dt.dokter_nama}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:14px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeData(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                                <a href="#" uk-icon="icon:file-edit;ratio:1;" @click.prevent="editData(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="hims-table-margin" style="margin-top:1em; background-color: transparent;padding:0;">
                <full-pagination 
                    ref="pagination"
                    :pagination="pagination"
                    :isLoading="isLoading" 
                    v-on:numRowsChange="updateNumRowChange"
                    v-on:pageChange="pageChange"
                >
                </full-pagination>
            </div>

       
            
        </div>        
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import FullPagination from '@/Pages/PraktekDokter/components/BaseTablePraktek/FullPagination.vue';


export default {
    name : 'modal-dokter',    
    components : {
        headerPage,
        FullPagination
    },

    data() {
        return {
            isUpdate : false,
            isLoading : true,
            pagination : { current_page :0,per_page:10,total_data:0,num_pages:0, },
            modalId : 'modalDokterPraktek',
            listData : {
                ref_id : null,
                ref_nama : null,
                deskripsi : null,
                json_data : []
            },
            refData : {
                dokter_id : null,
                dokter_nama : null,
            },
            dokterList : null,
        }
    },
    mounted(){
        this.refreshData();
        this.initialize();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('praktekDokter',['doctorLists']),
    },
    methods : {
        ...mapActions('referensi',['updateReferensi']),
        // ...mapActions('dokter',['listDokter']),
        ...mapActions('praktekDokter',['createDokter','updateDokter','deleteDokter','listDokter']),
        ...mapMutations(['CLR_ERRORS']),

       initialize() {
            this.submitFilter();
      },

      /* START PAGINATION */
      submitFilter() {
        this.isLoading = true;

        this.refData.page = this.pagination.current_page;
        this.refData.per_page = this.pagination.per_page;
        this.listDokter(this.refData).then((response) => {
            if (response.success == true) {
                this.doctorLists = response.data.data;
                this.pagination.total_data = response.data.total;
                this.pagination.per_page = response.data.per_page;
                this.pagination.current_page = response.data.current_page;
                this.pagination.last_page = response.data.last_page;
                this.pagination.num_pages = response.data.num_pages;
                this.$refs.pagination.createPagination();
            }
            this.isLoading = false;
          });
      },
      updateNumRowChange(num){
        this.pagination.current_page = 0;
        this.pagination.per_page = num;
        this.pagination.num_pages = 0;
        this.pagination.total_data = 0;
        this.pagination.page = 1;
        this.isLoading = true;
        this.submitFilter();
      },
  
      pageChange(index) {
        this.isLoading = true;
        this.pagination.current_page = index;
        this.submitFilter();
      },
      nextIndexClick() {
        this.isLoading = true;
        if (this.pagination.current_page < this.pagination.num_pages) {
            this.pagination.current_page++;
            this.submitFilter();
        }
      },
      prevIndexClick() {
        this.isLoading = true;
        if (this.pagination.current_page > 1) {
            this.pagination.current_page--;
            this.submitFilter();
        }
      },
      //* END PAGINATION *//

        refreshData() {     
            this.clrRefData();      
            // this.retrieveDokter();
            // this.submitFilter();
        },

        clrRefData() {
            this.refData.dokter_id = null;
            this.refData.dokter_nama = null;
        },

        retrieveDokter(){
            let params = {
                is_aktif : true,
                per_page : 'ALL',
            };
            this.listDokter(params).then((response) => {
                if (response.success == true) {
                    // console.log(this.doctorLists.data);
                    this.dokterList = this.doctorLists.data;
                    this.submitFilter();
                }
                this.isLoading = false;
            })
        },

        showModal(){
            this.retrieveDokter();
            UIkit.modal(`#${this.modalId}`).show();
        },

        closeModal(){
            this.retrieveDokter();
            this.$emit('modalClosed',true);
            UIkit.modal(`#${this.modalId}`).hide();
            
        },
        
        addData(){
            if(this.refData.dokter_nama == null || this.refData.dokter_nama == ''  ){
                alert('Data tidak lengkap.');
                return false;
            }
            else {
                this.submitData();
            }
        },

        removeData(data){
            if(data) {
                if(confirm(`Proses ini akan menghapus data dokter  ${data.dokter_id} - ${data.dokter_nama}. Lanjutkan ?`)){
                    this.deleteDokter(data.dokter_id).then((response) => {
                        if (response.success == true) {
                            alert("Data Dokter berhasil dihapus.");
                            this.clrRefData();
                            this.retrieveDokter();
                            this.submitFilter();
                        }
                        else {
                            alert('ada kesalahan dalam menghapus data');
                        }
                    })
                };
            }
        },

        editData(data) {
            this.CLR_ERRORS();
            this.refData.dokter_id = data.dokter_id;
            this.refData.dokter_nama = data.dokter_nama;
            this.isUpdate = true;
        },

        submitData() {
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createDokter(this.refData).then((response) => {
                    if (response.success == true) {
                        alert("Data Dokter berhasil ditambahkan.");
                        this.clrRefData();
                        this.retrieveDokter();
                        this.submitFilter();
                    }
                    else {
                        alert('ada kesalahan dalam menambah data');
                    }
                })
            }
            else {
                this.updateDokter(this.refData).then((response) => {
                    if (response.success == true) {
                        this.clrRefData();
                        this.retrieveDokter();
                        alert("Data Dokter berhasil diubah.");
                    }
                    else {
                        alert('ada kesalahan dalam mengubah data');
                    }
                })
            }            
        }
    }
}
</script>