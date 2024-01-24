<template>
    <div>
        <h4 style="font-weight:500;margin:0;padding:0;" class="uk-text-uppercase">{{title}}</h4>
        <p style="font-weight:300;margin:0;padding:0;font-size:12px;font-style:italic;">{{subTitle}}</p>
    </div>
</template>
<script>
export default {
    name : 'header-page',
    props : {
        title : { type:String , required:false, default:'' },
        subTitle : { type:String , required:false, default:'' },        
    }
}
</script>
