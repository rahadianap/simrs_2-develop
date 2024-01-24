<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="SATUSEHAT IHS - Location" subTitle="SatuSehat IHS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">CREATE Location</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - ID</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - NAME</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - PART OF</a></li>
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <div style="margin-top:1em;">
                            <base-table ref="tableLocation"
                                :columns = "tbColumns" 
                                :config="configTable" 
                                :buttons = "buttons"
                                :urlSearch="searchUrl">
                            </base-table>
                        </div>
                    </li>
                    <li>
                        <form action="" @submit.prevent="submitFilterID()" style="padding:0;margin:0;">
                            <input id="ihs" type="text" name="ihs" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="ID IHS" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1" v-if="isLoading">
                                            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                                <span uk-spinner></span>
                                                sedang mengambil data
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                            </slot>
                            <slot name="tbl-body">
                                <tbody v-if="this.datas && this.datas.length > 0">
                                    <tr v-for="dt in this.datas">
                                        <td>{{dt['id']}}</td>
                                        <td>{{dt['name']}}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="!isLoading">
                                        <td :colspan="columns.length + 1">
                                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </slot>
                        </table>
                    </li>
                    <!-- <li>
                        <form action="" @submit.prevent="submitFilterNama()" style="padding:0;margin:0;">
                            <input id="nama" type="text" name="nama" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="Nama" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1" v-if="isLoading">
                                            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                                <span uk-spinner></span>
                                                sedang mengambil data
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                            </slot>
                            <slot name="tbl-body">
                                <tbody v-if="this.datas && this.datas.length > 0">
                                    <tr v-for="dt in this.datas">
                                        <td>{{dt['id']}}</td>
                                        <td>{{dt['name']}}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="!isLoading">
                                        <td :colspan="columns.length + 1">
                                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </slot>
                        </table>
                    </li> -->
                    <!-- <li>
                        <form action="" @submit.prevent="submitFilterID()" style="padding:0;margin:0;">
                            <input id="abc" type="text" name="abc" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="ID IHS" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign,}">{{ col.title }}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; background-color: #fff;padding:1em;" :colspan="columns.length + 1" v-if="isLoading">
                                            <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                                                <span uk-spinner></span>
                                                sedang mengambil data
                                            </p>
                                        </th>
                                    </tr>
                                </thead>
                            </slot>
                            <slot name="tbl-body">
                                <tbody v-if="this.datas && this.datas.length > 0">
                                    <tr v-for="dt in this.datas">
                                        <td>{{dt['ihs_number']}}</td>
                                        <td>{{dt['name']}}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="!isLoading">
                                        <td :colspan="columns.length + 1">
                                            <p style="font-weight:400;font-size:14px; font-style:italic;padding:1em;text-align: center;">tidak ada data untuk ditampilkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </slot>
                        </table>
                    </li> -->
                </ul>
            </div>
        </div>
    </div>
    <form-create-Location ref="formCreateLocation" v-on:saveSucceed="refreshTable" v-on:closed="refreshTable"></form-create-Location>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import BaseTable from '@/Components/BaseTable';
    import FormCreateLocation from './components/FormCreateLocation.vue';
    
    export default {
        components : {
            headerPage,
            BaseRow,
            BaseTable,
            FormCreateLocation
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isExpanded : false,
                configTable : {                
                    isExpandable : false,
                    filterType : 'REGULAR', 
                },
                tbColumns : [             
                    { 
                        name : 'bridging_id', 
                        title : 'Id', 
                        colType : 'linkmenus', 
                        functions: [
                            { 'title':'Ubah', 'icon':'file-edit','callback':this.updateData },
                            { 'title':'Hapus', 'icon':'trash','callback':this.deleteData },   
                        ],
                    },   
                    { 
                        name : 'bridging_group', 
                        title : 'Group', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'local_resource_id', 
                        title : 'Local', 
                        colType : 'text', 
                        isSearchable : true,
                    },
                    { 
                        name : 'bridging_resource_id', 
                        title : 'Bridging', 
                        colType : 'text', 
                        isSearchable : false,
                    }
                    
                ], 
                buttons : [
                    { title : 'Data Baru', icon:'plus-circle', 'callback' : this.createLocation },
                ],
                isLoading : false,
                datas: [],
                genderSelected: 1,
                options: [
                    {text: 'Male', value:'male'},
                    {text: 'Female', value:'female'},
                ],
                columns: [
                    { name:"id", title: "IHS ID"},
                    { name:"name", title: "Name" },
                ],
                tahun: '',
                params: [],
                searchUrl: '/satusehat/location'
             }
        },
    
        methods : {
            ...mapActions('satusehatLokasi',['searchID', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            getYear(params) {
                let res = new Date(params);
                let year = res.getFullYear();
                return `${year}`;
            },

            submitFilterNama(){
                this.datas = [];
                this.isLoading = true;
                this.params = [
                    {
                        'name' : document.getElementById('nama').value
                    }
                ];
                this.searchName(document.getElementById('nama').value).then((response) => {
                    this.isLoading = false;
                    for (let i=0;i<=response.entry.length-1;i++) {
                        this.datas.push({
                            'ihs_number': response.entry[i]['resource'].id,
                            'name': response.entry[i]['resource']['name'][0].text,
                        });
                    }
                    return this.datas;
                });
            },

            submitFilterID(){
                this.datas = [];
                this.isLoading = true;
                this.searchID(document.getElementById('ihs').value).then((response) => {
                    this.isLoading = false;
                    console.log(response);
                    this.datas.push({
                        'id': response.id,
                        'name': response.name,
                    });
                    return this.datas;
                });
            },

            createLocation(){        
                this.CLR_ERRORS();
                this.$refs.formCreateLocation.newLocation();        
            },

            refreshTable() {
                this.$refs.tableLocation.retrieveData();
            },
        }
    }
    </script>