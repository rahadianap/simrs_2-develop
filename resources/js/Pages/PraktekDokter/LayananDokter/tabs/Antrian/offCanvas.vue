<template>
    <div ref="offCanvasMedrec" id="canvasMedrec" class="uk-container uk-width-1-2@m" style="height:calc(100vh - 75px); top:70px; right:0; z-index:100; position:absolute; background-color:white;padding:2em;">
        <div class="uk-width-1-1@m">
            <div style="background:white;z-index:100;position:sticky;top:0;width:auto;right:0" class="uk-width-1-1" >    
                <div style="top: 0;">
                    <div style="border-bottom: solid #D4D4D4 1px;width:auto;">
                        <div uk-tooltip="Tutup">
                            <font-awesome-icon :icon="['fas', 'xmark']" size="xl" style="color: #d4d4d4;" @click.prevent="closeCanvasMedrec"/>
                        </div>
                        <h3 style="color: black;" class="offcanvas-title uk-margin-remove-bottom"> {{ canvasTitle }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name : 'canvas-medrec',
    data() {
        return {
            isLoading : false,
            canvasTitle : 'REKAM MEDIS PASIEN',
        }
    },
    props : {

    },

    methods : {
        
        showCanvas(data) {

        },
        closeCanvas(){
            this.$refs.offCanvasMedrec.style.display = "none";
        },
        closeCanvasMedrec(){
            this.$refs.offCanvasMedrec.style.display = "none";
            return false;
        }
    }
}
</script>