<!--
    search select berfungsi seperti combo box dengan tambahan
    fitur pencarian , yang akan melakukan pengambilan data ke
    array list data yang diambil sebelumnya.
    tanggal : 26 Januari 2021 - Stefanus Agung
    ada kelemahan sekaligus kekuatan : pencarian /pengambilan data terus menerus ke server pada saat input
-->

<template>
    <div :class="compClass" ref="containerSearchSelect" :style="compStyle" class="mySearchOptionContainer">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">{{label}}</label>
        <div class="uk-form-controls search-select-input">
            <div class="uk-inline uk-width-1-1">
                <input ref="inputSearchOption" 
                    class="uk-input uk-form-small" 
                    type="text" 
                    v-bind:style="compStyle" 
                    style="color:black;padding-left:10px;"
                    :disabled = "disabled"
                    :placeholder= "placeholder"
                    :required= "required"
                    v-model = "keyword"
                    @keydown.enter="keyPressEnter"
                    @keydown.esc="isOpen = false"
                    @keydown = "onInputFieldKeydown"
                    @focus ="isOpen = true"
                    @blur = "onBlur"
                    @input ="debouncedOnChange">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: chevron-down"></span>
            </div>                                             
        </div>
        <div class="uk-form-controls uk-hidden1">
            <!-- <input type="text" v-model="value"> -->
        </div>
        <div ref="dropSection" class="drop-list">
            <div ref="scrollContainer" class="uk-card uk-card-body uk-card-default" style="padding:0; margin:0;max-height:330px;overflow-y:auto;width:100%;z-index:200;">
                <ul class="uk-list">
                    <li ref = "options" 
                        v-for = "(item,index) in items" 
                        :class = "{ selected: index === listindex }" 
                        v-bind:key = "index" 
                        @mousedown = "selectItem(item,index)">                            
                        <p v-for="(cap,capindex) in itemListData" v-bind:key="capindex" class="list-data" :class="cap.isTitle && 'list-title'">{{item[cap.colName]}}</p>                                                                    
                    </li>
                    <li v-if="retrieveAll == false">
                        <p style="font-size:11px; font-style:italic;text-align:center;">maksimum setiap pengambilan {{ qtyPerPage }} data</p>
                    </li>
                </ul>
            </div>
        </div>                      
    </div>
</template>

<script>
import $axios from '@/Stores/httpReq';
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';
import { debounce } from "lodash";
export default {
    name : 'search-option',
    model : { prop : 'value', event : 'input' },
    props : {
        //column data yang digunakan sebagai text / caption di dropdown
        disabled : { type:Boolean, required:false, default:false },
        //column data yang digunakan sebagai text / caption di dropdown
        required : { type:Boolean, required:false, default:false },
        //component class for container
        compClass : { type:String, required:false, default:'' },
        //component style for container
        compStyle : { type:String, required:false, default:'' },
        //maximum item data per page
        qtyPerPage : { type : String, required: false ,default:'10' },
        //retrieve all item data or by search with maximum 20 item
        retrieveAll : { type : Boolean, required: false ,default:true },        
        //text label
        label  : { type : String, required: false, default:null },
        //component placeholder        
        placeholder : { type:String, required:false, default:'' },
        //value data   
        value : { type:String, required: false, default : null },
        //URL API untuk pengambilan data
        searchUrl : { type : String, required: true },
        //nama kolom yang digunakan untuk value
        valueField :  { type : String, required: true },
        //nama kolom dari data yang akan ditampilkan
        captionField :  { type : String, required: true },
        //column data tambahan untuk ditampilkan sebagai informasi tambahan(Array->bisa lebih dari satu)
        itemListData : { type : Array, required: true },        
    },
    computed: {
        ...mapGetters(['user','clientId']),
        ...mapGetters(['errors']),
        ...mapGetters(['optionLists']),

        debouncedOnChange () {
            return debounce(this.onChange, 700);
        }
    },
    data() {
        return {
            //buffer items hasil dari pencarian - yang sesuai yang ditampilkan di list
            items : [],

            //buffering result from fetching data
            dataBuffers : [],
            
            //adding parameter on axios fetch data
            params : { per_page : this.qtyPerPage, keyword : '', aktif : 1, page: 1},
            
            //indeks data saat melakukan pemilihan pada 
            listindex : -1,
            //bind ke text pencarian
            keyword : '',

            dropShow : false,
            numPageData : 0,
            focused : false,
            onInput : false,
            onSearching : false,
            selectedData : null,
            isOpen: false,
            isLoading : false,
        }
    },
    created() {
        // if(this.searchUrl !== null && this.searchUrl !== '' ) {
        //     if(this.retrieveAll == true) { this.retrieveAllData(); } 
        //     else { this.listSearchData(); }
        // }
        this.SET_URL_SEARCH_OPTIONS(this.searchUrl);

    },
    watch: {
        'isOpen' : function(newVal, oldVal){
            if(newVal == true)  {
                this.showDropList();
            }
        },
        /**
         * amati setiap perubahan kata pencarian 
         * hanya berjalan saat keyword dituliskan
         */
        // 'keyword' : function(newVal, oldVal){
        //     if(newVal !== oldVal) {
        //         if(this.focused == true) {
        //             this.params.keyword = newVal;
        //             if(this.retrieveAll == true) { this.listData(); } 
        //             else { this.listSearchData(); }
        //         }
        //     }
        // },

        /**
         * watch value to update / clear data when 
         * parent update value
         */
  	    // 'value': function(newVal, oldVal) {
        //     if(newVal == '' || newVal == null) {
        //         this.keyword = '';
        //         this.params.keyword = '';
        //         this.selectedData = null;
        //         if(this.focused == false) { 
        //             if(this.retrieveAll == true) { 
        //                 this.listData();  
        //             }
        //             else { 
        //                 this.listSearchData(); 
        //             }
        //         }
        //     }
        //     else {
        //         if(this.focused == false && this.selectedData == null) {
        //             this.params.keyword = newVal;
        //             if(this.retrieveAll == true) { this.listData(true); }
        //             else { 
        //                 this.listSearchData(true); 
        //             }
        //         }
        //     }           
        // },

        'value': function(newVal, oldVal) {
            this.valueChanged(newVal);
            // if(newVal == '' || newVal == null) {
            //     this.keyword = '';
            //     this.selectedData = null;
            //     if(this.focused == false) {
            //         this.listSearchData(false);
            //     }
            // }
            // else {
            //     if(this.focused == false && this.selectedData == null) {
            //         this.params.keyword = newVal;
            //         if(this.retrieveAll == true) { this.listData(true); }
            //         else { 
            //             this.listSearchData(true); 
            //         }
            //     }
            // }           
        },

        'searchUrl' : function(newVal,oldVal){
            this.urlSearchChange(newVal);
            // if(newVal !== oldVal) {
            //     console.log(newVal)
            //     // this.params.keyword = '';
            //     // if(newVal != null && newVal != '') {
            //     //     if(this.retrieveAll == true) { this.retrieveAllData(); } 
            //     //     else { this.listSearchData(); }
            //     // }
            //     this.SET_URL_SEARCH_OPTIONS(newVal);
            //     this.CLR_OPTION_LISTS();
            // }
        },
    },
    
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations(['SET_URL_SEARCH_OPTIONS','CLR_OPTION_LISTS']),
        ...mapActions(['searchOptions']),
        
        valueChanged(data){
            console.log(data);
            if(data) {
                if(data.length > 0){
                    this.searchInitialItems();
                }
            }
        },

        urlSearchChange(data) {
            if(data) {
                console.log(data);
                this.SET_URL_SEARCH_OPTIONS(data);
                this.CLR_OPTION_LISTS();
            }
        },

        searchInitialItems(key){
            if(this.searchUrl !== null && this.searchUrl !== ''){
                this.params.keyword = key;
                this.searchOptions(this.params).then((response)=>{
                    if (response.success == true) {
                        let val = response.data.data.find(item => item[this.valueField] == this.value);
                        if(val) { this.keyword = val[this.captionField]; }
                        else { this.$emit('item-select', null); }
                    } 
                    else { alert(response.message); }
                })  
            }
        },

        onChange () {
            this.showDropList();
            if(!this.isLoading) { 
                console.log(this.keyword);
                this.isLoading = true;
                this.params.keyword = this.keyword;
                this.searchOptions(this.params).then((response)=>{
                    if (response.success == true) {
                        this.isLoading = false;
                        this.items = response.data.data;
                    } 
                    else { 
                        this.isLoading = false;
                    }
                })
            } 
        },


        listSearchData(init){
            this.CLR_ERRORS();                
            this.listindex = -1;
            if(this.searchUrl !== null && this.searchUrl !== '') {
                this.params.page = 1;
                this.params.keyword = this.keyword;
                $axios.get(this.searchUrl, {params : this.params })
                .then((response) => {
                    if (response.data.success == false) { 
                        alert(response.data.message); 
                    } 
                    else { 
                        this.items = response.data.data.data;
                        console.log(this.items);
                        this.dataBuffers =this.items;
                        // if(init == true) {
                        //     if(this.items.length == 1) {
                        //         this.onSearching = false;
                        //         this.listindex = 0;
                        //         this.selectedData = this.items[0];
                        //         this.emitValueChanged();
                        //     }
                        //     else {
                        //         let buff = this.items.filter(item => item[this.valueField] == this.value);
                        //         this.selectedData = buff[0];
                        //         this.emitValueChanged();
                        //     }
                        // }
                    }
                })              
                .catch((error) => {
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

        /**menampilkan list hasil pencarian */
        showDropList() {
            //if(this.optionLists.length > 0) {
                let cWidth = this.$refs.inputSearchOption.clientWidth;
                this.$refs.dropSection.style.display = "block";
                this.$refs.dropSection.style.width = `${cWidth}px`;
                this.dropShow = true;
            //}
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
            // //key tab
            // if(keyCode == 9) {
            //     this.dropShow = true;
            //     this.focused = false;
            //     this.selectedData = this.items[this.listindex];
            //     this.emitValueChanged();
            //     return true;                
            // } 
            // //enter key pressed
            // else if(keyCode == 13) { this.keyPressEnter(event); }
            //arrow up
            if(keyCode == 38) {  this.onArrowUp(event); event.break;}          
            //arrow down  
            else if(keyCode == 40) {  this.onArrowDown(event); event.break;}
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
            // this.onSearching = false;
            // this.focused = false;
            // let that = this;
            // window.setTimeout(function(){ that.blurHandler(); },100);   
            this.blurHandler();
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
            this.keyword = this.selectedData[this.captionField];
            this.hideDropList();
        },

        //send data to parent
        emitValueChanged() {
            this.$emit('item-select', this.selectedData); 
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
            if(this.retrieveAll == true) { this.listData(); } 
            else { this.listSearchData(); }
        },50,true),
        

        listData(init) { 
            if(init) {
                this.items = this.dataBuffers.filter(dataBuffer => {
                    return dataBuffer[this.valueField].toLowerCase() == this.params.keyword.toLowerCase()
                }) 
                if(this.items.length == 1) {
                    this.listindex = 0;
                    this.selectedData = this.items[0];
                    this.keyword = this.selectedData[this.captionField];
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
                    })
                }
                this.dataBuffers = buffer;
                this.items = this.dataBuffers;
                this.$emit('ready',true);
            }
            
        },
    },
}
</script>

<style scoped>

.mySearchOptionContainer {
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
    background-color:#1E90FF;
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