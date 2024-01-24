<template>
    <div>
        <div class="status-7 main-box">
            <div class="box-7 x16-semibold">{{totalPengaduan}}</div>
            <div class="flex-row-7">
                <img class="ticket-icon" src='@/Assets/assetoverview-ticket.svg' alt="TicketIcon">
                <h3 class="text-7 h3overview">{{value}}</h3>
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
        name:"Box7",
        props: [
            "totalPengaduan", 
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
    .status-7 {
        align-items: flex-end;
        background-color: var(--berylgreen);
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        /* gap: 26px; */
        /* height: 112px; */
        overflow: hidden;
        padding: 23px 14px;
        /* width: 252px; */
    }
    .box-7 {
        align-self: flex-start;
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        margin-right: 16px;
        margin-left: 10px;
        white-space: nowrap;
    }
    .flex-row-7{
        align-items: center;
        display: flex;
        gap: 280px;
        min-width: 166px;
        margin-top: 25px;
    }
    .ticket-icon {
        height: 35px;
        /* position: relative; */
        width: 35px;
        margin-top: 20px;
        /* min-width: 176px; */
    }
    .text-7 {
        color: var(--black100);
        font-weight: 600;
        line-height: 20px;
        /* min-width: 125px; */
        white-space: nowrap;
    }
</style>