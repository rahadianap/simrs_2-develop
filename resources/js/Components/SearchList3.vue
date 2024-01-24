<template>
    <div :class="config.compClass" ref="containerListSelect" :style="config.compStyle" class="myListSelectContainer">
        <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">{{label}}</label>
        <div class="uk-form-controls list-select-input">
            <div class="uk-inline uk-width-1-1">
                <input ref="inputListSelect" class="uk-input uk-form-small" type="text" v-bind:style="config.compStyle" style="color:black;padding-left:10px;"
                    :disabled = "config.disabled"
                    :placeholder= "placeholder"
                    :required= "config.required"
                    v-model = "keyword"
                    @keydown = "onInputFieldKeydown"
                    @focus = "onFocus"
                    @blur = "onBlur">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: chevron-down"></span>                
            </div>                                             
        </div>
        <div class="uk-hidden">
            <!-- <input type="text" :name="searchName" v-model="value"> -->
        </div>
        <div ref="dropListSection" class="drop-list">
            <div ref="scrollContainer" class="uk-card uk-card-body uk-card-default" style="padding:0; margin:0;max-height:350px;overflow-y:auto;width:100%;z-index:200;">
                <ul v-if="items" class="uk-list">
                    <li ref = "listoptions" 
                        v-for = "(item,index) in items" 
                        :class = "{ selected: index === listindex }" 
                        v-bind:key = "index" 
                        @mousedown = "selectItem(item,index)">                            
                        <p v-for="(cap,capindex) in detailItems" v-bind:key="capindex" class="list-data" :class="cap.isTitle && 'list-title'"><span style="font-weight:500;">{{cap.labelData}}</span>{{item[cap.colName]}}</p>                                                                    
                    </li>
                </ul>
            </div>
        </div>                      
    </div>
</template>

<script>
import $axios from '@/Stores/httpReq';
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';

export default {
    name : 'search-list3',
    model : { 
        prop : 'value', 
        event:'input' 
    },
    props : {
        //data list 
        searchName : {type:String, required:false, default:'keyword' },
        dataLists : { type : Object, required: false, default:[] },
        config : {
            //column data yang digunakan sebagai text / caption di dropdown
            disabled : { type:Boolean, required:false, default:false },
            //column data yang digunakan sebagai text / caption di dropdown
            required : { type:Boolean, required:false, default:false },
            //component class for container
            compClass : { type:String, required:false, default:'' },
            //component style for container
            compStyle : { type:String, required:false, default:'color:red;' },
        },
        //text label
        label  : { type : String, required: false, default:null },
        //component placeholder        
        placeholder : { type:String, required:false, default:'' },
        //value data   
        value : { type:String, required: false, default : null },
        //items value field
        valueField :  { type : String, required: true },
        //items text untuk menampilkan data yang dipilih
        captionField :  { type : String, required: true }, 
        //items text untuk menampilkan data yang dipilih
        detailItems :  { type : Array, required: false }, 
    },
    
    data() {
        return {
            //buffering result from fetching data
            items : [],
            //buffer selected item
            dataBuffers : [],
            //indeks data saat melakukan pemilihan pada 
            listindex : -1,
            //final value
            keyword : '',
            dropShow : false,
            focused : false,
            onInput : false,
            onSearching : false,
            selectedData : null,
        }
    },
    created() {
    },

    watch: {
        /**
         * amati setiap perubahan kata pencarian 
         * hanya berjalan saat keyword dituliskan
         */
        'keyword' : function(newVal, oldVal){
            if(newVal !== oldVal) {
                if(this.focused == true) {
                    this.listData();
                }
            }
        },

        /**
         * watch value to update / clear data when 
         * parent update value
         */
  	    'value': function(newVal, oldVal) {
            this.valueChange(newVal);
        },
        
        'dataLists': function(newVal, oldVal) {            
            this.items = newVal;
            if(this.items.length > 0 ) {
                this.listData();
            }
        }
    },
    
    methods : {
        valueChange(newVal){
            this.items = this.dataLists;            
            if(newVal == '' || newVal == null) {
                this.keyword = '';
                this.selectedData = null;
                /*edit disini tgl 7 juni 2023 */
                this.items = this.dataLists;
                /*akhir edit disini tgl 7 juni 2023 */
                
                this.listindex = -1; 
            }
            else {
                this.listindex = -1;
                if(this.items.length > 0){
                    this.listindex = this.items.findIndex(x => x[this.valueField].toLowerCase() === newVal.toLowerCase());
                    if(this.listindex >= 0) {
                        this.keyword = this.items[this.listindex][this.captionField];
                    }
                }
                
                // this.items = this.dataLists.filter(item => {
                //     return item[this.valueField].toLowerCase() == newVal.toLowerCase();
                // }) 
                //jika hanya ada satu maka nilai ini yang dianggal benar.
                // if(this.items.length == 1) {
                //     this.listindex = 0;
                //     this.selectedData = this.items[0];
                //     this.keyword = this.selectedData[this.captionField];
                // }
                // else {
                //     this.items = this.dataLists;
                //     this.listindex = -1;
                //     this.selectedData = null;
                //     this.keyword = null;
                // }
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
            let cWidth = this.$refs.inputListSelect.clientWidth;
            this.$refs.dropListSection.style.display = "block";
            this.$refs.dropListSection.style.width = `${cWidth}px`;
            this.dropShow = true;
        },
        
        hideDropList() {
            this.$refs.dropListSection.style.display = "none";
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
            this.listData(true);
        },
        /**
         * data dipilih saat key enter ditekan
         */
        keyPressEnter(event){
            event.preventDefault();
            //this.selectedData = this.items[this.listindex];
            this.focused = false;             
            this.emitValueChanged();
        },        

        onInputFieldKeydown(event){         
            let keyCode = event.keyCode;
            //key tab
            if(keyCode == 9) {
                this.dropShow = true;
                this.focused = false;
                this.emitValueChanged();
                return true;                
            } 
            //enter key pressed
            if(keyCode == 13) { this.keyPressEnter(event); }
            //arrow up
            else if(keyCode == 38) {  this.onArrowUp(event); }          
            //arrow down  
            else if(keyCode == 40) {  this.onArrowDown(event); }
            //else
            else { this.searchData(); }
        },
        //handle event when user press arrow down
        onArrowDown(ev) {
            ev.preventDefault();
            if (this.listindex < this.items.length-1) {                 
                this.listindex = this.listindex + 1;
                if(this.listindex < 0) { this.listindex = 0; };
                this.keyword = this.items[this.listindex][this.captionField];
                this.fixScrolling(ev);
            }
        },
        //handle event when user press arrow up
        onArrowUp(ev) {
            ev.preventDefault();
            if (this.listindex > 0) {
                this.listindex = this.listindex - 1;
                if(this.listindex < 0) { this.listindex = 0; };
                this.keyword = this.items[this.listindex][this.captionField];
                this.fixScrolling();
            }
        },
        //handle scrolling on item when user press key down or up
        fixScrolling(){
            let top = this.$refs.listoptions[this.listindex].offsetTop;
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
            //this.selectedData = this.items[this.listindex];
            this.emitValueChanged();
        },
        
        //retrieve data when clicked / enter key on dropdown list
        selectItem(val,index) {      
            this.focused = false;
            this.listindex = index;
            //this.selectedData = val;
            this.keyword = val[this.captionField];
            this.hideDropList();
        },

        //send data to parent
        emitValueChanged() {
            window.clearTimeout();
            this.hideDropList();    
            if(this.listindex >= 0) {                
                let buffData = this.items[this.listindex];                
                if(buffData) {
                    let val = buffData[this.valueField];
                    this.keyword = buffData[this.captionField];
                    if(val !== null && val !== this.value) {
                        this.$emit('input', val);
                        this.$emit('item-select', buffData);   
                    }
                } 
                else {
                    this.keyword = '';
                    this.$emit('input', null);
                    this.$emit('item-select', null);
                }
            }
            else {
                this.keyword = '';
                this.$emit('input', null);
                this.$emit('item-select', null);
            }                
        },
        
        /**
         * execute when input type event.
         * debounce give time to user finish typing before search to backend  
         * to avoid too many request data to backend
         */
        searchData(){
            this.showDropList();
            this.listData();
        },

        listData(init) { 
            this.listindex = -1;
            if(init) {
                if(this.value == null || this.value == '') {
                    this.listindex = -1;
                    this.keyword = '';
                    //this.items = this.listData;
                }
                else {
                    this.listindex = -1;
                    this.items = this.dataLists.filter(item => {return item[this.valueField].toUpperCase().includes(this.value.toLowerCase())});
                
                    this.listindex = this.items.findIndex(x => x[this.valueField].toLowerCase().includes(this.value.toLowerCase()));
                    if(this.listindex >= 0){
                        this.keyword = this.items[this.listindex][this.captionField];
                    }
                }
            }

            else {
                if(this.keyword == null || this.keyword == '') {
                    this.listindex = -1;
                    this.items = this.dataLists;
                }
                else {
                    this.listindex = -1;
                    // console.log(`keyword : ${this.keyword}` );
                    // console.log(this.dataLists);
                    this.items = this.dataLists.filter(item => {return item[this.valueField].toUpperCase().includes(this.keyword.toUpperCase())});
                
                    this.listindex = this.items.findIndex(x => x[this.valueField].toLowerCase().includes(this.keyword.toLowerCase()));
                }
            }
            
            // if(this.keyword == null) {
            //     this.keyword = '';
            // }
            // this.items = this.dataLists.filter(item => {
            //     return item[this.captionField].toLowerCase().includes(this.keyword.toLowerCase())
            // });
            // if(this.items.length == 0) {
            //     this.selectedData = null;
            //     this.listindex = -1;
            //     this.items = this.dataLists;
            // }
            // else if (this.items.length == 1) {
            //     this.listindex = 0;
            // }
            // else { 
            //     this.listindex = -1;
            //     this.selectedData = null; 
            // }
        },
        refreshList(data) {
            this.items = data;
            this.listData(true);
        }
    },
}
</script>

<style scoped>

.myListSelectContainer {
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