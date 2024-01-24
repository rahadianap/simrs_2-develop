<template>
    <div style="padding:0.5em;">
        <h3 style="font-weight:500;margin:0;padding:0;">{{title}}</h3>
        <p style="margin:0;padding:0;font-size:12px;"></p>
    </div>
</template>

<script>

export default{
    props  : {
        title : { type:String , required:false, default:'Header Page' }
    }
}
</script>
