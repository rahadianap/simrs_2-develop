<template>
    <div class="uk-grid-small hims-table-filter-container" uk-grid style="margin:0;padding:0;">            
        <div class="uk-width-expand" style="padding:0;margin:0;">
            <div class="uk-button-group uk-box-shadow-large" style="padding:0;margin:0;border-radius:5px;">
                <div v-if="hasFilter" class="uk-inline">
                    <button class="hims-table-btn" @click.prevent="filterTable"><span uk-icon="search"></span> filter</button>
                    <div v-if="hasFilter" ref="formFilterTable" :id="idTable" class="uk-dropdown-large form-filter-table" uk-dropdown="mode: click; offset:5;">
                        <slot name="form-filter"></slot>
                    </div>
                </div>

                <button class="hims-table-btn" @click.prevent="refresh"><span uk-icon="refresh"></span>refresh</button>
                <button v-for="btn in customButtons" class="hims-table-btn" @click.prevent="btn.callback">
                    <span :uk-icon="btn.icon"></span>{{btn.title}}
                </button>                   
            </div>
        </div>
        <div class="uk-width-1-1 uk-hidden" style="font-size:12px; font-style:italic;padding:1em 0 0.5em 0;margin:0;">
            filter pencarian <span style="font-weight:500;">role id: </span>lalala, <span style="font-weight:500;">deskripsi: </span> : dududu       
        </div>
    </div>
</template>

<script>
export default {
    name : 'button-table',
    props : {
        configs : { type : Object, required:false, default:null },
        customButtons : { type : Object, required:false, default:null },
        hasFilter : { type : Boolean, required:false, default:false },
        searchFields : { type : Object, required:false, default:null },
    },

    data(){
        return {
            idTable : this.generateId(),
            per_page : 10,
            params : {per_page : 10},
        }
    },

    watch : {
        configs(newVal,oldVal){
            //console.log(newVal)
        }
    },
    mounted() {
        //this.idTable = this.generateId();
    },

    methods : {
        
        generateId() {
            let characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = ' ';
            const charactersLength = characters.length;
            for ( let i = 0; i < 10; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        },

        submitSearch() {
            this.params = this.$refs.filterForm.value;
            return false;
        },

        /**event on refresh */
        refresh(){ 
            let comp = this.$refs.formFilterTable;
            let formElements = Array.from(comp.elements);
            formElements.forEach(element => {
                //console.log(element);
            });
            this.$emit('refresh',true);  

        },


        filterTable() {
            let id = this.$refs.formFilterTable.id;
            UIkit.dropdown('#'.id).show();
        },
    }
}
</script>

<style>
.hims-table-btn {
    border:none;
    padding:0.5em 1em 0.5em 1em;
    min-width:20px;
    background-color: #fefefe;
}

.hims-table-btn span {
    margin-right:5px;
}

.hims-table-btn:hover {
    background-color: #cc0202;
    color:white;
}

.hims-table-btn:first-child {
    border-bottom-left-radius: 5px;
    border-top-left-radius: 5px;
}

.hims-table-btn:last-child {
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
}
.form-filter-table {
    background-color: white;
    padding:1em;
    border-radius:5px;
}

.form-filter-table button {
    padding:0.2em 1em 0.2em 1em; 
    width:100%; 
    background-color: #cc0202;
    color:white;
}
</style>