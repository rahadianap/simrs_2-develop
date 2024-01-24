<template>
    <div class="uk-container uk-container-large">
        <div style="margin:0.5em 0.5em 1em 0.5em;">
            <workplaces ref="workplaceLists"/>
        </div>
        <div style="margin:2em 0.5em 1em 0.5em;">
            <invitations ref="invitationLists" v-on:invitationListChange="refreshHome"></invitations>
        </div>
        <div style="margin:2em 0.5em 1em 0.5em;">
            <activities ref="activityLists"/>
        </div>
    </div>

</template>

<script>
import headerpage from '@/Pages/HeaderPage.vue'
import workplaces from '@/Pages/Workplace/index.vue'
import activities from '@/Pages/UserActivities/index.vue'
import invitations from '@/Pages/Invitations/index.vue'

export default {
    data() {
        return {
           title : 'Dashboard Pengguna',
        }
    },
    components : {
        headerpage,
        workplaces,
        activities,
        invitations,
    },
    mounted(){
        this.init();
    },
    methods :{
        init(){
            this.$refs.invitationLists.retrieveMyInvitation();
        },

        refreshHome(){
            this.$refs.workplaceLists.getListWorkplace();
        },
    }
}
</script>