<template>
    <div style="padding:0;margin:0;background-color: whitesmoke;">
        <div class="uk-grid uk-grid-small" style="padding:0 0 0.5em 0;margin:0;background-color: !!transparent;">
            <p class="uk-width-expand@m" style="padding:0 0.2em 0.2em 0.2em;margin:0;font-style: italic; font-size:14px;">
                referensi cara penggunaan obat.    
            </p>
        </div>
        <div class="uk-card uk-card-default">
            <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                <thead>
                    <th style="padding:1em;margin:0;color:white;font-size:14px;font-weight:400;background-color:#cc0202;">LABEL CARA PAKAI</th>
                    <th v-if="allowEdit" style="padding:1em;margin:0;color:black;font-size:14px;font-weight:400;text-align:center;background-color:#cc0202;">...</th>
                </thead>
                <tbody>
                    <tr v-if="allowEdit">
                        <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                            <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refData.value" required>
                        </td>
                        <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                            <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                        </td>
                    </tr>
                    <tr v-for="dt in listData.json_data">
                        <td style="padding:1em;margin:0;color:black;font-size:12px;font-weight:400;">{{dt.value}}</td>
                        <td  v-if="allowEdit" style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;width:80px;">
                            <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeData(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'form-label-obat',
    props : {
        allowEdit : { type:Boolean, required:false, default: true }
    },
    data() {
        return {
            listData : {
                ref_id : '',
                ref_nama : null,
                deskripsi : null,
                json_data : []
            },
            refData : {
                value : null,
                text : null,
            }
        }
    },
    mounted(){
        this.refreshData();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['jenisProdukRefs','isRefProdukExist']),
    },
    methods : {
        ...mapActions('refProduk',['updateRefProduk','listRefProduk']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData(data) {     
            if(data) {
                this.listData.ref_id = data.ref_id;
                this.listData.ref_nama = data.ref_nama;
                this.listData.deskripsi = data.deskripsi;
                if(data.json_data) {
                    this.listData.json_data = data.json_data;
                }
                else {
                    this.listData.json_data = [];                
                }
            }
        },

        addData(){
            if(this.refData.value == null || this.refData.value == ''){
                alert('Data tidak lengkap.');
                return false;
            }
            else {
                this.refData.value = this.refData.value.toUpperCase(); 
                this.refData.text = this.refData.value.toUpperCase(); 
                let data = JSON.parse(JSON.stringify(this.listData));
                data.json_data.push(this.refData);
                this.submitData(data);
            }
        },

        removeData(data){
            if(data) {
                if(confirm(`Proses ini akan menghapus data referensi  ${data.value} - ${data.desc}. Lanjutkan ?`)) {
                    let dt = JSON.parse(JSON.stringify(this.listData));
                    let dataref = dt.json_data.filter(function (el) { return el.value !== data.value; }); 
                    dt.json_data = dataref;   
                    this.submitData(dt);         
                };
            }
        },

        submitData(data) {
            this.updateRefProduk(data).then((response) => {
                if (response.success == true) {
                    alert("Data referensi berhasil diubah.");
                    this.refData.value= null;
                    this.refData.text = null;
                    this.listData.json_data = response.data.json_data;
                    this.listRefProduk({per_page:'ALL'});
                }
                else {
                    alert('ada kesalahan dalam mengubah data');
                }
            })
        }
    }
}
</script>