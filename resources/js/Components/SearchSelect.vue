<!--
    search select berfungsi seperti combo box dengan tambahan
    fitur pencarian , yang akan melakukan pengambilan data ke
    array list data yang diambil sebelumnya.
    tanggal : 26 Januari 2021 - Stefanus Agung
    ada kelemahan sekaligus kekuatan : pencarian /pengambilan data terus menerus ke server pada saat input
-->

<template>
    <div :class="config.compClass" ref="containerSearchSelect" :style="config.compStyle" class="mySearchSelectContainer">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">{{label}}</label>
        <div class="uk-form-controls search-select-input">
            <div class="uk-inline uk-width-1-1">
                <input ref="inputSearchSelect" class="uk-input uk-form-small" type="text" v-bind:style="config.compStyle" style="color:black;padding-left:10px;"
                    :disabled = "disabled || config.disabled"
                    :placeholder= "placeholder"
                    :required= "config.required"                     
                    v-model = "searchKeyword"
                    @keydown = "onInputFieldKeydown"
                    @focus = "onFocus"
                    @blur = "onBlur">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: chevron-down"></span>
            </div>                                             
        </div>
        <div ref="dropSection" class="drop-list">
            <div ref="scrollContainer" class="uk-card uk-card-body uk-card-default" style="padding:0; margin:0;max-height:350px;overflow-y:auto;width:100%;z-index:200;">
                <ul class="uk-list">
                    <li ref = "options" 
                        v-for = "(item,index) in items" 
                        :class = "{ selected: index === listindex }" 
                        v-bind:key = "index" 
                        @mousedown = "selectItem(item,index)">                            
                        <p v-for="(cap,capindex) in itemListData" v-bind:key="capindex" class="list-data" :class="cap.isTitle && 'list-title'">{{item[cap.colName]}}</p>                                                                    
                    </li>
                    <li v-if="config.retrieveAll == false">
                        <p style="font-size:11px; font-style:italic;text-align:center;">maksimum setiap pengambilan {{ config.qtyPerPage }} data</p>
                    </li>
                </ul>
            </div>
        </div>                      
    </div>
</template>

<script>
import $axios from '@/Stores/httpReq';
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';
import { debounce } from "debounce";
// import { nextTick } from 'vue';
export default {
    name : 'search-select',
    model : { prop : 'value', event : 'input' },
    props : {
        disabled : { type:Boolean, required:false, default:false },
        config : {
            //column data yang digunakan sebagai text / caption di dropdown
            disabled : { type:Boolean, required:false, default:false },
            //column data yang digunakan sebagai text / caption di dropdown
            required : { type:Boolean, required:false, default:false },
            //component class for container
            compClass : { type:String, required:false, default:'' },
            //component style for container
            compStyle : { type:String, required:false, default:'' },
            //retrieve all item data or by search with maximum 20 item
            retrieveAll : { type : Boolean, required: false ,default:true },
            //maximum item data per page
            qtyPerPage : { type : String, required: false ,default:'20' },
        },
        //text label
        label  : { type : String, required: false, default:null },
        //component placeholder        
        placeholder : { type:String, required:false, default:'' },
        //value data   
        value : { required: false, default : null },
        //URL API untuk pengambilan data
        searchUrl : { type : String, required: true },
        //items value field
        valueField :  { type : String, required: true },
        //items text untuk menampilkan data yang dipilih
        captionField :  { type : String, required: true },
        //column data tambahan untuk ditampilkan sebagai informasi tambahan(Array->bisa lebih dari satu)
        itemListData : { type : Array, required: true },        
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
            params : { per_page : this.config.qtyPerPage, keyword : '', aktif : 1, page: 1},
            //buffer selected item
            dataBuffers : [],
            //indeks data saat melakukan pemilihan pada 
            listindex : -1,
            //final value
            searchKeyword : '',
            dropShow : false,
            numPageData : 0,
            focused : false,
            onInput : false,
            onSearching : false,
            selectedData : null,
        }
    },
    created() {
        if(this.searchUrl !== null && this.searchUrl !== '' ) {
            if(this.config.retrieveAll == true) { this.retrieveAllData(); } 
            else { this.listSearchData(); }
        }
    },
    watch: {
        /**
         * amati setiap perubahan kata pencarian 
         * hanya berjalan saat keyword dituliskan
         */
        'searchKeyword' : function(newVal, oldVal){
            // alert('search keyword');
            if(newVal == '' || newVal == null) {
                this.searchKeyword = null;
                this.listindex = -1;
            }
            else {
                if(newVal !== oldVal) {
                    if(this.focused == true) {
                        this.params.keyword = newVal;
                        if(this.config.retrieveAll == true) { this.listData(); } 
                        else { this.listSearchData(); }
                    }
                }
            }
        },

        /**
         * watch value to update / clear data when 
         * parent update value
         */
  	    'value': function(newVal, oldVal) {
            //alert('value changed');
            if(newVal == '' || newVal == null) {
                this.listindex = -1;
                this.searchKeyword = null;
                this.params.keyword = '';
                this.selectedData = null;
                //alert(`value changed : ${newVal} dan lama ${oldVal}`);
                if(this.focused == false) { 
                    if(this.config.retrieveAll == true) { this.listData(true); }
                    else { this.listSearchData(); }
                }
            }
            else {
                if(this.focused == false && this.selectedData == null) {
                    this.params.keyword = newVal;
                    if(this.config.retrieveAll == true) { this.listData(true); }
                    else { this.listSearchData(true); }
                }
            }
        },

        'searchUrl' : function(newVal,oldVal){
            if(newVal !== oldVal) {
                this.params.keyword = '';
                if(newVal != null && newVal != '') {
                    if(this.config.retrieveAll == true) { this.retrieveAllData(); } 
                    else { this.listSearchData(); }
                }
            }
        },
    },
    
    methods : {
        listSearchData(init){
            this.listindex = -1;
            if(this.searchUrl !== null && this.searchUrl !== '') {
                this.$store.commit('CLR_ERRORS', { root: true });                
                this.params.page = 1;
                $axios.get(this.searchUrl, {params : this.params })
                .then((response) => {
                    if (response.data.success == false) { 
                        alert(response.data.message); 
                    } 
                    else { 
                        this.items = response.data.data.data;
                        this.dataBuffers =this.items;
                        if(init == true) {
                            if(this.items.length == 1) {
                                this.onSearching = false;
                                this.listindex = 0;
                                this.selectedData = this.items[0];
                                this.emitValueChanged();
                            }
                            else {
                                let buff = this.items.filter(item => item[this.valueField] == this.value);
                                this.selectedData = buff[0];
                                this.emitValueChanged();
                            }
                        }
                    }
                })              
                .catch((error) => {
                    console.log(error);
                })
            }
        },

        /**
         * show and hide drop section.
         * event yang digunakan :
         * mouse click
         * enter key press
         */
        toggleDropSection(){
            if(this.dropShow) { this.hideDropList(); } 
            else { this.showDropList(); }
        },
        showDropList() {
            let cWidth = this.$refs.inputSearchSelect.clientWidth;
            this.$refs.dropSection.style.display = "block";
            this.$refs.dropSection.style.width = `${cWidth}px`;
            this.dropShow = true;
        },
        
        hideDropList() {
            this.$refs.dropSection.style.display = "none";
            this.dropShow = false;
        },
        
        onFocus(event){
            event.preventDefault();            
            let that = this;
            window.setTimeout(function(){ that.focusHandler(); },100); 
        },

        focusHandler() {
            this.onSearching = true;
            this.focused = true;
            this.toggleDropSection();
        },
        /**
         * data dipilih saat key enter ditekan
         */
        keyPressEnter(event){
            event.preventDefault();
            this.selectedData = this.items[this.listindex];
            this.focused = false;             
            this.emitValueChanged();
        },        

        onInputFieldKeydown(event){            
            let keyCode = event.keyCode;
            //key tab
            if(keyCode == 9) {
                this.dropShow = true;
                this.focused = false;
                this.selectedData = this.items[this.listindex];
                this.emitValueChanged();
                return true;                
            } 
            //enter key pressed
            else if(keyCode == 13) { this.keyPressEnter(event); }
            //arrow up
            else if(keyCode == 38) {  this.onArrowUp(event); }          
            //arrow down  
            else if(keyCode == 40) {  this.onArrowDown(event); }
        },
        //handle event when user press arrow down
        onArrowDown(ev) {
            ev.preventDefault();
            if (this.listindex < this.items.length-1) { 
                this.listindex = this.listindex + 1;
                this.fixScrolling(ev);
            }
        },
        //handle event when user press arrow up
        onArrowUp(ev) {
            ev.preventDefault();
            if (this.listindex > 0) {
                this.listindex = this.listindex - 1;
                this.fixScrolling();
            }
        },
        //handle scrolling on item when user press key down or up
        fixScrolling(){
            let top = this.$refs.options[this.listindex].offsetTop;
            this.$refs.scrollContainer.scrollTop = top;
        },
        onBlur(event){
            this.onSearching = false;
            this.focused = false;
            let that = this;
            window.setTimeout(function(){ that.blurHandler(); },100);   
        },  

        blurHandler(){
            window.clearTimeout();
            this.selectedData = this.items[this.listindex];
            this.emitValueChanged();
        },
        
        //retrieve data when clicked / enter key on dropdown list
        selectItem(val,index) {      
            this.focused = false;
            this.listindex = index;
            this.selectedData = val;
            this.$emit('input', this.selectedData[this.valueField]);
            this.$emit('item-select', this.selectedData);    
            this.searchKeyword = this.selectedData[this.captionField];
            this.hideDropList();
        },

        //send data to parent
        emitValueChanged() {
            window.clearTimeout();
            let buffData = this.selectedData;
            
            if(buffData) {
                let val = buffData[this.valueField];
                this.$emit('input', val);
                this.$emit('item-select', buffData);   
                this.searchKeyword = buffData[this.captionField];
            } 
            else {
                this.searchKeyword = '';
                this.$emit('input', null);
                this.$emit('item-select', null);   
            }
            this.hideDropList();                    
        },
        
        /**
         * execute when input type event.
         * debounce give time to user finish typing before search to backend  
         * to avoid too many request data to backend
         */
        searchData : debounce(function(e){
            //this.listindex = -1;
            this.showDropList();
            if(this.config.retrieveAll == true) { this.listData(); } 
            else { this.listSearchData(); }
        },50,true),
        

        listData(init) { 
            if(init) {
                this.items = this.dataBuffers.filter(dataBuffer => { return dataBuffer[this.valueField].toLowerCase() == this.params.keyword.toLowerCase() }) 
                if(this.items.length == 1) {
                    this.listindex = 0;
                    this.selectedData = this.items[0];
                    this.searchKeyword = this.selectedData[this.captionField];
                }
                else {
                    this.listindex = -1;
                    this.searchKeyword = null;
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
            if(this.searchUrl !== null && this.searchUrl !== '') {
                let buffer = [];
                this.numPageData = 2;
                
                for(let pageIndex = 1; pageIndex <= this.numPageData; pageIndex++ ){
                    this.params.page = pageIndex;
                    this.params.per_page = 'ALL';
                    await $axios.get(this.searchUrl, {params : this.params })
                    .then((response) => {
                        if (response.data.success == false) { 
                            alert('ada kesalahan dalam proses'); 
                        } 
                        else {
                            this.numPageData = response.data.data.last_page;
                            let datas = response.data.data.data;
                            datas.forEach(function(dt){
                                buffer.push(dt);
                            });
                        }
                    })              
                    .catch((error) => {
                        pageIndex = this.numPageData + 1;
                        console.log(error);
                    })
                }
                this.dataBuffers = buffer;
                this.items = this.dataBuffers;
                this.$emit('ready',true);
            }
            
        },

        clearInputData(){
            this.searchKeyword = null;
            this.selectedData = null;
            this.listindex = -1;
        }
    },
}
</script>

<style scoped>

.mySearchSelectContainer {
    position: relative;
}
.drop-list{
    display:none;
    position: absolute;
    width: 100%;
    max-height: 500px;
    margin-top: 4px;
    overflow-y: auto;
    background: #ffffff;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-radius: 8px;
}

.uk-list li {
    border-bottom:1px solid #f3f3f3;
    padding:0.5em 1em 0.5em 1em;
    margin:0;
    cursor: pointer; 
}
li.selected {
    background-color:#cc0202;
    color: white;
}
.list-data {
    font-weight:100;
    font-size:10px;
    margin:0;
    padding:0;
}
.list-description a {
    color:#333;
}
p.list-title {
    font-weight:500;
    font-size:12px;
    margin:0;
    padding:0;
}
.list-title a {
    color:black;
}
</style>