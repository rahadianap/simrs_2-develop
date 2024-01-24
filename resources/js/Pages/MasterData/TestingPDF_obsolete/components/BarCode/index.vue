<template>
    <!-- <div>
      <input type="text" v-model="inputValue" @input="generateBarcode" />
      <canvas ref="barcodeCanvas" />
    </div> -->
    <div class="uk-flex-center uk-align-center" style="margin-top:35px;" v-for="index in 2" :key="index" v-if="barCode">
        <div class="uk-margin-small-top">
            <svg :style="{ height: height, width: width }" id="barcodeGenerator" ></svg>
        </div>
        <div>
            <h4 class="value-text" contenteditable @input="updateValue">{{ value }}</h4>
        </div>
    </div>
  </template>
  
<script>
import "./style.css"
import { mapGetters, mapActions, mapMutations } from 'vuex';
import JsBarcode from 'jsbarcode';

export default {
    name: 'barcode',
    props : {
        barCode : { type:Boolean, default:''}, 
        value : { type:String , required:true }, 
        height: {type:String, required:true},
        width: {type:String, required:true},
    },
    data() {
        return {
            value:'RM-000042123532',
            height: '350px',
            width: '5',
        }
    },
    mounted() {
        this.generateBarcode();
    },
    // watch: {
    //     value(newValue) {
    //         this.value = newValue;
    //         this.generateBarcode();
    //     },
    //     width(newWidth) {
    //         this.barcodeWidth = newWidth;
    //         this.generateBarcode();
    //     },
    //     height(newHeight) {
    //         this.barcodeHeight = newHeight;
    //         this.generateBarcode();
    //     },
    // },
    methods: {
        updateValue(event) {
            this.value = event.target.textContent;
            this.generateBarcode();
        },
        generateBarcode() {
            if (this.value.trim() !== '') {
                JsBarcode("#barcodeGenerator", this.value, {
                format: 'CODE128',
                // width: 5,
                width: this.width,
                height: this.height,
                displayValue: false
                });
            }
        },

    }
}
</script>
<style>
/* .underline-text{
    text-align:center;
    border-bottom: 2px solid transparent;
    border-image: linear-gradient(0.25turn, rgb(255, 255, 255),34), rgb(0, 0, 0), rgba(255, 255, 255);
    border-image-slice: 1;
    width:100%;
} */

/* .value-text {
    font-family: cour;
    font-size:35px;
    text-align:center;
    text-decoration:1px underline rgb(20, 20, 20);
    text-underline-offset: 12px;
    letter-spacing:1em;
} */

.value-text {
    font-family: monospace;
    font-size: 40px;
    letter-spacing: 0.2cm;
    text-align:center;
    margin: 1px 0 30px 0px;
    position: relative;
}

</style>