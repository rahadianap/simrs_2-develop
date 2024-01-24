<template>
    <div>
        <div>
            <div class="uk-grid-small" uk-grid style="padding:1em;">
                <div class="uk-width-expand@m" style="padding:0;margin:0;">
                    <h4 class="dash-title" style="margin:0;padding:0;font-size:18px;font-weight:500;color:#333;">Undangan Bergabung</h4>
                    <p style="margin:0;padding:0;font-size:12px;font-weight:400;color:#333;font-style: italic;">daftar undangan bergabung untuk anda.</p>
                </div>
            </div>        
        </div>
        <div  v-if="isLoading" class="uk-width-1-1@m">
            <div class="uk-text-align-center" style="width:100%; text-align:left;padding:1em;" >
                <div uk-spinner="ratio : 1"></div>
                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
            </div>
        </div>

        <div v-if="invitationLists.length > 0" class="uk-grid-small" uk-grid>
            <div v-for="client in invitationLists" :key="client.client_id" class="uk-width-1-3@l uk-width-1-2@m" style="padding:0.5em;">
                <div class="uk-card uk-card-default uk-card-hover" style="padding:0.5em;border-radius:10px;min-height:120px;">
                    <div class=" uk-grid-small" uk-grid style="padding:1em 0.5em 0.2em 1em;">
                        <div v-if="client.url_logo == null || client.url_logo == '' || client.url_logo == 'noimage' " class="uk-width-auto@m" style="padding:0.5em;width:100px;">
                            <img class="wps-card-logo" src="@/Assets/noimage.png" alt="" style="">
                        </div>
                        <div v-else class="uk-width-auto@s" style="padding:0.5em;width:100px;">
                            <img class="wps-card-logo" :src="client.url_logo" alt="" style="">
                        </div>
                        <div class="uk-width-expand@s">
                            <div class="wps-card-body">
                                <div class="uk-grid-small" uk-grid>
                                    <div class="uk-width-expand">
                                        <h4 class="uk-card-title wps-card-title uk-text-truncate">
                                            <a href="#" @click.prevent="showWorkplace(client)" title="klik untuk memilih">{{client.client_nama}}</a>
                                        </h4>
                                    </div>
                                </div>
                                <p style="margin:0 0 1em 0; padding:0;font-size:12px;">
                                    <span style="font-size:10px;" class="uk-label uk-label-default">{{client.client_tipe}}</span>
                                </p>
                                <p class="wps-card-description">
                                    {{client.deskripsi}}
                                </p>                            
                            </div>
                        </div>
                    </div>                        
                    <div class="uk-width-1-1 uk-grid-small" uk-grid style="padding:0 0.5em 0 0.5em; text-align: center;">
                        <div style="text-align:center;width:100%;">
                            <button type="button" style="margin-right:5px;width:80px;" @click.prevent="showWorkplace(client)">INFO</button>
                            <button type="button" style="margin-right:5px;width:80px;" @click.prevent="receiveInvitation(client)">TERIMA</button>
                            <button type="button" style="margin-right:5px;width:80px;" @click.prevent="unacceptInvitation(client)">TOLAK</button>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else style="background-color: #ccc; padding:1em;border-radius: 10px;">
            <h5 style="font-style: italic;padding:0;margin:0;">Tidak (belum) ada undangan bergabung.</h5>
        </div> 
        <info-workplace ref="infoWorkplace"/>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';
import InfoWorkplace from '@/Pages/Invitations/InfoWorkplace.vue'

export default {
    name : 'invitations',
    components : {
        InfoWorkplace,
    },
    data() {
        return {
            isLoading : false,
            invitationLists : [],
        }
    },
    computed: {
        ...mapState(['errors'])
    },
    
    methods : {
        ...mapActions('invitation',['myInvitations','dataInvitation','acceptInvitation','rejectInvitation']),
        ...mapMutations(['CLR_ERRORS']),

        retrieveMyInvitation(){
            this.isLoading = true;
            this.myInvitations().then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    this.invitationLists = response.data;
                }
                else { 
                    alert(response.message); 
                }
            })
        },

        showWorkplace(data){
            if(data) {
                this.$refs.infoWorkplace.showWorkplace(data.client_id);
            }
        },

        receiveInvitation(data){
            if(data) {
                if(confirm(`Anda akan menerima undangan bergabung dari ${data.client_nama}. Lanjutkan ?`)){
                    let dt = { 'user_lists' : this.selectedUser };
                    this.acceptInvitation(data.invitation_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.$emit('invitationListChange',true);
                            this.retrieveMyInvitation();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                }
            }
        },

        unacceptInvitation(data){
            if(data) {
                if(confirm(`Anda akan menolak undangan bergabung dari ${data.client_nama}. Lanjutkan ?`)){
                    this.rejectInvitation(data.invitation_id).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.retrieveMyInvitation();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                }
            }
        },
    }
}
</script>

<style>

.wps-button {
    font-weight:bold;
    color:#333;
    border-radius:5px;
    background-color: whitesmoke;
}

.wps-button:hover {
    color:white;
    background-color:#39f;
}

.card {
  box-sizing: border-box;
  padding: 1em;
  border-radius: 10px;
  color: white;
  box-shadow: 5px 5px 10px #ccc;
  float: left;
  background: linear-gradient(315deg, #045de9 0%, #09c6f9 74%);
}
/* 
.card.purple {
  background: linear-gradient(150deg, #f731db, #4600f1 100%);
}

.card.green {
  background: linear-gradient(150deg, #39ef74, #4600f1 100%);
}

.card.blue {
  background: linear-gradient(315deg, #045de9 0%, #09c6f9 74%);
}

.card.bluegreen {
    background : linear-gradient(315deg, #20bf55 0%, #01baef 74%);
} */

.card-button {
  text-transform: uppercase;
  width: 150px;
  display: block;
  margin: 0;
  color: #4600f1;
  border-radius: 50px;
  padding:0.5em;
  background: white;
  border: none;
  font-weight: bold;
  margin-top: 1.5em;
}

</style>