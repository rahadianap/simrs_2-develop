<template>
    <div>
        <div>
            <form action="">
                <div class="uk-grid-small" uk-grid style="padding:0.5em 1em 0.5em 0;margin:0;">
                    <div class="uk-width-expand@m">
                        <h5 style="color:indigo;padding:0;margin:0;">RESEP OBAT BARU</h5>
                    </div>
                    <div class="uk-width-auto@m">
                        <button @click.prevent="searchObat">Pilih Item</button>
                    </div>                                        
                </div>
            </form>
        </div>
        <div style="padding:0 0.5em 0.2em 0.5em;">
            <table class="uk-table uk-table-striped child-table" style="padding:0;margin:0;">
                <thead>
                    <th class="tb-header-reg" style="text-align:left;color:white;">ID</th>
                    <th class="tb-header-reg" style="text-align:left;color:white;">Item Resep</th>
                    <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">Satuan</th>
                    <th class="tb-header-reg" style="width:40px;text-align:center;color:white;">Jumlah</th>
                    <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Harga</th>
                    <th class="tb-header-reg" style="width:80px;text-align:right;color:white;">Subtotal</th>                                                
                    <th class="tb-header-reg" style="width:60px;text-align:center;color:white;">...</th>
                </thead>
                <tbody>
                    <row-form-resep 
                        v-if="prescriptionItems" 
                        v-for="dt in prescriptionItems"
                        :rowData = "dt"
                        :dataChange = "calculateTotal"
                        :activationChange = "changeActivationActItem">
                    </row-form-resep>
                    <tr>
                        <td colspan="5" style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">TOTAL</td>
                        <td style="font-size:14px;font-weight:500;text-align:right;color:black;padding:0.5em;">{{formatCurrency(poliTransaction.resep.total)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    name : 'form-resep',

    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('resep',['prescriptionItems','prescription']),
    },

    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapMutations('resep',['CLR_PRESCRIPTION','SET_PRESCRIPTION']),

        createResepBaru(data) {

        },

        ambilDataResep(data) {

        }
    }
}
</script>