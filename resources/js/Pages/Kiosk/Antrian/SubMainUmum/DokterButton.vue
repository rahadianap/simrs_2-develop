<template>
    <div class="uk-card-default dokter-button">
        <div class="uk-grid-small uk-flex-middle" uk-grid>
            <div class="uk-width-auto">
                <img class="uk-border-circle1" width="40" height="40" :src="data.url_avatar" alt="Avatar">
            </div>
            <div class="uk-width-expand">
                <a href="#" @click.prevent="fnCallback(data)" class="dokter-button-text">
                    <p class="dokter-button-title">{{ data.dokter_nama }}</p>
                    <p class="dokter-button-unit">{{ data.unit_nama }}</p>
                    <p class="dokter-button-subtitle">{{ data.mulai }} - {{ data.selesai }}</p>
                </a>
            </div>
        </div>
        
    </div>

</template>
<script>
export default {
    name : 'dokter-button',
    props : {
        fnCallback : { type:Function, required:false, default:null },        
        data : { type:Object, required:true },
    },
    methods : {
    }
}
</script>

<style>

.dokter-button {
    padding : 1em;
}

a.dokter-button-text {
    display:block; 
    text-decoration:none;
    cursor: pointer; 
    color:#000;
}

.dokter-button-title {
    padding:0;
    margin:0;
    font-size: 18px;
    font-weight: 500;
}

.dokter-button-subtitle {
    padding:0;
    margin:0;
    font-size: 12px;
    color:#000;
}
.dokter-button-unit {
    padding:0;
    margin:0;
    font-size: 12px;
    color:#000;
}


div.dokter-button:hover {
    background-color: #cc0202;
}

div.dokter-button:hover > div > div > a > p.dokter-button-subtitle {
    color: #fff;
}

div.dokter-button:hover > div > div > a > p.dokter-button-title {
    color: #fff;
}
div.dokter-button:hover > div > div > a > p.dokter-button-unit {
    color: #fff;
}
</style>