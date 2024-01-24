<template>
    <div :class="compClass" ref="containerSearchSelect" :style="compStyle">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">{{label}}</label>
        <div class="uk-form-controls">
            <div class="uk-inline uk-width-1-1">
                <a :disabled="disabled" class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: chevron-down"></a>
                <input :disabled="disabled" class="uk-input uk-form-small" type="text" v-bind:value="value" >
            </div>
            <div ref="dropSection" class="dropSection" uk-drop="mode: click" style="margin:0;padding:0;">
                <div ref="scrollContainer" class="uk-card uk-card-body uk-card-default" style="padding:0; margin:0;max-height:200px;overflow-y:auto;width:100%;">
                    <ul class="uk-list">
                        <li ref = "options" 
                            v-for = "(item,index) in items" 
                            :class = "{ selected: index === listindex }" 
                            v-bind:key = "index" style="padding:0.5em;margin:0;" @click.prevent = "selectItem(item,index)">                            
                            <a style="font-size:12px;color:black;line-height:30px;width:100%;" @click.prevent = "selectItem(item,index)">{{item[captionField]}}</a>                                                                    
                        </li>
                    </ul>
                </div>
            </div>    
        </div>
    </div>
</template>
<script>
import $axios from '@/Stores/httpReq';
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';

export default {
    name : 'dropdown-list',
    model : {
        prop : 'value',
        event : 'input'
    },
    props : {
        value : { type : String, required: false, default: null },
        //label untuk input field
        label : { type : String, required: false },
        //component class for container
        compClass : { type : String, required: false },
        //component style for container
        compStyle : { type : String, required: false },
        //component class for container
        placeholder : { type : String, required: false, default:'' },
        //URL API untuk pengambilan data
        searchUrl : { type : String, required: true },
        //items value field
        valueField :  { type : String, required: true },
        //items text untuk menampilkan data yang dipilih
        captionField :  { type : String, required: true },
        // //column data yang digunakan sebagai text / caption di dropdown
        required : { type : Boolean, required: false, default:false },
        disabled : { type : Boolean, required: false, default:false },
    },

    watch: {
        /**
         * watch value to update / clear data when 
         * parent update value
         */
  	    'value': function(newVal, oldVal) {
            
        },
        'searchUrl': function(newVal, oldVal) {
            if(newVal != oldVal) {
                if(newVal != null && newVal !== '') {
                    this.retrieveAllData();
                }
            }
        }
    },

    computed: {
        ...mapGetters(['user','clientId']),
        ...mapState(['errors'])
    },

    data() {
        return {
            //buffering result from fetching data
            items : [],
            //adding parameter on axios fetch data
            params : { per_page : 'ALL', keyword : '', page: 1},
            //buffer selected item
            dataBuffers : [],
            //indeks data saat melakukan pemilihan pada 
            listindex : -1,
            //final value
            finalValue : '',
            //dropShow : false,
            numPageData : 0,
            //focused : false
        }
    },
    created() {
        if(this.searchUrl != null && this.searchUrl != ''){
            this.retrieveAllData();
        }
    },
    methods : {
        valueChange(data){
            let dt = this.items.filter(item => {
                    return item[this.valueField].toUpperCase().includes(data.toUpperCase())
                })
        },

        //retrieve data when clicked / enter key on dropdown list
        selectItem(val,index) {
            if(this.disabled == false) {
                this.$emit('input', val[this.valueField]);
                this.$emit('item-select', val);  
                UIkit.drop('.dropSection').hide(0);
            }
        },

        listData(init) { 
            if(init) {
                this.items = this.dataBuffers.filter(dataBuffer => {
                    return dataBuffer[this.valueField].toLowerCase().includes(this.params.keyword.toLowerCase())
                })
                if(this.items.length == 1) {
                    this.listindex = 0;
                    this.finalValue = this.items[0][this.captionField];
                }
            }
            else {
                this.items = this.dataBuffers.filter(dataBuffer => {
                    return dataBuffer[this.captionField].toLowerCase().includes(this.params.keyword.toLowerCase())
                })
            }
        },

        /**
         * retrieve all data to store on buffer items
         */
        retrieveAllData: async function() {
            this.$store.commit('CLR_ERRORS', { root: true });
            //this.params.client_id = this.clientId;
            let buffer = [];
            //this.numPageData = 2;
            
            //for(let pageIndex = 1; pageIndex <= this.numPageData; pageIndex++ ){
                //this.params.page = pageIndex;
                await $axios.get(this.searchUrl, {params : this.params })
                .then((response) => {
                    if (response.data.success == false) { 
                        alert('ada kesalahan dalam proses'); 
                    } 
                    else {
                        //this.numPageData = response.data.data.last_page;
                        let datas = response.data.data.data;
                        //if(pageIndex == 1) {
                            buffer = datas; 
                        // } else {
                        //     datas.forEach(function(dt){
                        //         buffer.push(dt);
                        //     });
                        // }
                    }
                })              
                .catch((error) => {
                    pageIndex = this.numPageData + 1;
                })
            //}
            this.dataBuffers = buffer;
            this.items = this.dataBuffers;
        },
        
    }
}
</script>

<style scoped>

    .uk-list li {
        border-bottom:1px solid #f3f3f3;
        padding:1em;
        margin:0;
        cursor: pointer; 
    }
    .uk-list li a {
        display:block;
        text-decoration: none;
        width:100%;        
    }
    .uk-list li:hover{
        background:whitesmoke;
    }

    .uk-list li:hover a {
        color:white;
    }

    </style>