<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="Asset Management IHS" subTitle="Asset IHS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">List Asset</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">Master Data Kategori</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">Master Data Lokasi</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">Master Data Sparepart</a></li>
                </ul>

                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <!-- Table List Asset -->
                    <li>
                        <div style="margin-top:1em;">
                           <TableAsset></TableAsset>
                        </div>
                    </li>
                    
                    <!-- Table Master Data Group/Kategori -->
                    <li>
                        <div style="margin-top:1em;">
                            
                            <TableKategoriStatus></TableKategoriStatus>
                            <!-- <TableKategori></TableKategori> -->

                        </div>
                    </li>
            
                    <!-- Table Master Data Lokasi -->
                    <li>
                        <div style="margin-top:1em;">
                            <TableLokasi></TableLokasi>
                        </div>
                    </li>
                    
                    <!-- Table Master Data Lokasi -->
                    <li>
                        <div style="margin-top:1em;">
                            <TableSparepart></TableSparepart>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    // import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    // import BaseTable from '@/Components/BaseTable';
    import TableAsset from './component/Asset/table.vue';
    // import TableKategori from './component/DataKategori/table.vue';
    import TableKategoriStatus from './component/Kategori/TableKategoriStatus.vue';
    import TableLokasi from './component/Lokasi/table.vue';
    import TableSparepart from './component/Sparepart/table.vue';

    
    export default {
        components : {
            headerPage,
            TableAsset,
            TableKategoriStatus,
            TableLokasi,
            TableSparepart,
        },
        computed : {
            ...mapGetters(['errors']),
        },
        mounted() {
        },
    
        methods : {
        }
    }
    </script>