<template>
    <div>
        <div class="uk-grid uk-grid-small" style="padding:0;margin:0;">
            <p class="uk-width-expand@m" style="padding:0.2em;margin:0;font-style: italic; font-size:11px;">{{listData.deskripsi}}</p>
            <!-- <button class="uk-width-auto@m" type="button" uk-toggle="target: #rowAddAgama" style="padding:5px;background-color:#cc0202;color:white;cursor:pointer;border:none;border-radius:5px;">
                <span uk-icon="icon:plus-circle;"></span> Data Baru
            </button> -->
        </div>
        <table class="uk-table uk-table-striped">
            <thead>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;max-width:80px;">VALUE</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;">TEXT</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;">MENINGGAL</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;text-align:center;">...</th>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;">
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refData.value" required>
                        </div>                        
                    </td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                        <div class="uk-form-controls">
                            <input class="uk-input uk-form-small" type="text" v-model="refData.text" required>
                        </div>                        
                    </td>
                    <td  style="padding:1em 0.5em 0.5em 0.5em;margin:0;color:black;font-size:12px;">
                        <div class="uk-form-controls">
                            <label style="color:black; font-size:14px;">
                                <input class="uk-checkbox" type="checkbox" v-model="refData.is_meninggal"> Meninggal
                            </label>
                        </div>
                    </td>
                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                    </td>
                </tr>
                <tr v-for="dt in listData.json_data">
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:80px;font-weight:500;">{{dt.value}}</td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.text}}</td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                        <input class="uk-checkbox" type="checkbox" v-model="dt.is_meninggal" disabled>
                    </td>                    
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeData(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'sub-ref-status-pulang',
    data() {
        return {
            listData : {
                ref_id : null,
                ref_nama : null,
                deskripsi : null,
                json_data : []
            },
            refData : {
                value : null,
                text : null,
                is_meninggal : false,
            }
        }
    },
    mounted(){
        this.refreshData();
    },

    computed : {
        ...mapGetters(['errors']),
    },
    methods : {
        ...mapActions('referensi',['updateReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData(data) {            
            if(data) {
                this.listData.ref_id = data.ref_id;
                this.listData.ref_nama = data.ref_nama;
                this.listData.deskripsi = data.deskripsi;
                this.listData.json_data = data.json_data;
            }
        },

        addData(){
            if(this.refData.value == null || this.refData.value == '' || this.refData.text == null || this.refData.text == ''  ){
                alert('Data tidak lengkap.');
                return false;
            }
            else {
                this.refData.value = this.refData.value.toUpperCase(); 
                let data = JSON.parse(JSON.stringify(this.listData));
                data.json_data.push(this.refData);
                this.submitData(data);
            }
        },

        removeData(data){
            if(data) {
                if(confirm(`Proses ini akan menghapus data referensi  ${data.value} - ${data.text}. Lanjutkan ?`)){
                    let dt = JSON.parse(JSON.stringify(this.listData));
                    let dataref = dt.json_data.filter(function (el) { return el.value !== data.value; }); 
                    dt.json_data = dataref;   
                    this.submitData(dt);         
                };
            }
        },

        submitData(data) {
            this.updateReferensi(data).then((response) => {
                if (response.success == true) {
                    alert("Data referensi berhasil diubah.");
                    this.refData.value= null;
                    this.refData.text = null;
                    this.refData.is_meninggal = null;
                    this.listData.json_data = response.data.json_data;
                }
                else {
                    alert('ada kesalahan dalam mengubah data');
                }
            })
        }
    }
}
</script>