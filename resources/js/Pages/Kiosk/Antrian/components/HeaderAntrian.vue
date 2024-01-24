<template>
    <div class="uk-container uk-container-xlarge">
        <p class="main-text">{{ mainText }}</p>
        <p class="sub-main-text">{{ subMainText }}</p>
        <p  class="tag-line">{{ tagLine }}</p>
    </div>
</template>

<script>
export default {
    name : 'header-antrian',
    props : {
        mainText : { type:String, required:true, default:'YOUR NAME' },
        subMainText : { type:String, required:false, default:null },
        tagLine : { type:String, required:false, default:null },        
    },

    methods : {
        
    }
}
</script>
<style>

</style>