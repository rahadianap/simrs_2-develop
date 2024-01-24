<template>
    <div>
        <div class="status-1 main-box">
            <div class="box-1 x16-semibold">{{totalAsetTerdata}}</div>
            <div class="flex-row-1">
                <img class="arrow-rise-1" src='@/Assets/assetoverview-arrowrise.svg' alt="ArrowRiseIcon">
                <h3 class="text-1 h3overview">{{value}}</h3>
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
        name:"Box1",
        props: [
            "totalAsetTerdata", 
            "value"                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
        ],
        components : {
            headerPage,
            BaseRow,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {  }
        },
        
        
        mounted() {
        },
    
        methods : {
            // ...mapActions('asset',['listAsset','deleteAsset','searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS'])

        }
    }
</script>

<style>
    .status-1 {
        align-items: flex-end;
        background-color: var(--primaryblue);
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        /* gap: 26px; */
        /* height: 112px; */
        overflow: hidden;
        padding: 23px 14px;
        /* width: 252px; */
    }
    .box-1 {
        align-self: flex-start;
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        margin-right: 16px;
        margin-left: 10px;
        white-space: nowrap;
    }
    .flex-row-1{
        align-items: center;
        display: flex;
        gap: 285px;
        min-width: 166px;
        margin-top: 25px;
    }
    .arrow-rise-1 {
        height: 35px;
        /* position: relative; */
        width: 35px;
        margin-top: 20px;
        /* min-width: 176px; */
    }
    .text-1 {
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        /* min-width: 125px; */
        white-space: nowrap;
    }
</style>