<template>
    <div class="uk-width-1-1@m" style="padding:0 0.5em 0.2em 0.5em;margin:0;">
        <table class="uk-table hims-table">
            <thead class="uk-card uk-card-default uk-box-shadow-small" style="border-top:2px solid #cc0202;">
                <tr>
                    <th style="text-align:center;width:50px;padding:0.5em;">...</th>
                    <th style="padding:0.5em;">No Labu</th>
                    <th style="text-align:center;padding:0.5em;">Gol Darah</th>
                    <th style="text-align:center;padding:0.5em;">Rhesus</th>
                    <th style="padding:0.5em;">Group Darah</th>
                    <th style="text-align:center;width:70px;padding:0.5em;">Vol(CC)</th>
                    <th style="text-align:center;padding:0.5em;">Terima</th>
                    <th style="text-align:center;padding:0.5em;">Kadaluarsa</th>                                            
                </tr>
            </thead>
            <tbody>    
                <tr v-if="darahLists" v-for="dt in darahLists">
                    <td style="width:50px;text-align: center;">
                        <input class="uk-checkbox" type="checkbox" v-model="dataLists" :value="dt" @change="showData" style="margin-left:5px;border:1px solid black;">
                    </td>
                    <td>{{dt.no_labu}}</td>
                    <td style="text-align:center;">{{dt.gol_darah}}</td>
                    <td class="uk-text-uppercase" style="text-align:center;">{{dt.rhesus}}</td>
                    <td>{{dt.group_darah}}</td>
                    <td style="text-align:center;">{{dt.volume}}</td>
                    <td style="text-align:center;">{{dt.tgl_terima}}</td>
                    <td style="text-align:center;">{{dt.tgl_kadaluarsa}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'table-pemusnahan',
    data() {
        return {
            selectAll : [],
            dataLists : [],
            darahLists : [],
            isLoading : false,
        }
    },
    mounted() {     
        this.retrieveStockDarah();
    },

    methods : {
        ...mapActions('bankDarah',['getStockDarah','getDarahLists','createPenerimaanDarah','updatePenerimaanDarah','deletePenerimaanDarah','dataPenerimaanDarah']),
        ...mapMutations(['CLR_ERRORS']),

        refreshData(data) {
            if(data){
                this.dataLists = JSON.parse(JSON.stringify(data));
            }
        },

        retrieveStockDarah(){
            this.CLR_ERRORS();
            this.isLoading = true;
            this.getStockDarah().then((response) => {
                if (response.success == true) {
                    this.isLoading = false;
                    this.darahLists = response.data;
                    console.log(this.darahLists);
                }
                else { alert(response.message); this.isLoading = false; }
            })
        },

        showData(){
            console.log(this.dataLists);
            this.$emit('itemPemusnahanChange',this.dataLists);
        },

        selectAllData(){
            if(this.selectAll){
                this.dataLists = this.darahLists;
            }
            else { this.dataLists = []; }
        }
    }


}
</script>