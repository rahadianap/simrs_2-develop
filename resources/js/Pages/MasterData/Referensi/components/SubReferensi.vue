<template>
    <div>
        <div class="uk-grid uk-grid-small" style="padding:0;margin:0;">
            <p class="uk-width-expand@m" style="padding:0.2em;margin:0;font-style: italic; font-size:11px;">{{listData.deskripsi}}</p>
        </div>
        <table class="uk-table uk-table-striped">
            <thead>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;max-width:100px;">VALUE</th>
                <th style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;">TEXT</th>
                <th v-if="allowEdit" style="padding:0.2em;margin:0;color:black;font-size:14px;font-weight:500;text-align:center;">...</th>
            </thead>
            <tbody>
                <tr v-if="allowEdit">
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;">
                        <input class="uk-input uk-form-small uk-text-uppercase" type="text" v-model="refData.value" required>
                    </td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">
                        <input class="uk-input uk-form-small" type="text" v-model="refData.text" required>
                    </td>
                    <td style="padding:1em;margin:0;color:black;font-size:12px;text-align:center;">
                        <a href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="addData" style="padding:0;margin:0;color:blue;display:inline-block;"></a>
                    </td>
                </tr>
                <tr v-for="dt in listData.json_data" :key="dt in listData">
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;max-width:100px;font-weight:500;">{{dt.value}}</td>
                    <td style="padding:0.5em;margin:0;color:black;font-size:12px;">{{dt.text}}</td>
                    <td  v-if="allowEdit" style="padding:0.5em;margin:0;color:black;font-size:12px;text-align:center;">
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
    name : 'sub-referensi',
    props : {
        allowEdit : { type:Boolean, required:false, default: true }
    },
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
            }
        }
    },
    mounted(){
        this.refreshData();
    },

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('referensi'),
    },
    methods : {
        ...mapActions('referensi',['updateReferensi','listReferensi']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData(data) {            
            if(data) {
                this.listData.ref_id = data.ref_id;
                this.listData.ref_nama = data.ref_nama;
                this.listData.deskripsi = data.deskripsi;
                this.listData.json_data = data.json_data;
            }
        },

        // addData() {
        //     if (this.refData.value == null || this.refData.value == '' || this.refData.text == null || this.refData.text == '') {
        //         alert('Data tidak lengkap.');
        //         return false;
        //     } else {
        //         this.refData.value = this.refData.value.toUpperCase();
        //         let data = JSON.parse(JSON.stringify(this.listData));
        //         data.json_data.push(this.refData);

        //         this.listData = { ...this.listData, json_data: data.json_data };
                
        //         this.submitData(data);
        //     }
        // },

        addData() {
            if (this.refData.value == null || this.refData.value == '' || this.refData.text == null || this.refData.text == '') {
                alert('Data tidak lengkap.');
                return false;
            } else {
                this.refData.value = this.refData.value.toUpperCase();

                if (!Array.isArray(this.listData.json_data)) {
                    this.listData.json_data = [];
                }

                let data = JSON.parse(JSON.stringify(this.listData));
                data.json_data.push(this.refData);

                this.listData = { ...this.listData, json_data: data.json_data };

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

        // submitData(data) {
        //     this.updateReferensi(data).then((response) => {
        //         if (response.success == true) {
        //             alert("Data referensi berhasil diubah.");
        //             this.refData.value= null;
        //             this.refData.text = null;
        //             //this.listData.json_data = response.data.json_data;
        //             this.retrieveReferensi();
        //         }
        //         else {
        //             alert('ada kesalahan dalam mengubah data');
        //         }
        //     })
        // },

        submitData(data) {
            this.updateReferensi(data).then((response) => {
                if (response.success == true) {
                    //console.log(response.data.json_data);
                    alert("Data referensi berhasil diubah.");
                    this.refData.value= null;
                    this.refData.text = null;

                    this.listData = { ...this.listData, json_data: response.data.json_data };
                    this.retrieveReferensi();
                } else {
                    alert('ada kesalahan dalam mengubah data');
                }
            });
        },

        retrieveReferensi(){
            this.listReferensi().then((response)=>{});
        }
    }
}
</script>