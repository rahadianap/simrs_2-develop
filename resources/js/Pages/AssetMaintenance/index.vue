<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="Asset Management IHS" subTitle="Asset IHS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">List Maintenance Ticket</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">List Data Schedule</a></li>
                </ul>


                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <!-- Table List Maintenance Ticket -->
                    <li>
                        <div style="margin-top:1em;">
                           <TableTicket></TableTicket>
                        </div>
                    </li>

                    <!-- Table Data Maintenance Schedule -->
                    <li>
                        <div style="margin-top:1em;">
                           <TableSchedule></TableSchedule>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
     <!-- <form-asset ref="formAsset" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-asset> -->
    <!-- <edit-form-asset ref="editformAsset" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></edit-form-asset> -->
</template>

<script>
    import { mapGetters } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    // import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    // import BaseTable from '@/Components/BaseTable';
    import TableTicket from './component/Ticket/table.vue';
    import TableSchedule from './component/Schedule/table.vue';

    export default {
        components : {
            headerPage,
            TableTicket,
            TableSchedule,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        methods : {
        },
    }
    </script>