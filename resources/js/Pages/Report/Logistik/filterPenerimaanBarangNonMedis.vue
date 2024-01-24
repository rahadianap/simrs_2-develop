<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="filterPenerimaanBarangMedis" style="min-height:50vh;">
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
                            <button type="button" @click.prevent="cetakData" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Cetak Data</button>                  
                        </div>
                    </div>
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>                
                        
                    <div class="uk-width-1-2@m uk-grid-small" uk-grid style="padding:0.5em;">                           
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Start Date</label>
                            <div class="uk-form-controls">
                                <input id="start_date2" type="date" name="start_date" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="Start Date" required>
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">End Date</label>
                            <div class="uk-form-controls">
                                <input id="end_date2" type="date" name="end_date" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="End Date" required>
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
        <report-penerimaan-barang-non-medis ref="reportPenerimaanBarangNonMedis"></report-penerimaan-barang-non-medis>
    </div> 
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import reportPenerimaanBarangNonMedis from '@/Pages/Report/Logistik/reportPenerimaanBarangNonMedis.vue';
    
    export default {
        name : 'filter-penerimaan-barang-non-medis', 
        components : {
            headerPage,
            BaseRow,
            reportPenerimaanBarangNonMedis
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isUpdate : true,
                isLoading : false,
                params: [],
                start_date: '',
                end_date: '',
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('report',['listData', 'penerimaanUmum']),
            ...mapMutations(['CLR_ERRORS']),

            closeModalReport(){
                this.$emit('closed',true);
                UIkit.modal('#filterPenerimaanBarangMedis').hide();
            },

            filterReport(){
                // this.clearReport();
                this.isUpdate = false;
                UIkit.modal('#filterPenerimaanBarangMedis').show();
            },

            cetakData(){
                this.params = [
                    {
                        'start_date': document.getElementById('start_date2').value,
                        'end_date': document.getElementById('end_date2').value,
                    }
                ];
                this.penerimaanUmum(this.params).then((response) => {
                    if (response.success == true) {
                        this.$refs.reportPenerimaanBarangNonMedis.generateReport(response.data);
                    }
                    else { alert (response.message) }
                });
            }
        }
    }
    </script>