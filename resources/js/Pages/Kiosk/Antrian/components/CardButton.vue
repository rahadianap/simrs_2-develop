<template>
    <div class="uk-card-default card-button uk-grid-small" uk-grid>
        <div class="uk-width-auto" style="background-color: #ccc;width:50px;padding-top:0.2em;">
            <h1>{{ cardNo }}</h1>
        </div>
        <div class="uk-width-expand">
            <a href="#" @click.prevent="fnCallback" class="card-button-text">
                <p class="card-button-title">{{ cardTitle }}</p>
                <p class="card-button-subtitle">{{ cardSubTitle }}</p>
            </a>
        </div>
    </div>

</template>
<script>
export default {
    name : 'card-button',
    props : {
        fnCallback : { type:Function, required:false, default:null },
        cardNo : { type:String, required:true },
        cardTitle : { type:String, required:true },
        cardSubTitle : { type:String, required:false },
    },
    methods : {
    }
}
</script>

<style>

</style>