<template>
    <div class="uk-modal" uk-modal="bg-close:false;" id="cetakanGelangDewasa" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitReport" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">CETAKAN GELANG DEWASA</h5>
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
                            <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">No. RM</label>
                            <div class="uk-form-controls">
                                <input id="no_rm" type="text" name="no_rm" v-model="cetakanGelangDewasa.no_rm" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="No. RM" required>
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
        name : 'cetakan-gelang-dewasa', 
        components : {
            headerPage,
            BaseRow,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                htmlContent: "",
                isUpdate : true,
                isLoading : false,
                params: [],
                cetakanGelangDewasa: [],
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('cetakan',['dataGelangDewasa']),
            ...mapMutations(['CLR_ERRORS']),

            closeModalReport(){
                this.$emit('closed',true);
                UIkit.modal('#cetakanGelangDewasa').hide();
            },

            filterReport(){
                this.isUpdate = false;
                UIkit.modal('#cetakanGelangDewasa').show();
            },

            cetakData(){
                // Parameter Isi Disini
                this.params = [
                    {
                        'no_rm': this.cetakanGelangDewasa.no_rm,
                    }
                ];
                // Tempat untuk mengirim parameter
                this.dataGelangDewasa(this.params).then((response) => {
                    var newNode = document.createElement('p');
                    newNode.appendChild(document.createTextNode('html string'));
                    this.printDiv(response,"1.0in 11.0in");
                });
            },

            getDateFormated(curDate){
                const today = new Date(curDate);
                const yyyy = today.getFullYear();
                let mm = today.getMonth() + 1; // Months start at 0!
                let dd = today.getDate();

                if (dd < 10) dd = '0' + dd;
                if (mm < 10) mm = '0' + mm;

                const formattedToday = dd + '/' + mm + '/' + yyyy;
                return formattedToday;
            },

            printDiv(pdf_body, paperSize) {
                const customPaperSize = paperSize;
                let frame1 = document.createElement('iframe');
                frame1.name = "frame1";
                frame1.style.position = "absolute";
                frame1.style.top = "-1000000px";
                document.body.appendChild(frame1);
                let frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
                frameDoc.document.open();

                // Set custom paper size using CSS @page rule
                const customStyles = `
                    <style>
                        @page {
                            size: ${customPaperSize};
                            margin: 0;
                        }
                        .page-number {
                            font-size:9px;
                            position: absolute;
                            bottom: 10px;
                            right: 10px;
                            color: white;
                        }
                        .left-text {
                            font-size:9px;
                            position: absolute;
                            bottom: 10px;
                            left: 10px;
                            color: white;
                        }
                    </style>
                `;

                // Append page number to each page
                const pages = pdf_body.split('<div class="page-break"></div>'); // Assuming you have a class for page breaks
                let fullHtml = customStyles;
                var Currentdate = this.getDateFormated(new Date());
                for (let i = 0; i < pages.length; i++) {
                    const pageNumber = i + 1;
                    const pageHtml = pages[i];
                    const pageWithNumber = `
                        <div class="page-container">
                            ${pageHtml}
                            <div class="left-text">${Currentdate}</div>
                            <div class="page-number">Page ${pageNumber} of ${pages.length}</div>
                        </div>
                    `;
                    fullHtml += pageWithNumber;
                    if (i < pages.length - 1) {
                        fullHtml += '<div class="page-break"></div>';
                    }
                }

                frameDoc.document.write(fullHtml);
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