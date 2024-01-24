<template>
    <thead>
        <tr>
            <th></th>
            <th v-for="hd in headers">{{ hd.title }}</th>
        </tr>
    </thead>
</template>

<script>
export default {
    name : 'base-header',
    props : {
        headers : { type:Array, required:false, default:[] },
        isExpandable : {type:Boolean, required:false, default: false }
    }
}
</script>

