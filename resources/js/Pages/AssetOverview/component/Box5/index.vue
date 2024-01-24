<template>
    <div>
        <div class="status-5 main-box">
            <div class="box-5 x16-semibold">{{totalBarangKeluar}}</div>
            <div class="flex-row-5">
                <img class="package-icon" src='@/Assets/assetoverview-package.svg' alt="PackageIcon">
                <h3 class="text-5 h3overview">{{value}}</h3>
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
            "totalBarangKeluar", 
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
    .status-5 {
        align-items: flex-end;
        background-color: var(--guardian-red);
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        /* gap: 26px; */
        /* height: 112px; */
        overflow: hidden;
        padding: 23px 14px;
        /* width: 252px; */
    }
    .box-5 {
        align-self: flex-start;
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        margin-right: 16px;
        margin-left: 10px;
        white-space: nowrap;
    }
    .flex-row-5{
        align-items: center;
        display: flex;
        gap: 285px;
        min-width: 166px;
        margin-top: 25px;

    }
    .package-icon {
        height: 35px;
        /* position: relative; */
        width: 35px;
        margin-top: 20px;
        /* min-width: 176px; */
    }
    .text-5 {
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        /* min-width: 125px; */
        white-space: nowrap;
    }
</style>