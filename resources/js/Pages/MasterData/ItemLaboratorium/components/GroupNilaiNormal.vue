<template>
    <div class="uk-container uk-container-small" style="min-height:50vh; padding:0;margin:0;border-radius:5px;">
        <div class="uk-card" style="padding:0;border-radius:5px;">
            <div class="uk-grid uk-grid-small" style="padding:0 0 0.5em 0;margin:0;background-color: !!transparent;">
                <p class="uk-width-expand@m" style="padding:0 0.2em 0.2em 0.2em;margin:0;font-style: italic; font-size:14px;">
                    Group (pengelompokkan) nilai normal berdasar rentang usia
                </p>
            </div>
            <div class="uk-card uk-card-default">
                <table class="uk-table uk-table-striped" style="padding:0;margin:0;">
                    <thead>
                        <th style="padding:1em;margin:0;color:white;font-size:14px;font-weight:400;max-width:100px;background-color:#cc0202;">GROUP</th>
                        <th style="padding:1em;margin:0;color:white;font-size:14px;font-weight:400;background-color:#cc0202;">DESKRIPSI</th>
                        <th style="padding:1em;margin:0;color:white;font-size:14px;font-weight:400;background-color:#cc0202;">MIN.USIA</th>
                        <th style="padding:1em;margin:0;color:white;font-size:14px;font-weight:400;background-color:#cc0202;">MAKS.USIA</th>
                        <th v-if="allowEdit" style="padding:1em;margin:0;color:black;font-size:14px;font-weight:400;text-align:center;background-color:#cc0202;">...</th>
                    </thead>
                    <tbody>
                        <tr v-if="allowEdit">
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                                <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refData.value" required>
                            </td>
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                                <input class="uk-input uk-form-small" type="text" v-model="refData.desc" required>
                            </td>
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;width:100px;">
                                <input class="uk-input uk-form-small" type="number" v-model="refData.usia_min"  style="text-align:center;" min="0">
                            </td>
                            <td style="padding:0.5em;margin:0;color:black;font-size:12px;width:100px;">
                                <input class="uk-input uk-form-small" type="number" v-model="refData.usia_maks" style="text-align:center;" min="0">
                            </td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                            </td>
                        </tr>
                        <tr v-for="dt in listData.json_data">
                            <td style="padding:1em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;">{{dt.value}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;">{{dt.desc}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">{{dt.usia_min}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">{{dt.usia_maks}}</td>
                            <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                                <a href="#" uk-icon="icon:trash;ratio:1;" @click.prevent="removeData(dt)" style="padding:0;margin:0;display:inline-block;"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>            
        </div>

    </div>
</template>
<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'group-nilai-normal',
    props : {
        allowEdit : { type:Boolean, required:false, default: true }
    },
    data() {
        return {
            listData : {
                ref_id : '',
                ref_nama : null,
                deskripsi : null,
                json_data : [],
            },
            refData : {
                value : null,
                desc : null,
                usia_min : null,
                usia_maks : null,
                is_aktif : true,
            }
        }
    },
    mounted(){
        this.refreshData();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refProduk',['groupLabNormalRefs','isRefPenunjangExist']),
    },
    methods : {
        ...mapActions('refPenunjang',['updateRefPenunjang','listRefPenunjang']),
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
            if(this.refData.value == null || this.refData.value == '' || this.refData.desc == null || this.refData.desc == ''){
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
                if(confirm(`Proses ini akan menghapus data referensi  ${data.value} - ${data.desc}. Lanjutkan ?`)) {
                    let dt = JSON.parse(JSON.stringify(this.listData));
                    let dataref = dt.json_data.filter(function (el) { return el.value !== data.value; }); 
                    dt.json_data = dataref;   
                    this.submitData(dt);         
                };
            }
        },

        submitData(data) {
            this.updateRefPenunjang(data).then((response) => {
                if (response.success == true) {
                    alert("Data referensi berhasil diubah.");
                    this.refData.value= null;
                    this.refData.desc = null;
                    this.refData.aktif = true;
                    this.listData.json_data = response.data.json_data;
                    this.listRefPenunjang({per_page:'ALL'});
                }
                else {
                    alert('ada kesalahan dalam mengubah data');
                }
            })
        }
    }
}
</script>