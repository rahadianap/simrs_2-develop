<template>
    <div class="uk-grid-small hims-form-subpage" uk-grid >                                    
        <div class="uk-width-1-1@s uk-grid-small" uk-grid>
            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_unit_kiosk" @change="updateOtorisasi"> Kiosk Pendaftaran
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">tampilkan unit di kiosk pendaftaran mandiri.</p>
            </div>
            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_unit_external" @change="updateOtorisasi"> Aplikasi eksternal
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">tampilkan unit di aplikasi lain (mobile app, bridging, dsb).</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_registrasi" @change="updateOtorisasi"> Pendaftaran Pasien
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat melakukan pendaftaran pasien.</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_rm_baru" @change="updateOtorisasi"> Pasien Baru
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat mendaftarkan pasien baru.</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_unit_rujukan" @change="updateOtorisasi"> Unit Rujukan
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat menjadi rujukan unit lain.</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_bedah" @change="updateOtorisasi"> Jadwal Operasi
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Masukkan di pilihan unit untuk jadwal operasi.</p>
            </div>

            <!-- <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_stock_adjustment" @change="updateOtorisasi"> Stock Adjustment
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit boleh melakukan penyesuaian stok item persediaan.</p>
            </div> -->

            <!-- <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_inventory_issue" @change="updateOtorisasi"> Inventory Issue
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit boleh melakukan pengeluaran item persediaan.</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_distribusi" @change="updateOtorisasi"> Distribusi Persediaan
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat melakukan proses distribusi persediaan.</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_purchase_req" @change="updateOtorisasi"> Purchase Request
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat mengajukan pembelian.</p>
            </div>
            
            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_purchase_order" @change="updateOtorisasi"> Purchase Order
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat mengeluarkan PO (pembelian)</p>
            </div> -->

            <!-- <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_purchase_receive" @change="updateOtorisasi"> Purchase Receive
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat melakukan penerimaan item PO</p>
            </div>

            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_purchase_return" @change="updateOtorisasi"> Pengembalian Penerimaan PO
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat mengembalikan penerimaan PO</p>
            </div> -->
            <!-- <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_direct_purchase" @change="updateOtorisasi"> Pembelian langsung
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit dapat melakukan pembelian non PO</p>
            </div>
            <div class="uk-width-1-3@m" style="margin:0;padding:0.2em 0.2em 0.2em 1.2em;">
                <div class="uk-form-controls">
                    <label style="color:black; font-size:14px;">
                        <input class="uk-checkbox" type="checkbox" value="1" v-model="otorisasi.is_produksi" @change="updateOtorisasi"> Produksi persediaan
                    </label>
                </div>
                <p style="font-size:11px; color:black;font-style:italic;padding:0.2em 0.2em 0.2em 2em;margin:0;">Unit boleh melakukan produksi persediaan</p>
            </div> -->
        </div>
    </div>
</template>
<script>
export default {
    name : 'sub-form-otorisasi',
    props : {
        otorisasi : { type:Object,required:false }
    },
    data() {
        return {
        }
    },
    methods : {
        updateOtorisasi(){
            this.$emit('update',this.otorisasi);
        },
    }
}
</script>