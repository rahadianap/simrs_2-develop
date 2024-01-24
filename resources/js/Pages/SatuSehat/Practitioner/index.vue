<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="SATUSEHAT IHS - PRACTITIONER REFERENCE" subTitle="SatuSehat IHS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - NIK</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - NAME</a></li>
                    
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <form action="" @submit.prevent="submitFilterNIK()" style="padding:0;margin:0;">
                            <input id="nik" type="text" name="nik" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="NIK" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table id="table1" class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
                                        <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign, functions:col.functions}">{{ col.title }}</th>
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
                                        <td>{{dt['rsc_type']}}</td>
                                        <td>
                                        <button type="button" @click.prevent="dataPract" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-left-radius: 5px;border-bottom-left-radius: 5px;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                                            <span uk-icon="file-pdf"></span> Lihat Data
                                        </button>
                                        </td>
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
                    <li>
                        <form action="" @submit.prevent="submitFilterNama()" style="padding:0;margin:0;">
                            <input id="nama" type="text" name="nama" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="Nama" required>
                            <select id="gender" name="gender" v-model="this.genderSelected" class="uk-select uk-form-small uk-width-auto select-status-aktif" style="border:none; border-right:1px solid #eee;height:33px;margin-left:10px;">
                                <option v-for="option in options" v-bind:value="option.value">
                                    {{ option.text }}
                                </option>
                            </select>
                            <input id="tahun" type="number" min="1900" max="2099" step="1" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;width:125px;margin-left:10px;" placeholder="Tahun Lahir" required/>
                            <!-- <input id="tahun" type="date" format="YYYY" name="tahun" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="Tahun Lahir" required> -->
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table id="table2" class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
                                        <th v-for="col in columns" v-bind:style="{ width: col.width, textAlign:col.textAlign, functions:col.functions}">{{ col.title }}</th>
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
                                        <td>{{dt['rsc_type']}}</td>
                                        <td>
                                        <button type="button" @click.prevent="dataPract" class="uk-width-auto hims-table-btn-submit" style="border:none;border-top-left-radius: 5px;border-bottom-left-radius: 5px;border-top-right-radius: 5px;border-bottom-right-radius: 5px;">
                                            <span uk-icon="file-pdf"></span> Lihat Data
                                        </button>
                                        </td>
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
                </ul>
            </div>
        </div>
        <modal-practitioner ref="modalPractitioner"></modal-practitioner>
    </div>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import ModalPractitioner from '@/Pages/SatuSehat/Practitioner/ModalPractitioner.vue';
    
    export default {
        components : {
            headerPage,
            BaseRow,
            ModalPractitioner
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isLoading : false,
                datas: [],
                columns: [
                    { name:"id", title: "IHS Number"},
                    { name:"name", title: "Name" },
                    { name:"rsc_type", title: "Resource Type" },
                    { name:"action", title: "Action" },
                ],
                genderSelected: 1,
                options: [
                    {text: 'Male', value:'male'},
                    {text: 'Female', value:'female'},
                ],
                tahun: '',
                params: [],
                searchUrl: '/satusehat/pract'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('satusehatDokter',['searchNIK', 'searchName']),
            ...mapMutations(['CLR_ERRORS']),

            getYear(params) {
                let res = new Date(params);
                let year = res.getFullYear();
                return `${year}`;
            },
    
            submitFilterNIK(){
                this.datas = [];
                this.isLoading = true;
                this.searchNIK(document.getElementById('nik').value).then((response) => {
                    this.isLoading = false;
                    for (let i=0;i<=response.entry.length-1;i++) {
                        this.datas.push({
                            'ihs_number': response.entry[i]['resource'].id,
                            'nik': response.entry[i]['resource']['identifier'][1].value,
                            'name': response.entry[i]['resource']['name'][0].text,
                            'rsc_type': response.entry[i]['resource'].resourceType,
                            'birth_date': response.entry[i]['resource'].birthDate,
                            'gender': response.entry[i]['resource'].gender
                        });
                    }
                    return this.datas;
                });
            },

            submitFilterNama(){
                this.datas = [];
                this.isLoading = true;
                this.params = [
                    {
                        'name': document.getElementById('nama').value,
                        'gender': document.getElementById('gender').value,
                        'birthdate': this.getYear(document.getElementById('tahun').value),
                    }
                ];
                this.searchName(this.params).then((response) => {
                    this.isLoading = false;
                    for (let i=0;i<=response.entry.length-1;i++) {
                        this.datas.push({
                            'ihs_number': response.entry[i]['resource'].id,
                            'nik': response.entry[i]['resource']['identifier'][1].value,
                            'name': response.entry[i]['resource']['name'][0].text,
                            'rsc_type': response.entry[i]['resource'].resourceType,
                            'birth_date': response.entry[i]['resource'].birthDate,
                            'gender': response.entry[i]['resource'].gender
                        });
                    }
                    return this.datas;
                });
            },

            dataPract() {
                this.CLR_ERRORS();
                this.$refs.modalPractitioner.fillKomponenHarga(this.datas);
                UIkit.modal('#modalPractitioner').show();
                // UIkit.modal('#modalPractitioner').show(this.datas);
            },
        }
    }
    </script>