<template>
    <div class="uk-container uk-container-large" style="padding:1em;">
        <header-page title="SATUSEHAT IHS - PATIENT REFERENCE" subTitle="SatuSehat IHS"></header-page>
        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
            <p>{{ errors.invalid }}</p>
        </div>
        <div style="margin-top:1em;">
            <div class="uk-card" style="padding:0;margin:0;border-radius:10px;min-height:400px;">
                <ul uk-tab style="padding:0;">
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - NIK</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - NAME</a></li>
                    <li><a href="#" style="font-size:14px;font-weight:500;">SEARCH - ID</a></li>
                </ul>
                <ul class="uk-switcher" style="padding:0;margin:0;">
                    <li>
                        <form action="" @submit.prevent="submitFilterNIK()" style="padding:0;margin:0;">
                            <input id="nik" type="text" name="nik" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="NIK" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
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
                                        <td>{{dt['no']}}</td>
                                        <td>{{dt['ihs_number']}}</td>
                                        <td>{{dt['name']}}</td>
                                        <td>{{dt['gender']}}</td>
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
                            <!-- <input id="tahun" type="date" format="YYYY" name="tahun" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:10px;" placeholder="Tahun Lahir" required> -->
                            <input id="tahun" type="number" placeholder="YYYY" min="1900" max="2020" class="uk-input uk-form-small uk-width-auto" style="border:none;height:33px;margin-left:0;" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
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
                                        <td>{{dt['no']}}</td>
                                        <td>{{dt['ihs_number']}}</td>
                                        <td>{{dt['name']}}</td>
                                        <td>{{dt['gender']}}</td>
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
                        <form action="" @submit.prevent="submitFilterID()" style="padding:0;margin:0;">
                            <input id="ihs" type="text" name="ihs" value="100000030009" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="ID IHS" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <div class="uk-card uk-card-default" style="padding:1em;min-height:400px;margin-top:1em;">
                            <form action="" @submit.prevent="postProfile" >
                                <div class="uk-grid-small" uk-grid style="padding:0;margin:0.5em 0.5em 0.5em 0;">
                                    <div class="uk-width-1-1@m">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Nama Lengkap</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <input id="patien_nama" class="uk-input uk-form-small" type="text" placeholder="nama lengkap" required>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Jenis Kelamin</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <input id="patien_js" class="uk-input uk-form-small" type="text" placeholder="nama lengkap" required/>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Tanggal Lahir</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <input id="patien_tglLahir" class="uk-input uk-form-small" type="text" placeholder="tanggal lahir">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-1">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Alamat</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <textarea id="patien_alamat" class="uk-textarea uk-form-small" type="text" placeholder="no tanda pengenal"></textarea>
                                        </div>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">No Telepon</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <input id="patien_noTlpn" class="uk-input uk-form-small" type="text" placeholder="telepon">
                                        </div>
                                    </div>
                                    <div class="uk-width-1-3@m">
                                        <label class="uk-form-label" for="form-stacked-text" style="font-weight:500;">Marital Status</label>
                                        <div class="uk-form-controls" style="margin:0.5em 0 0 0;">
                                            <input id="patien_marital" class="uk-input uk-form-small" type="text" placeholder="telepon">
                                        </div>
                                    </div>
                                </div>                
                            </form>      
                        </div>
                          
                        <!-- <form action="" @submit.prevent="submitFilterID()" style="padding:0;margin:0;">
                            <input id="ihs" type="text" name="ihs" class="uk-input uk-form-small uk-width-auto text-keyword-simple" style="border:none;height:33px;margin-left:0;" placeholder="ID IHS" required>
                            <button type="submit" class="hims-table-btn uk-width-auto" style="margin-left: 10px;"><span uk-icon="search"></span> Cari</button>
                        </form>
                        <table class="uk-table hims-table uk-table-responsive1 uk-box-shadow-large uk-table-middle1" style="margin-top:1em;">
                            <slot name="tbl-header">
                                <thead>
                                    <tr>
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
                                        <td>{{dt['no']}}</td>
                                        <td>{{dt['ihs_number']}}</td>
                                        <td>{{dt['name']}}</td>
                                        <td>{{dt['gender']}}</td>
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
                        </table> -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapActions, mapMutations } from 'vuex';
    import headerPage from '@/Components/HeaderPage.vue';
    import BaseRow from '@/Components/BaseTable/BaseRow.vue';
    import userProfile from '@/Pages/SatuSehat/Patient/components/UserProfile.vue';
    
    export default {
        components : {
            headerPage,
            BaseRow,
            userProfile
        },
        computed : {
            ...mapGetters(['errors']),
        },
        data() {
            return {
                isLoading : false,
                datas: [],
                columns: [
                    { name:"no", title: "Nomor"},
                    { name:"id", title: "IHS Number"},
                    { name:"name", title: "Name" },
                    { name:"gender", title: "Gender"},
                ],
                genderSelected: 1,
                options: [
                    {text: 'Male', value:'male'},
                    {text: 'Female', value:'female'},
                ],
                tahun: '',
                params: [],
                searchUrl: '/satusehat/patient'
             }
        },
        mounted() {
        },
    
        methods : {
            ...mapActions('satusehatPasien',['searchNIK', 'searchName', 'searchID']),
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
                            'no':(i+1),
                            'ihs_number': response.entry[i]['resource'].id,
                            'name': response.entry[i]['resource']['name'][0].text,
                            'gender' : response.entry[i]['resource'].gender,
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
                    console.log(response);
                    this.isLoading = false;
                    document.getElementById('patien_nama').value = response.name[0].text;
                    document.getElementById('patien_js').value = response.gender;
                    document.getElementById('patien_tglLahir').value = response.birthDate;
                    document.getElementById('patien_alamat').value = response.address[0].line;
                    document.getElementById('patien_noTlpn').value = response.telecom[0].value;
                    document.getElementById('patien_marital').value = response.maritalStatus.text;
                });
            },
        }
    }
    </script>