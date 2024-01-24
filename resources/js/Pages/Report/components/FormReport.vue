<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="formReport" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitReport" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">DATA REPORT</h5>
                        </div>                                
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModalReport" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-1@s uk-grid-small" uk-grid style="padding:0.5em;">                                 
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Report</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="report.report_name" type="text" placeholder="report" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">URL Report</label>
                            <div class="uk-form-controls">
                                <input class="uk-input uk-form-small" v-model="report.report_url" type="text" placeholder="report" required>
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
    name : 'form-report', 
    data() {
        return {
            isUpdate : true,
            report : {
                client_id:null,
                report_id:null,
                report_name : null,
                report_url : null,
                is_aktif : true,
            },
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
    },
    
    methods : {
        ...mapActions('report',['listData','createData']),
        ...mapMutations(['CLR_ERRORS']),

        closeModalReport(){
            this.$emit('closed',true);
            UIkit.modal('#formReport').hide();
        },

        editReport(id){
            this.clearReport();            
            this.dataReport(id).then((response)=>{
                if (response.success == true) {
                    this.fillReport(response.data);
                    this.isUpdate = true;
                    UIkit.modal('#formReport').show();
                } else {
                    alert(response.message)
                }
            })
        },   

        submitReport(){
            this.CLR_ERRORS();
            if(!this.isUpdate) {
                this.createData(this.report).then((response) => {
                    if (response.success == true) {
                        alert("Report baru berhasil dibuat.");
                        this.clearReport();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                    }
                })
            }    
            else {
                this.updateReport(this.report).then((response) => {
                    if (response.success == true) {
                        alert("Report berhasil diubah.");
                        this.clearReport();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModalReport();
                    }
                })
            }         
        },

        newReport(){
            this.clearReport();
            this.isUpdate = false;
            UIkit.modal('#formReport').show();
        },  

        clearReport(){
            this.report.client_id = null;
            this.report.report_id = null;
            this.report.report_name = null;
            this.report.report_url = null;
            this.report.is_aktif = true;
        },

        fillReport(data){
            this.report.client_id = null;
            this.report.report_id = data.report_id;
            this.report.report_name = data.report_name;
            this.report.report_url = data.report_url;
            this.report.is_aktif = data.is_aktif;
        },
    }
}
</script>

<style>
</style>