<template>
    <div>
        <div class="status-2 main-box">
            <div class="box-2 x16-semibold">{{totalNilaiAset}}</div>
            <div class="flex-row-2">
                <span class="currency-circle-rupiah"></span>
                <h3 class="text-2 h3overview">{{formattedTotalNilaiAset}}</h3>
            </div>
        </div>
    </div>

</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import "../overviewStyle.css";

export default {
  created () {
  },
        name:"Box2",
        props: {
            totalNilaiAset: {
                type: Text
            },
            value: {
                type: Number
            }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
        },
        components : {
            headerPage,
            BaseRow,
        },
        computed : {
            ...mapGetters(['errors']),
            formattedTotalNilaiAset() {
            // Format totalNilaiAset ke format rupiah
            const totalNilaiAsetNumber = parseFloat(this.value);
            if (isNaN(totalNilaiAsetNumber)) {
                return "Invalid Value";
            }
            const formatter = new Intl.NumberFormat('id-ID', {
                currency: 'IDR',
            });
            return formatter.format(totalNilaiAsetNumber);
            },
        },
        data() {
            return {  }
        },
        
        
        mounted() {
        },
    
        methods : {
            // ...mapActions('asset',['listAsset','deleteAsset','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),
        }
    }
</script>

<style>
    .status-2 {
        /* align-items: flex-end; */
        background-color: var(--palegreen);
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        /* gap: 26px; */
        /* height: 112px; */
        overflow: hidden;
        padding: 23px 14px;
        /* width: 252px; */
    }
    .box-2 {
        align-self: flex-start;
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        margin-right: 16px;
        margin-left: 10px;
        white-space: nowrap;
    }
    .flex-row-2 {
        align-items: center;
        display: grid;
        grid-template-columns: auto min-content;
        gap: 10px;
        margin-top: 25px;
    }
    .currency-circle-rupiah {
        background-image: url("@/Assets/assetoverview-rupiah.svg");
        display: inline-block;
        height: 35px;
        position: relative;
        width: 35px;
        margin: 20px 20px 0;
    }
    .text-2 {
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        /* min-width: 125px; */
        white-space: nowrap;
        margin-left: 10px;
    }
    .h3overview {
        flex: 1;
    }
</style>