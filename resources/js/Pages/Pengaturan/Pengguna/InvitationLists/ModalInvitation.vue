<template>
    <div class="uk-modal uk-modal-container" uk-modal="bg-close:false;" id="modalInvitation" style="min-height:50vh;">
        <div class="uk-modal-dialog uk-modal-body">
            <button class="uk-modal-close-default" type="button" uk-close @click.prevent=""></button>
            <div class="hims-form-container" style="padding:0; margin:0;">
                <form action="" @submit.prevent="submitInvitation">
                    <h3 class="uk-text-uppercase" style="font-weight:bold;">Undangan Bergabung</h3>
                    <div class="uk-grid-small" uk-grid style="border-top:1px solid #cc0202;padding:0.1em;">
                        <div class="uk-width-1-1@m">
                            <div class="uk-inline uk-width-1-1" style="margin-top:0.5em;">
                                <a class="uk-form-icon uk-form-icon-flip" href="#" uk-icon="icon: search" @click.prevent="retrieveUser" style="color:black; background-color:white;border:1px solid #aaa;"></a>
                                <input class="uk-input uk-form-small" v-model="keywordText" 
                                    type="text" aria-label="email atau username pengguna..." placeholder="email atau username pengguna..." 
                                    style="color:black;">
                            </div>
                        </div>
                        <div class="uk-width-1-1@m">
                            <div v-if="isLoading" class="uk-text-align-center" style="width:100%; text-align:center;padding:1em;" >
                                <div uk-spinner="ratio : 1"></div>
                                <span style="margin-left:10px;font-size:14px; color:#333; font-weight:500;">Sedang mengambil data...</span>
                            </div>
                        </div>
                        <div v-for="dt in searchLists" class="uk-width-1-3@m">
                            <div class="uk-card-default uk-grid-small" uk-grid style="padding:1em 0.5em 1em 0.5em;margin:0;">
                                <div class="uk-width-auto" style="width:40px;padding-top:0.6em;">
                                    <img class="uk-border-circle" :src="dt.avatar" alt="" uk-img>
                                </div>
                                <div class="uk-width-expand">
                                    <a style="font-size:12px; color:black; font-weight:500; padding:0;margin:0;" href="#">{{dt.username}}</a>
                                    <p style="font-size:12px; font-weight:400; padding:0;margin:0;">{{dt.email}}</p>
                                </div>
                                <div class="uk-width-auto" style="padding-top:0.5em;">
                                    <input class="uk-checkbox" type="checkbox" v-model="dt.is_selected" style="margin-left:5px;border:1px solid black;" @change="addUserToInvite(dt)">
                                </div>                                
                            </div>
                        </div>

                        <div class="uk-width-1-1@m"></div>
                        
                                                
                        <div class="uk-width-1-1" style="text-align:right;margin-top:1.5em;">
                            <button type="submit" class="uk-button hims-button-primary uk-button-primary uk-button-medium uk-box-shadow-large" style="font-size:12px;">Undang</button>
                        </div>

                        <div v-for="sel in selectedUser" class="uk-width-1-1@m">
                            <p style="font-size:11px;"><span style="font-weight: 500;">{{sel.username}}</span> {{sel.email}}</p>
                        </div>
                    </div>     
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex';

export default {
    name : 'modal-invitation',
    data() {
        return {
            isLoading : false,
            keywordText : null,
            searchLists : null,
            selectedUser : [],
        }
    },
    computed: {
        ...mapState(['errors'])
    },
    methods : {
        ...mapActions('invitation',['listAllUser','createInvitation']),
        ...mapMutations(['CLR_ERRORS']),

        invite(){            
            UIkit.modal('#modalInvitation').show();
            this.retrieveUser();
        },

        retrieveUser(){
            this.isLoading = true;
            let param = {'keyword' : this.keywordText };
            this.listAllUser(param).then((response) => {
                this.isLoading = false;
                if (response.success == true) {
                    this.searchLists = response.data.data;
                }
                else { 
                    alert(response.message); 
                }
            })
        },

        addUserToInvite() {
            this.selectedUser = this.searchLists.filter(item => { return item['is_selected'] == true });
        },

        submitInvitation(){
            this.CLR_ERRORS();
            if(this.selectedUser.length <= 0) { return false; }
            
            else {
                if(confirm(`Buat undangan kepada user terpilih ?`)){
                    let dt = { 'user_lists' : this.selectedUser };
                    this.createInvitation(dt).then((response) => {
                        if (response.success == true) {
                            alert(response.message);
                            this.closeModalInvitation();
                        }
                        else { 
                            alert (response.message); 
                        }
                    });
                };
            }
            
        },

        closeModalInvitation(){
            UIkit.modal('#modalInvitation').hide();
            this.$emit('modalInvitationClosed',true);
        }
    }
}
</script>