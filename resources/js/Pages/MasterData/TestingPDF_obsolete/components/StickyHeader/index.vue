<template>
    <div class="form uk-container">
        <div class="uk-container uk-flex">
            <div class="uk-width-1-2 uk-flex-start" >
                <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">{{title}}</h4>
                <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">{{subTitle}}</p>
            </div>
            <div class="uk-width-1-2 uk-text-right">
                <input class="submit" type="button" value="Print" @click="printDiv()"> 
            </div>
        </div>
    </div>
</template>
<script>
import "./style.css"
import { mapGetters, mapActions, mapMutations } from 'vuex';
export default {
    name: 'sticky-header',
    props : {
        title : { type:String , required:false, default:'' },
        subTitle : { type:String , required:false, default:'' },    
        div_print : { type:String, default:null, required : false },    
    },
    computed: {

    },
    methods: {
        
        printDiv() {
            
            let pdf_body = this.div_print;
            let contents = document.getElementById(pdf_body).innerHTML;
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write('<html lang="en"><head><title>SIMRS PRINT</title>');
            printWindow.document.write('<link rel="stylesheet" type="text/css" href="http://localhost:8000/uikit/css/uikit.min.css"/>');
            printWindow.document.write('<style>@page { @bottom-center { content: "Page " counter(page) " of " counter(pages); } }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="print-content">'+contents+'</div>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            setTimeout(function () {
                printWindow.print();
                printWindow.close();
            }, 1500);
            return false;

        },
    },
}


</script>