<template>
    <tr style="border-bottom:1px solid #ccc;">
        <td style="width:40px;padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            Rujukan Berdasarkan Nomor Kartu (List / Multi)
        </td>
    </tr>
    <tr>
        <Transition>
            <td colspan="2" v-if="isExpandedReg" style="background-color:#fefefe;border-bottom:1px solid #eee;margin:0;padding:0;">
                <div class="uk-width-1-1" style="padding:1em 1em 1em 2em;margin:0;">
                    <form action="" @submit.prevent="getData"  class="uk-grid-small" uk-grid>
                        <select class="uk-select uk-form-small uk-width-1-4@m" v-model="asalRujukan" required style="margin-right:5px;max-width:160px;">
                        <option value="PCARE">Pcare</option>
                        <option value="RS">Rumah Sakit</option>
                    </select>
                        <input class="uk-input uk-form-small uk-width-1-4@m" v-model="params" type="text" placeholder="no rujukan" required style="margin-right:5px;max-width:180px;">
                        <button type="submit">Cari Data</button>
                    </form>
                </div>
                <div class="uk-width-1-1" style="padding:0.5em 1em 1em 1em;margin:0;">
                    <h5 style="padding:0;margin:0;">Hasil Pencarian: </h5>
                    <div v-if="isLoading" style="text-align: center; background-color: #fff;padding:1em;" colspan="7">
                        <p style="font-size:12px; font-weight: 500; font-style:italic;padding:0.5em; color:black;">
                            <span uk-spinner></span>
                            sedang mengambil data
                        </p>
                    </div>
                    <div v-if="listResponse !== null">
                        <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                            <thead>
                                <th style="font-size: 12px;font-weight: 500;color:black;">Kunjungan</th>
                                <th style="font-size: 12px;font-weight: 500;color:black;">Peserta</th>
                                <th style="font-size: 12px;font-weight: 500;color:black;">Jenis Peserta</th>
                                <th style="font-size: 12px;font-weight: 500;color:black;">Pelayanan</th>                                                             
                            </thead>
                            <tbody>
                                <tr v-if="listResponse" v-for="item in listResponse">
                                    <td>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">No. </span> {{ item.noKunjungan }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Tgl. </span> {{ item.tglKunjungan }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Keluhan </span> {{ item.keluhan }}
                                        </p>
                                    </td>
                                    <td>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Nama. </span> {{ item.peserta.nama }} ({{ item.peserta.sex }})
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Tgl Lahir. </span> {{ item.peserta.tglLahir }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Noka. </span> {{ item.peserta.noKartu }} |                                        
                                            <span style="font-weight:500;">NIK. </span> {{ item.peserta.nik }}
                                        </p>
                                    </td>
                                    <td>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Jenis. </span> {{ item.peserta.jenisPeserta.kode }} - {{ item.peserta.jenisPeserta.keterangan }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Status. </span> {{ item.peserta.statusPeserta.kode }} - {{ item.peserta.statusPeserta.keterangan }}
                                        </p>
                                    </td>
                                    <td>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Pelayanan. </span> {{ item.pelayanan.kode }} - {{ item.pelayanan.nama }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Rujukan. </span> {{ item.poliRujukan.kode }} - {{ item.poliRujukan.nama }}
                                        </p>
                                        <p style="font-size:12px; color:black;padding: 0;margin:0;">
                                            <span style="font-weight:500;">Perujuk. </span> {{ item.provPerujuk.kode }} - {{ item.provPerujuk.nama }}
                                        </p>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                
            </td>
        </Transition>
    </tr>
</template>
<script>

import { mapGetters, mapActions, mapMutations } from 'vuex';

export default {
    emits : ['itemRequestSelected'],
    name : 'search-list-kartu',
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isLoading : false,
            isExpandedReg : false,
            buffer : [],
            params : null,
            asalRujukan : 'PCARE',
            listResponse : null,
                
            response : {
                diagnosa : {kode:null, nama: null},
                keluhan : null,
                noKunjungan : null,
                pelayanan : { kode: null, nama: null },
                peserta : {
                    cob : {
                        nmAsuransi : null, noAsuransi: null,
                        tglTAT : null, tglTMT:null,
                    },
                    hakKelas : {kode:null, keterangan:null },
                    informasi : { dinsos : null, noSKTM:null, prolanisPRB:null },
                    jenisPeserta : { kode:null, keterangan:null },
                    mr : { noMR:null, noTelepon:null },
                    nama : null,
                    nik : null,
                    noKartu : null,
                    pisa : null,
                    provUmum : { kdProvider:null, nmProvider: null},
                    sex : null,
                    statusPeserta : {keterangan:null, kode:null},
                    tglCetakKartu:null,
                    tglTAT:null,
                    tglTMT:null,
                    tglLahir:null,
                    umur : {umurSaatPelayanan:null, umurSekarang:null},
                },
                poliRujukan : { kode:null, nama:null },
                provPerujuk : { kode:null, nama:null },
                tglKunjungan : null,
            },
        }
    },
    methods : {
        ...mapMutations(['CLR_ERRORS']),
        ...mapActions('bpjsRujukan',[
            'rujukanByNoRujukanPcare',
            'rujukanByNoKartuPcare',
            'rujukanListByNoKartuPcare',
            'rujukanByNoRujukanRS',
            'rujukanByNoKartuRS',
            'rujukanListByNoKartuRS',
            
        ]),

        formatCurrency(value) {
            let val = (value/1).toFixed(2).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        getData(){
            this.CLR_ERRORS();
            this.response = null;
            this.isLoading = true;
            if(this.asalRujukan == 'PCARE') {
                this.rujukanListByNoKartuPcare(this.params).then((response) => {
                    if (response.success == true) {
                        this.listResponse = response.data;
                        this.isLoading = false;
                    }
                    else { 
                        this.isLoading = false;
                        alert(response.message); 
                    }
                })
            }
            else if(this.asalRujukan == 'RS') {
                this.rujukanListByNoKartuRS(this.params).then((response) => {
                    if (response.success == true) {
                        let dt = response.data;
                        console.log(dt);
                        this.response = dt;
                        this.isLoading = false;
                    }
                    else { 
                        this.isLoading = false;
                        alert(response.message); 
                    }
                })
            }
        },

        changeRowRegExpand() {
            this.isExpandedReg = !this.isExpandedReg;
            return false;
        },
    }
}
</script>