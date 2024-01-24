<template>
    <div class="uk-container hims-form-container" style="min-height:50vh;">
        <div class="uk-container" style="background-color:#fff;padding:1em;margin-top:1em;">
            <form action="" @submit.prevent="submitDiet">
                <input v-model="pasien.pasien_diet_id" type="hidden" style="color:black;">
                <div class="uk-grid-small uk-card uk-card-default hims-form-header" uk-grid>
                    <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                        <h5 class="uk-text-uppercase">DATA DIET PASIEN</h5>
                    </div>                                
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border:none;">Tutup</button>                  
                    </div>
                    <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                        <button type="submit" class="uk-button hims-button-primary1 uk-button-default uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                    </div>
                </div>
                <div> 
                    <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                        <div uk-spinner="ratio : 2"></div>
                        <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                    </div>                         
                    <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                        <p>{{ errors.invalid }}</p>
                    </div>              
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA PASIEN</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi utama pasien.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Transaksi ID</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.trx_id" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-2-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Pasien</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.nama_pasien" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tempat Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.tempat_lahir" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Lahir*</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.tgl_lahir" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Umur</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.usia" type="text" disabled style="color:black;">
                                </div>
                            </div>                                
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Unit</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.unit_nama" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Kelas</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.kelas_harga" type="text" disabled style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Dokter</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.dokter_nama" type="text" disabled style="color:black;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DIET</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi diet pasien.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Berlaku</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="date" v-model="pasien.tgl_berlaku" required style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Jam Berlaku</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" type="time" v-model="pasien.jam_berlaku" placeholder="01:00"  required style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Meal Set</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.meal_set" class="uk-select uk-form-small">
                                        <option value="Pagi">Pagi</option>
                                        <option value="Siang">Siang</option>
                                        <option value="Sore">Sore</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Bentuk Makanan</label>
                                <div class="uk-form-controls">
                                    <select v-model="pasien.bentuk_makanan" class="uk-select uk-form-small" style="border:1px solid #cc0202;">
                                        <option v-for="option in bentukMakananRefs.json_data" v-bind:value="option.value" v-bind:key="option.value">{{option.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diagnosa</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.diagnosa" type="text" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" v-model="pasien.catatan" type="text"></textarea>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tinggi Badan(Cm)</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.height" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Berat Badan(Kg)</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.weight" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Index Massa Tubuh</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="pasien.bmi" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-1@m">
                                <div class="uk-form-controls">
                                    <label style="color:black; font-size:14px;">
                                        <input class="uk-checkbox" type="checkbox" value="1" v-model="pasien.is_special"> Pasien dengan kondisi khusus
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-small hims-form-subpage" uk-grid >
                        <div class="uk-width-1-4@s" style="padding:0 0.5em 1em 0.5em;">
                            <h5 style="color:indigo;padding:0;margin:0;">DATA DETAIL MENU DIET</h5>
                            <p style="font-size:12px;font-style:italic;padding:0;margin:0;">
                                informasi menu diet pasien.
                            </p>
                        </div>
                        <div class="uk-width-3-4@m uk-grid-small" uk-grid>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Diet</label>
                                <div class="uk-form-controls">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = dietUrl
                                        label = ""
                                        placeholder = "diet"
                                        captionField = "nama_diet"
                                        valueField = "nama_diet"
                                        :itemListData = dietDesc
                                        :value = "addedDiet.nama_diet"
                                        v-on:item-select = "dietSelected"
                                    ></search-select>
                                </div>
                            </div>
                            <div class="uk-width-1-2@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Menu</label>
                                <div class="uk-form-controls">
                                    <search-select
                                        :config = configSelect
                                        :searchUrl = urlSearch
                                        placeholder = "Menu Diet Pagi...."
                                        captionField = "nama_menu"
                                        valueField = "menu_id"
                                        :itemListData = menuDesc
                                        :value = addedDiet.menu
                                        v-on:item-select = "menuSelected"
                                    ></search-select>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Liquid Food Qty</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.lf_qty" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Liquid Food Schedule</label>
                                <div class="uk-form-controls">
                                    <select class="uk-select uk-form-small" @change="addTag" @keydown.enter='addTag' @keydown.188='addTag' @keydown.delete='removeLastTag'>                
                                        <option v-for="item in addedDiet.schedule" :value="item">{{item}}</option>
                                    </select> 
                                    <div v-for='(tag, index) in tags' :key='tag' class='tag-input__tag'>
                                        {{ tag }}
                                        <span @click='removeTag(index)'>x</span>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Catatan</label>
                                <div class="uk-form-controls">
                                    <textarea class="uk-textarea uk-form-small" v-model="addedDiet.catatan" type="text"></textarea>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Kalori</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.kalori" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_kalori" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_kalori" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_kalori" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Karbo</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.karbo" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_karbo" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_karbo" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_karbo" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Protein</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.protein" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_protein" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_protein" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_protein" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Lemak</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.lemak" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_lemak" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_lemak" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_lemak" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Serat</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.serat" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_serat" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_serat" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_serat" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nilai Garam</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.garam" type="number" style="color:black;">
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Interval</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.int_garam" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Min.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.min_garam" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-4@m">
                                <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Max.</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-small" v-model="addedDiet.max_garam" type="number" style="color:black;" readonly>
                                </div>
                            </div>
                            <div class="uk-width-1-3@m">
                                <div class="uk-form-controls">
                                    <button type="button" @click.prevent="addDiet" class="uk-button-small uk-button-primary uk-box-shadow-small" style="border-radius: 5px; padding:0.2em 0.5em 0.2em 0.5em;">Insert</button>
                                </div>
                            </div>
                        </div>
                        <table class="uk-table hims-table1 uk-table-responsive1 uk-box-shadow-small">
                            <thead>
                                <tr style="border-bottom:1px solid #cc0202;color:white;padding:0.5em;font-weight:500;background-color:#cc0202;">
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Diet ID</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Nama Diet</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Menu</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Liquid Qty</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Liquid Schedule</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Kalori</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Protein</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Lemak</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Karbohidrat</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Garam</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">Serat</th>
                                    <th style="color:white;padding:0.5em;font-size:12px:500;background-color:#cc0202;">...</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="itm in pasien.diets" style="border-top:1px solid #eee;">
                                    <td style="padding:1em;font-weight:500;font-size:11px;color:black;">{{itm.diet_id}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.nama_diet}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.nama_menu}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.lf_qty}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.schedule}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.kalori}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.protein}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.lemak}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.karbo}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.garam}}</td>
                                    <td style="padding:1em;font-size:11px;color:black;">{{itm.serat}}</td>
                                    <td style="padding:0.4em;margin:0;color:black;font-size:12px;text-align:center;">
                                        <button type="button" style="border:none;background-color: white; color:red;"><span uk-icon="minus-circle"></span></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';
import SearchSelect from '@/Components/SearchSelect.vue';
import SearchList from '@/Components/SearchList.vue';

export default {
    name : 'form-diet-pasien', 
    components : {
        SearchSelect, SearchList
    },
    data() {
        return {
            tags: [],
            isLoading : true,
            configSelect : {
                required : false,
                compClass : "uk-width-1-1@m",
                compStyle : "padding:0;margin:0;",
            },

            dietDesc : [
                { colName : 'nama_diet', labelData : '', isTitle : true },
                { colName : 'diet_id', labelData : '', isTitle : false },
            ],

            menuDesc : [
                { colName : 'nama_menu', labelData : '', isTitle : true },
                { colName : 'menu_id', labelData : '', isTitle : false },
            ],

            depoDesc : [
                { colName : 'depo_nama', labelData : '', isTitle : true },
                { colName : 'depo_id', labelData : '', isTitle : false },
            ],

            roleDesc : [
                { colName : 'role_nama', labelData : '', isTitle : true },
                { colName : 'role_id', labelData : '', isTitle : false },
            ],

            isUpdate : true,
            dietUrl : '/diet',
            urlSearch : '',

            addedDiet : {
                diet_id: null,
                nama_diet: null,
                menu_id: null,
                nama_menu: null,
                protein: null,
                kalori: null,
                lemak: null,
                karbo: null,
                garam: null,
                serat: null,
                is_aktif: true,
                schedule: [
                    '00:00', 
                    '01:00',
                    '02:00', 
                    '03:00',
                    '04:00', 
                    '05:00',
                    '06:00', 
                    '07:00',
                    '08:00', 
                    '09:00',
                    '10:00', 
                    '11:00',
                    '12:00', 
                    '13:00',
                    '14:00', 
                    '15:00',
                    '16:00', 
                    '17:00',
                    '18:00', 
                    '19:00',
                    '20:00', 
                    '21:00',
                    '22:00', 
                    '23:00'
                ]
            },
            addedDepo : {
                user_depo_id : null,
                depo_id : null,
                depo_nama : null,
                is_aktif : true,
            },

            pasien : {
                trx_id : null,
                pasien_id : null,
                nama_pasien : null,
                tempat_lahir : null,
                tgl_lahir : null,
                usia : null,
                unit_id : false,
                unit_nama : null,
                kelas_harga_id : null,
                kelas_harga : null,
                dokter_id : null,
                dokter_nama : null,
                meal_set : null,
                tgl_berlaku : new Date().toISOString().slice(0,10),
                jam_berlaku : this.getTimeNow(),
                is_aktif : false,
                is_special: false,
                bentuk_makanan: null,
                height: 0,
                weight: 0,
                bmi: 0,    
                diets : [],           
            }
        }
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters('refPenunjang',['isRefPenunjangExist', 'bentukMakananRefs']),
    },

    mounted() {
        this.initialize();
    },
    
    methods : {
        ...mapActions('rawatJalan',['dataTransaksiPoli']),
        ...mapActions('dietPasien',['createDiet','updateDiet']),
        ...mapActions('refPenunjang',['listRefPenunjang']),
        ...mapMutations(['CLR_ERRORS']),

        getTimeNow(){
            let currentDate = new Date();
            let hours = currentDate.getHours();
            if(hours < 10){ hours = `0${hours}` };
            let minutes = currentDate.getMinutes();
            if(minutes < 10){ minutes = `0${minutes}` };
            
            let time = hours + ":" + minutes;
            return time;
        },

        initialize() {
            let params = { is_aktif:true, per_page:'ALL' };
            this.listRefPenunjang(params);
        },

        addTag(event) {
            event.preventDefault();
            let val = event.target.value.trim();
            if (val.length > 0) {
            if (this.tags.length >= 1) {
                for (let i = 0; i < this.tags.length; i++) {
                if (this.tags[i] === val) {
                    return false;
                }
                }
            }
            this.tags.push(val);
            event.target.value = [];
            }
        },
        removeTag(index) {
            this.tags.splice(index, 1);
            this.addedDiet.schedule = [
                '00:00', 
                '01:00',
                '02:00', 
                '03:00',
                '04:00', 
                '05:00',
                '06:00', 
                '07:00',
                '08:00', 
                '09:00',
                '10:00', 
                '11:00',
                '12:00', 
                '13:00',
                '14:00', 
                '15:00',
                '16:00', 
                '17:00',
                '18:00', 
                '19:00',
                '20:00', 
                '21:00',
                '22:00', 
                '23:00'
            ];
        },
        removeLastTag(event) {
            if (event.target.value.length === 0) {
            this.removeTag(this.tags.length - 1);
            this.addedDiet.schedule = [
                '00:00', 
                '01:00',
                '02:00', 
                '03:00',
                '04:00', 
                '05:00',
                '06:00', 
                '07:00',
                '08:00', 
                '09:00',
                '10:00', 
                '11:00',
                '12:00', 
                '13:00',
                '14:00', 
                '15:00',
                '16:00', 
                '17:00',
                '18:00', 
                '19:00',
                '20:00', 
                '21:00',
                '22:00', 
                '23:00'
            ];
            }
        },

        closeModal(){
            this.clearPengguna();
            this.$emit('closed',true);
        },

        submitDiet(){            
            this.CLR_ERRORS();
            if(this.isUpdate == false){
                this.createDiet(this.pasien).then((response) => {
                    if (response.success == true) {
                        alert("Data diet baru berhasil dibuat.");
                        this.fillDataPasien(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                });
            } else {
                console.log(this.pasien);
                this.updateDiet(this.pasien).then((response) => {
                    if (response.success == true) {
                        alert("Data diet berhasil diubah.");
                        this.fillDataPasien(response.data);
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                    }
                });
            }
            
        },

        newDiet(data){
            this.clearPengguna();
            this.isLoading = true;
            this.isUpdate = false;
            this.dataTransaksiPoli(data.trx_id).then((response)=>{
                if (response.success == true) {
                    this.fillDataPasien(response.data);
                    this.pasien.diets = [];
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },

        editDiet(data){
            this.clearPengguna();
            this.isLoading = true;
            this.isUpdate = true;
            this.dataTransaksiPoli(data.trx_id).then((response)=>{
                if (response.success == true) {
                    this.fillDataPasien(response.data);
                    this.fillDietSchedule(data);
                    this.fillDietDetail(data);
                    this.isUpdate = true;
                } 
                else { alert(response.message); }
                this.isLoading = false;
            })
        },   

        clearPengguna(){
            this.pasien.trx_id = null;
            this.pasien.pasien_id = null;
            this.pasien.nama_pasien = null;
            this.pasien.tempat_lahir = null;
            this.pasien.tgl_lahir = null;
            this.pasien.usia = null;
            this.pasien.unit_id = false;
            this.pasien.unit_nama = null;
            this.pasien.kelas_harga_id = null;
            this.pasien.kelas_harga = null;
            this.pasien.dokter_id = null;
            this.pasien.dokter_nama = null;
            this.pasien.is_aktif = null;    
            this.pasien.diets = [];
        },

        fillDataPasien(data){
            this.pasien.trx_id = data.trx_id;
            this.pasien.pasien_id = data.pasien_id;
            this.pasien.nama_pasien = data.nama_pasien;
            this.pasien.tempat_lahir = data.tempat_lahir;
            this.pasien.tgl_lahir = data.tgl_lahir;
            this.pasien.usia = data.usia;
            this.pasien.unit_id = data.unit_id;
            this.pasien.unit_nama = data.unit_nama;
            this.pasien.kelas_harga_id = data.kelas_harga_id;
            this.pasien.kelas_harga = data.kelas_harga;
            this.pasien.dokter_id = data.dokter_id;
            this.pasien.dokter_nama = data.dokter_nama;
            // this.pasien.diets = data.diets;
        },
        
        fillDietDetail(data){
            this.pasien.pasien_diet_id = data.pasien_diet_id;
            this.pasien.tgl_berlaku = data.start_date;
            this.pasien.jam_berlaku = data.start_time;
            this.pasien.meal_set = data.meal_set;
            this.pasien.bentuk_makanan = data.bentuk_makanan;
            this.pasien.diagnosa = data.diagnosa;
            this.pasien.catatan = data.catatan;
            this.pasien.height = data.tinggi_badan;
            this.pasien.weight = data.berat_badan;
            this.pasien.bmi = data.bmi;
            this.pasien.is_special = data.is_special;
        },

        fillDietSchedule(data){
            let myArray = [];
            for(let i = 0; i < data.detail_diet.length; i++){
                myArray.push({
                    "diet_id"           : data.detail_diet[i]['diet_id'],
                    "diet_detail_id"    : data.detail_diet[i]['pasien_diet_detail_id'],
                    "nama_diet"         : data.detail_diet[i]['nama_diet'],
                    "menu_id"           : data.detail_diet[i]['menu_makanan_id'],
                    "nama_menu"         : data.detail_diet[i]['nama_menu'],
                    "lf_qty"            : data.detail_diet[i]['qty'],
                    "protein"           : data.detail_diet[i]['nilai_protein'],
                    "kalori"            : data.detail_diet[i]['nilai_kalori'],
                    "lemak"             : data.detail_diet[i]['nilai_lemak'],
                    "karbo"             : data.detail_diet[i]['nilai_karbo'],
                    "garam"             : data.detail_diet[i]['nilai_garam'],
                    "serat"             : data.detail_diet[i]['nilai_serat'],
                    "schedule"          : data.detail_diet[i]['schedule'],
                });
            }
            this.pasien.diets = myArray;
        },
        
        dietSelected(data) {
            this.addedDiet.diet_id = null;
            this.addedDiet.nama_diet = null;
            if(data) {
                this.addedDiet.diet_id = data.diet_id;
                this.addedDiet.nama_diet = data.nama_diet;
                this.addedDiet.protein = data.nilai_protein;
                this.addedDiet.int_protein = data.int_protein;
                this.addedDiet.min_protein = data.min_protein;
                this.addedDiet.max_protein = data.max_protein;
                this.addedDiet.kalori = data.nilai_kalori;
                this.addedDiet.int_kalori = data.int_kalori;
                this.addedDiet.min_kalori = data.min_kalori;
                this.addedDiet.max_kalori = data.max_kalori;
                this.addedDiet.karbo = data.nilai_karbo;
                this.addedDiet.int_karbo = data.int_karbo;
                this.addedDiet.min_karbo = data.min_karbo;
                this.addedDiet.max_karbo = data.max_karbo;
                this.addedDiet.lemak = data.nilai_lemak;
                this.addedDiet.int_lemak = data.int_lemak;
                this.addedDiet.min_lemak = data.min_lemak;
                this.addedDiet.max_lemak = data.max_lemak;
                this.addedDiet.serat = data.nilai_serat;
                this.addedDiet.int_serat = data.int_serat;
                this.addedDiet.min_serat = data.min_serat;
                this.addedDiet.max_serat = data.max_serat;
                this.addedDiet.garam = data.nilai_garam;
                this.addedDiet.int_garam = data.int_garam;
                this.addedDiet.min_garam = data.min_garam;
                this.addedDiet.max_garam = data.max_garam;
                this.urlSearch = `/diet/${data.diet_id}/menu`;   
            }
        },

        menuSelected(data) {
            if(data){
                this.addedDiet.menu_id = data.menu_id;
                this.addedDiet.nama_menu = data.nama_menu;
            }
        },

        addDiet() {
            if(this.addedDiet.nama_diet == "" || this.addedDiet.nama_diet == null) {
                alert("Data tidak dapat ditambahkan. ada data yang masih kurang.");
                return false;
            }

            const findItem = this.pasien.diets.find(item => item.nama_diet == this.addedDiet.nama_diet)
            if(findItem) {
                alert("Data sudah ada dalam list.");
                return false;
            }

            if(this.addedDiet.lf_qty != this.tags.length) {
                alert("Jumlah liquid Qty harus sama dengan schedule.");
                return false;
            }

            if(this.addedDiet.kalori > this.addedDiet.max_kalori) {
                alert("Nilai Kalori tidak boleh melebihi nilai maksimal yang ditetapkan.");
                return false;
            }
            this.addedDiet.schedule = this.tags;
            let addedVal = JSON.parse(JSON.stringify(this.addedDiet));
            this.pasien.diets.push(addedVal);
            this.clearAddedDiet();
        },

        clearAddedDiet() {
            this.addedDiet.diet_id = null;
            this.addedDiet.nama_diet = null;
            this.addedDiet.menu = null;
            this.addedDiet.lf_qty = null;
            this.addedDiet.catatan = null;
            this.tags = [];
            this.addedDiet.schedule = [
                '00:00', 
                '01:00',
                '02:00', 
                '03:00',
                '04:00', 
                '05:00',
                '06:00', 
                '07:00',
                '08:00', 
                '09:00',
                '10:00', 
                '11:00',
                '12:00', 
                '13:00',
                '14:00', 
                '15:00',
                '16:00', 
                '17:00',
                '18:00', 
                '19:00',
                '20:00', 
                '21:00',
                '22:00', 
                '23:00'
            ];
            this.addedDiet.is_aktif = true;
        },
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400&display=swap');
  
  .container{
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width:100%;
    height:100vh;
    background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(102,126,234,1) 50%, rgba(69,252,250,1) 100%);
  }
  
  a {
  position: absolute;
  right: 15px;
  bottom: 15px;
  font-weight: bold;
  text-decoration: none;
  color: #00003a;
  font-size: 20px;
}
  
  
/*tag input style*/
  
  .tag-input {
    width: 50%;
    border: 1px solid #D9DFE7;
    background: #fff;
    border-radius: 4px;
    font-size: 0.9em;
    min-height: 45px;
    box-sizing: border-box;
    padding: 0 10px;
    font-family: "Roboto";
    margin-bottom: 10px;
  }

  .tag-input__tag {
    height: 24px;
    color: white;
    float: left;
    font-size: 14px;
    margin-right: 10px;
    background-color: #667EEA;
    border-radius: 15px;
    margin-top: 10px;
    line-height: 24px;
    padding: 0 8px;
    font-family: "Roboto";
  }

  .tag-input__tag > span {
    cursor: pointer;
    opacity: 0.75;
    display: inline-block;
    margin-left: 8px;
  }

  .tag-input__text {
    border: none;
    outline: none;
    font-size: 1em;
  line-height: 40px;
  background: none;
  }

</style>