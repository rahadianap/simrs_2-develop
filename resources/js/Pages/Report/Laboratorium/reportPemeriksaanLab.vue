<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="reportPemeriksaanLab" style="min-height:50vh;">
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
                        
                    <div class="uk-width-1-1@m uk-grid-small" uk-grid style="padding:0.5em;">                           
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Start Date</label>
                            <div class="uk-form-controls">
                                <input id="start_date_tran_lab" type="date" name="start_date_tran_lab" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="Start Date" required>
                            </div>
                        </div>
                        <div class="uk-width-1-2@m">
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">End Date</label>
                            <div class="uk-form-controls">
                                <input id="end_date_tran_lab" type="date" name="end_date_tran_lab" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="End Date" required>
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    
    export default {
        name : 'report-pemeriksaan-lab', 
        components : {
            headerPage,
            BaseRow,
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
                searchUrl: '/report/pemeriksaan/lab'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('report',['listData', 'pemeriksaanLab']),
            ...mapMutations(['CLR_ERRORS']),

            closeModalReport(){
                this.$emit('closed',true);
                UIkit.modal('#reportPemeriksaanLab').hide();
            },

            filterReport(){
                // this.clearReport();
                this.isUpdate = false;
                UIkit.modal('#reportPemeriksaanLab').show();
            },

            cetakData(){
                this.params = [
                    {
                        'start_date_tran_lab': document.getElementById('start_date_tran_lab').value,
                        'end_date_tran_lab': document.getElementById('end_date_tran_lab').value,
                    }
                ];
                this.pemeriksaanLab(this.params).then((response) => {
                    var newNode = document.createElement('p');
                    newNode.appendChild(document.createTextNode('html string'));
                    this.printDiv(response);
                });
            },

            printDiv(pdf_body) {
                let frame1 = document.createElement('iframe');
                frame1.name = "frame1";
                frame1.style.position = "absolute";
                frame1.style.top = "-1000000px";
                document.body.appendChild(frame1);
                let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
                frameDoc.document.open();
                frameDoc.document.write(pdf_body);
                frameDoc.document.close();
                setTimeout(function () {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                    document.body.removeChild(frame1);
                }, 1000);
                return false;
            },
        }
    }
    </script>