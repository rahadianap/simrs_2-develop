<template>
    <div>
        <!-- <div class="uk-grid uk-grid-small" style="padding:0;margin:0;"> -->
            <!-- <h4 class="uk-width-expand@m" style="padding:0.2em;margin:0;font-weight: 500;">KATEGORI & STATUS ASET</h4> -->
            <header-page 
                title="KATEGORI & STATUS" 
                subTitle="master data kategori & status asset">
            </header-page>
            <Dialog 
                ref="dialog"
                :show="showDialog"
                :cancel="cancel"
                :confirm="confirm"
                v-on:cancel="refreshTable"
                v-on:confirm="removeDataGroup"
                title="Konfirmasi Hapus"
                :description="`Anda akan menghapus permanen data ID  ${selectedRow?.value} dengan nama referensi ${selectedRow?.text}. Lanjutkan ?`" 
            >
            </Dialog>
        <!-- </div> -->
        <div style="padding:3em 0 1em 0;">
            <div style="padding:0;border-radius:10px;min-height:400px;">
                <!-- <div> -->
                    <ul uk-accordion="multiple:false" class="ref-accordion">
                        <li class="uk-open" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Data Kategori</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <table class="uk-table uk-table-striped">
                                    <thead>
                                        <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:100px;">NAMA KATEGORI</th>
                                        <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;">KETERANGAN</th>
                                        <th v-if="allowEdit" style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;text-align:center;">AKSI</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;background-color: #ffffff7d;">
                                                <input class="uk-input uk-form-small uk-text-uppercase" type="text" style="border-radius:5px" v-model="refDataGroup.value" required>
                                            </td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;background-color: #ffffff7d;">
                                                <input class="uk-input uk-form-small" type="text" style="border-radius:5px" v-model="refDataGroup.text" required>
                                            </td>
                                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;background-color: #ffffff7d;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataGroup" style="padding:0;margin:0;color:#cc0202;display:inline-block;"></a>
                                            </td>
                                        </tr>
                                        <tr v-for="dt in refAsetGroup.ref_data">
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;background-color: #ffffff7d;">{{dt.value}}</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;background-color: #ffffff7d;">{{dt.text}}</td>
                                            <td  v-if="allowEdit" style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;background-color: #ffffff7d;">
                                                <a href="#" uk-tooltip="Hapus Data" class="boxedicon" @click.prevent="removeDataGroup(dt)" style="padding:0;margin:0;display:inline-block;">
                                                    <img 
                                                        :src="iconTrash" 
                                                        alt="iconTrash" 
                                                        :show="showDialog"
                                                        :cancel="cancel"
                                                        :confirm="confirm"
                                                        style="max-width:auto;width:16px;height:14px;" 
                                                    />
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    </ul>
                    <ul uk-accordion="multiple:false" class="ref-accordion">
                        <li class="uk-open" style="margin:0;padding:0;">
                            <a class="uk-accordion-title ref-accordion-title uk-box-shadow-small" href="#" >Data Status</a>
                            <div class="uk-accordion-content ref-accordion-content">                                            
                                <table class="uk-table uk-table-striped">
                                    <thead>
                                        <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:100px;">NAMA STATUS</th>
                                        <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;">KETERANGAN</th>
                                        <th v-if="allowEdit" style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;text-align:center;">AKSI</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;background-color: #ffffff7d;">
                                                <input class="uk-input uk-form-small uk-text-uppercase" type="text" style="border-radius:5px" v-model="refDataStatus.value" required>
                                            </td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;background-color: #ffffff7d;">
                                                <input class="uk-input uk-form-small" type="text" style="border-radius:5px" v-model="refDataStatus.text" required>
                                            </td>
                                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;background-color: #ffffff7d;">
                                                <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataStatus" style="padding:0;margin:0;color:#cc0202;display:inline-block;"></a>
                                            </td>
                                        </tr>
                                        <tr v-for="dt in refAsetStatus.ref_data">
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;background-color: #ffffff7d;">{{dt.value}}</td>
                                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;background-color: #ffffff7d;">{{dt.text}}</td>
                                            <td  v-if="allowEdit" style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;background-color: #ffffff7d;">
                                                <a href="#" uk-tooltip="Hapus Data" class="boxedicon" @click.prevent="removeDataStatus(dt)" style="padding:0;margin:0;display:inline-block;">
                                                    <img 
                                                        :src="iconTrash" 
                                                        alt="iconTrash" 
                                                        style="max-width:auto;width:16px;height:14px;" 
                                                    />
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </li>
                    </ul>
            </div>
        </div>
        <!-- <table class="uk-table uk-table-striped">
            <thead>
                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:100px;">VALUE</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;">TEXT</th>
                <th v-if="allowEdit" style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;text-align:center;">...</th>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refDataGroup.value" required>
                    </td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                        <input class="uk-input uk-form-small" type="text" v-model="refDataGroup.text" required>
                    </td>
                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataGroup" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                    </td>
                </tr>
                <tr v-for="dt in refAsetGroup.ref_data">
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;">{{dt.value}}</td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.text}}</td>
                    <td  v-if="allowEdit" style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeDataGroup(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                    </td>
                </tr>
            </tbody>
        </table> -->
<!-- 
        <div class="uk-grid uk-grid-small" style="padding:0;margin:0;">
            <h1 class="uk-width-expand@m" style="padding:0.2em;margin:0;font-style: italic; font-size:11px;">STATUS ASET</h1>
        </div> -->
        <!-- <table class="uk-table uk-table-striped">
            <thead>
                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;max-width:100px;">VALUE</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;">TEXT</th>
                <th v-if="allowEdit" style="padding:0.2em;margin:0;color:black;font-size:12px;font-weight:500;text-align:center;">...</th>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refDataStatus.value" required>
                    </td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                        <input class="uk-input uk-form-small" type="text" v-model="refDataStatus.text" required>
                    </td>
                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addDataStatus" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                    </td>
                </tr>
                <tr v-for="dt in refAsetStatus.ref_data">
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;">{{dt.value}}</td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.text}}</td>
                    <td  v-if="allowEdit" style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeDataStatus(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                    </td>
                </tr>
            </tbody>
        </table> -->
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import BaseRow from '@/Components/BaseTableAsset/BaseRow.vue';
import Dialog from '@/Components/DialogBox/index.vue';
import iconTrash from '@/Assets/icon-trash.svg';
import iconEdit from '@/Assets/icon-notepencil.svg';

export default {
    name : 'table-kategori-status',
    components: {
        headerPage,
        BaseRow,
        Dialog
    },
    props : {
        allowEdit : { type:Boolean, required:false, default: true }
    },
    data() {
        return {
            showDialog: false,
            iconTrash,
            listRefStatus : {
                ref_id : null,
                ref_nama : null,
                ref_data : []
            },
            listRefGroup : {
                ref_id : null,
                ref_nama : null,
                ref_data : []
            },

            refDataGroup : {
                value : null,
                text : null,
            },

            refDataStatus : {
                value : null,
                text : null,
            },
            selectedRow: null,
        }
    },
    mounted(){
        this.refreshData();
    },

    computed : {
        ...mapGetters('refAset',['refAsetStatus','refAsetGroup']),
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('refAset',['updateRefAset','listRefAsetGroup','listRefAsetStatus']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData() {  
            this.listRefAsetGroup().then((response) => {
                if (response.success == false) {
                    alert('ada kesalahan dalam mengambil referensi group aset');
                }
            });
            
            this.listRefAsetStatus().then((response) => {
                if (response.success == false) {
                    alert('ada kesalahan dalam mengubah referensi group aset');
                }
            });
        },

        addDataGroup() {
            if (!this.refDataGroup.value || !this.refDataGroup.text) {
                alert('Data tidak lengkap.');
                return false;
            } else {
                this.refDataGroup.value = this.refDataGroup.value.toUpperCase();
                let data = JSON.parse(JSON.stringify(this.refAsetGroup));
                data.ref_data.push({
                value: this.refDataGroup.value,
                text: this.refDataGroup.text
                });
                this.submitData(data);
                // Reset input fields
                this.refDataGroup.value = null;
                this.refDataGroup.text = null;
            }
        },

        addDataStatus() {
            if (!this.refDataStatus.value || !this.refDataStatus.text) {
                alert('Data tidak lengkap.');
                return false;
            } else {
                this.refDataStatus.value = this.refDataStatus.value.toUpperCase();
                let data = JSON.parse(JSON.stringify(this.refAsetStatus));
                data.ref_data.push({
                value: this.refDataStatus.value,
                text: this.refDataStatus.text
                });
                this.submitData(data);
                // Reset input fields
                this.refDataStatus.value = null;
                this.refDataStatus.text = null;
            }
        },

         // dialog box
         cancel() {
            console.log('cancel');
            this.showDialog = false;
        },
        confirm() {
            console.log('confirm');
            this.showDialog = false;
        },
        // end dialogbox

        // PopUp DialogBox delete confirmation
        cancel() {
                this.showDialog = false;
            },
            confirm() {
                this.removeDataGroup(this.selectedRow?.value)
                .then((response) => {
                        if (response.success == true) {
                                this.$refs.refAsetGroup.retrieveData();
                                this.showDialog = false;
                        }
                        else { alert(response.message) }
                    });            
        },
        // END PopUp DialogBox delete confirmation

        removeDataGroup(refDataGroup){
            this.CLR_ERRORS();
            this.showDialog = true
            this.selectedRow = refDataGroup
            // if(data) {
            //     if(confirm(`Proses ini akan menghapus data referensi  ${data.value} - ${data.text}. Lanjutkan ?`)){
            //         let dt = JSON.parse(JSON.stringify(this.refAsetGroup));
            //         let dataref = dt.ref_data.filter(function (el) { return el.value !== data.value; }); 
            //         dt.ref_data = dataref;   
            //         this.submitData(dt);         
            //     };
            // }
        },

        removeDataStatus(refDataStatus){
            this.CLR_ERRORS();
            this.showDialog = true
            this.selectedRow = refDataStatus

            // if(data) {
            //     if(confirm(`Proses ini akan menghapus data referensi  ${data.value} - ${data.text}. Lanjutkan ?`)){
            //         let dt = JSON.parse(JSON.stringify(this.refAsetStatus));
            //         let dataref = dt.ref_data.filter(function (el) { return el.value !== data.value; }); 
            //         dt.ref_data = dataref;   
            //         this.submitData(dt);         
            //     };
            // }
        },

        submitData(data) {
            this.updateRefAset(data).then((response) => {
                if (response.success == true) {
                    alert("Data referensi berhasil diubah.");
                    this.refreshData();
                }
                else {
                    alert('ada kesalahan dalam mengubah data');
                }
            })
        },

        // field akan otomatis kehapus saat selesai menambah data
        clearFields() {
            this.refDataGroup.value = null;
            this.refDataGroup.text = null;
            this.refDataStatus.value = null;
            this.refDataStatus.text = null;
        },

    }
}
</script>
<style>
.ref-accordion li {
    padding:0;
    margin:0;
    background-color:white;
    /* background-color: #ffffff7d; */
    border-radius: 5px;
    /* border-top:1px solid #cc0202; */
}
li.uk {
    margin:0;
    padding:0;
}

.ref-accordion li a.ref-accordion-title {
    padding: 1em;
    color: #cc0202;
    font-weight:500;
    font-size:14px;
    margin:0;
    border-radius: 5px;

}

.ref-accordion li.uk-open a.ref-accordion-title {
    color: #fff;
    background-color:#cc0202;
    border-radius: 5px;

}

a.ref-accordion-title {        
    display: block;
    padding:0 0 0 0; 
    font-size:1em; 
    font-weight:500;
    text-transform: uppercase;
    border-radius: 5px;

}

div.ref-accordion-content {
    margin:0;
    padding:0.5em;
    /* background-color:whitesmoke; */
    background-color: #ffffff7d;
    border-radius: 5px;

}
</style>