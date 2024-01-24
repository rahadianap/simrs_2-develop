<template>
    <div>
        Medical record data transaksi
        {{ regId }}
    </div>
</template>

<script>
export default {
    props: {
        regId : {type:String, required:true }
    }
}
</script>