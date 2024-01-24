<template>
    <!-- Popup Modal EDIT data maintenance schedule -->
    <div class="uk-modal-container" uk-modal="bg-close:false;" id="editFormSchedule" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body" style="border-radius:10px;">
            <div class="uk-container hims-form-container" style="min-height:50vh; background-color:#fff;">
                <form action="" @submit.prevent="submitSchedule" style="padding:0;" >
                    <div class="uk-grid-small uk-card uk-card-default hims-form-header" style="border-radius:10px;justify-content:center;text-align:center;" uk-grid >
                        <div class="uk-width-expand" style="padding:1em 0.5em 0.1em 1em;margin:0;">
                            <h5 class="uk-text-uppercase">EDIT DATA MAINTENANCE SCHEDULE</h5>
                        </div>                                
                        <!-- <div class="uk-width-auto" style="text-align:right;padding:0.5em;">
                            <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;">Tutup</button>                  
                        </div>
                        <div class="uk-width-auto" style="text-align:right;padding:0.5em;margin-right:1em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Simpan</button>                  
                        </div> -->
                    </div>
                    <div style="margin:0 10px;border-top:2px solid #cc0202;">                  
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div>              
                        <div class="uk-grid-small hims-form-subpage-asset" style="margin:30px 0" uk-grid >
                            <div class="uk-width-1-1@s uk-grid-small" style="margin-left:1px;padding-left:1px;" uk-grid>
                                <!-- nama teknisi -->
                                <div class="uk-width-1-2@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Teknisi*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mschedule.teknisi" type="text" placeholder="masukkan nama teknisi" required>
                                    </div>
                                </div>

                                <!-- tanggal perencanaan -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Tanggal Rencana*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mschedule.tgl_rencana" type="date" placeholder="masukkan tanggal rencana" required>
                                    </div>
                                </div>

                                <!-- nama vendor -->
                                <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Nama Vendor*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mschedule.nama_vendor" :disabled="!disabled" type="text" placeholder="masukkan nama vendor" required>
                                    </div>
                                </div>

                                <!-- status schedule -->
                                <!-- <div class="uk-width-1-3@m" style="padding-left:1px;padding-right: 10px">
                                    <label class="uk-form-label" style="padding:0;margin:0;font-size:12px;">Status Schedule*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-small" v-model="mschedule.status" type="text" placeholder="status schedule" required>
                                    </div>
                                </div> -->

                                <!-- <div class="uk-width-1-1" style="padding:0.5em 1em 0.2em 0.8em;margin:0;">
                                    <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="mschedule.is_aktif" style="margin-left:5px;"> Data Schedule aktif</label>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid-collapse uk-card uk-card-default hims-form-footer uk-column-1-2@m" style="border-radius: 10px;">
                        
                        <!-- checkbox is_aktif & is_vendor -->
                        <div class="uk-column-1-2 uk-width-1-1@s uk-grid-small uk-grid" style="margin-left:2px;" uk-grid>
                            <!-- <div class="uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;"> -->
                                <!-- <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="mschedule.is_aktif" style="margin-left:5px;"> Data Schedule aktif</label> -->
                            <!-- </div> -->
                            <div class="uk-column-1-1@s" style="padding:1em 1em 0.2em 0.8em;margin:0;">
                                <label style="padding:0;margin:0;font-size:12px;"><input class="uk-checkbox" type="checkbox" v-model="mschedule.is_vendor" v-model.trim="disabled" style="margin-left:5px;"> Ditangani oleh Vendor</label>
                            </div>
                        </div>
                        <!-- end checkbox is_aktif & is_vendor -->

                        <div class="uk-grid-match uk-align-center uk-align-right@m uk-column-1-2@s" style="margin-bottom:6px;margin-top:1px">
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="button" @click.prevent="closeModal" class="uk-button uk-button-default uk-button-medium" style="font-size:12px;border-radius:10px;padding:0 85px;">Tutup</button>                  
                            </div>
                            <div class="uk-width-auto" style="padding:0.5em;">
                                <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;border-radius:10px;padding:0 85px;">Simpan</button>                  
                            </div>
                        </div>
                    </div>
                </form>                          
            </div>
        </div>
    </div> 
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex';

export default {
    name : 'edit-form-schedule', 
    // mixins: [Vue2Filters.mixin],

    data() {
        return {
            isUpdate : true,
            mschedule : {
                maint_schedule_id:null,
                client_id:null,
                teknisi:null,
                tgl_rencana:null,
                nama_vendor : null,
                created_by : null,
                updated_by : null,
                status : 'Open',
                is_vendor:true,
                is_aktif:true,
            },
            disabled: false,
            unitData : {
                maint_schedule_id:null,
                client_id:null,
                teknisi:null,
                tgl_rencana:null,
                nama_vendor : null,
                created_by : null,
                updated_by : null,
                status : 'Open',
                is_vendor:true,
                is_aktif:true,
            },

            // status_asset : [
            //     { id:'Terverifikasi', text:'Terverifikasi' },
            //     { id:'Tidak Terverifikasi', text:'Tidak Terverifikasi' },
            //     { id:'Tidak Tahu', text:'Tidak Tahu' },
            // ]
        }
    },
    computed : {
        ...mapGetters(['errors']),
    },

    mounted() {
        // this.createSchedule();
        // this['assets/form-asset']();
    },
    

    methods : {
        ...mapActions('schedule',['createSchedule','updateSchedule','dataSchedule']),
        ...mapMutations(['CLR_ERRORS']),

        closeModal(){
            this.$emit('closed',true);
            UIkit.modal('#editFormSchedule').hide();
            return false;
        },

        submitSchedule(){
            this.CLR_ERRORS();
            console.log(this.isUpdate)
            if(!this.isUpdate) {
                this.createSchedule(this.mschedule).then((response) => {
                    if (response.success == true) {
                        alert("Data Schedule baru berhasil dibuat.");
                        // this.fillSchedule(response.data);
                        this.clearSchedule();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = true;
                        // UIkit.modal('#editFormSchedule').hide();
                    }
                })
            } 
            
            else {
                console.log("Masuk ke update");
                this.updateSchedule(this.schedule).then((response) => {
                    if (response.success == true) {
                        // this.fillSchedule(response.data);
                        alert("Data Schedule berhasil diubah.");
                        // this.$emit('saveSucceed',true);
                        // this.isUpdate = true;
                        this.clearSchedule();
                        this.$emit('saveSucceed',true);
                        this.isUpdate = false;
                        this.closeModal();
                    }
                })
            }            
        },

        newSchedule(){
            this.clearSchedule();
            this.isUpdate = false;
            UIkit.modal('#editFormSchedule').show();
        },

        editSchedule(maint_schedule_id){
                this.clearSchedule();            
                this.dataSchedule(maint_schedule_id).then((response)=>{
                    if (response.success == true) {
                        this.fillSchedule(response.data);
                        this.isUpdate = true;
                        UIkit.modal('#editFormSchedule').show();
                    } else {
                        alert(response.message)
                    }
                })
            
        },   

        clearSchedule(){
            this.mschedule.maint_schedule_id = null;
            this.mschedule.client_id = null;
            this.mschedule.teknisi = null;
            this.mschedule.tgl_rencana = null;
            this.mschedule.nama_vendor = null;
            this.mschedule.created_by = true;
            this.mschedule.updated_by = null;
            this.mschedule.status = 'Open';
            this.mschedule.is_aktif=null;
        },

        fillSchedule(data){
            this.mschedule.maint_schedule_id = data.maint_schedule_id;
            this.mschedule.client_id = data.client_id;
            this.mschedule.teknisi = data.teknisi;
            this.mschedule.tgl_rencana = data.tgl_rencana;
            this.mschedule.nama_vendor = data.nama_vendor;
            this.mschedule.created_by = data.created_by;
            this.mschedule.updated_by = data.updated_by;
            this.mschedule.status = data.status;
            this.mschedule.is_aktif=data.is_aktif;
        },
    }
}
</script>


<style>
/* .uk-input, .uk-textarea, .uk-checkbox {
    border: 1px solid #999;
}

.hims-form-container label {
    color:#333;
    font-size:12x;
}

.hims-button-primary {
    background-color: #cc0202;
    color: #fafafa;
    border:2px solid #cc0202;
}

.uk-button {
    border:2px solid #aaa;
    font-weight:400;
}

.hims-button-primary {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-button-primary:hover {
    background-color: #cc0202;
    color: white;
    border:2px solid #cc0202;
    font-weight:bold;
}

.hims-accordion-title {
    border-bottom:1px solid #cc0202; 
    font-size:14px; 
    font-weight:500; 
    background-color:#cc0202; 
    color:white; 
    padding:0.5em 1em 0.5em 1em;
    border-radius:5px;
}

.hims-accordion-title:hover {
    color:white; 
}
.hims-accordion-title::before {
    color:white;
}

.hims-form-header {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    top:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-header h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}


.hims-form-subpage {
    padding:1em;
    margin:0 0.5em 0.5em 0.5em;
    border-top:1px solid #cc0202;
} */
.hims-form-footer {
    padding:0.5em 0 0 0; 
    margin:1em 0 0 0;
    position:sticky;
    bottom:0;
    background-color:white;
    z-index:99;
    color:#cc0202;
}

.hims-form-footer h5 {
    color:#333;
    font-weight:500;
    font-size:18px;
}
</style>