<template>
    <tr style="border-bottom:1px solid #ccc;">
        <td style="width:40px;padding:1em; font-weight: 500; font-size: 12px; color:black;">
            <a v-if="isExpandedReg" href="#" uk-icon="icon:minus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
            <a v-else href="#" uk-icon="icon:plus-circle;ratio:1;" @click.prevent="changeRowRegExpand"></a>
        </td>
        <td style="padding:1em; font-weight: 500; font-size: 12px; color:black;">
            Rujukan Berdasarkan Nomor Rujukan
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
                    <div v-if="response !== null">
                        <table class="uk-table uk-table-striped child-table tb-hasil-lab" style="padding:0;margin:0;">
                            <tr><td colspan="2" style="color:black; font-size: 14px; text-align: left; font-weight: 500;">KUNJUNGAN</td></tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Nomor</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.noKunjungan }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Tanggal</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.tglKunjungan }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Keluhan</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.keluhan }}</td>
                            </tr>

                            <tr><td colspan="2" style="color:black; font-size: 14px; text-align: left; font-weight: 500;">PESERTA</td></tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Nama</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.nama }} ({{ response.peserta.sex }})</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">NIK</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.nik }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">NOKA</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.noKartu }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">MR</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.mr.noMR }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Telepon</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.mr.noTelepon }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">PISA</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.pisa }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Jenis</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.jenisPeserta.kode }} - {{ response.peserta.jenisPeserta.keterangan }}</td>
                            </tr>

                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Hak Kelas</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.hakKelas.kode }} - {{ response.peserta.hakKelas.keterangan }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Status</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.statusPeserta.kode }} - {{ response.peserta.statusPeserta.keterangan }}</td>
                            </tr>

                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Prov Umum</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.provUmum.kdProvider }} - {{ response.peserta.provUmum.nmProvider }}</td>
                            </tr>

                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Umur Sekarang</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.umur.umurSekarang }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Umur Pelayanan</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.umur.umurSaatPelayanan }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Tgl.Cetak Kartu</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.tglCetakKartu }}</td> 
                            </tr>
                            
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Tgl.Lahir</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.tglLahir }}</td>
                            </tr>
                                    
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Tgl.TAT</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.tglTAT }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Tgl.TMT</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.peserta.tglTMT }}</td>
                            </tr>
                            

                            <tr><td colspan="2" style="color:black; font-size: 14px; text-align: left; font-weight: 500;">DIAGNOSA</td></tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Kode</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.diagnosa.kode }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Nama</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.diagnosa.nama }}</td>
                            </tr>

                            <tr><td colspan="2" style="color:black; font-size: 14px; text-align: left; font-weight: 500;">PELAYANAN</td></tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Jenis</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.pelayanan.kode }} - {{ response.pelayanan.nama }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Rujukan</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.poliRujukan.kode }} - {{ response.poliRujukan.nama }}</td>
                            </tr>
                            <tr>
                                <td style="margin:0;padding:0.5em 0.5em 0.5em 2em;font-size:12px;font-weight: 500;color:black;">Perujuk</td>
                                <td style="margin:0;padding:0.5em;font-size:12px;color:black;">{{ response.provPerujuk.kode }} - {{ response.provPerujuk.nama }}</td>
                            </tr>                            
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
    name : 'search-no-rujukan',
    computed : {
        ...mapGetters(['errors']),
    },
    data() {
        return {
            isLoading : false,
            isExpandedReg : false,
            selectAll : true,
            buffer : [],
            params : null,
            asalRujukan : 'PCARE',
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
                this.rujukanByNoRujukanPcare(this.params).then((response) => {
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
            else if(this.asalRujukan == 'RS') {
                this.rujukanByNoRujukanRS(this.params).then((response) => {
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