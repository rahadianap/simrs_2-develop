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

<style>
.hims-table thead {
    border:none;
    position:sticky;
    top:0;
    z-index:100;
}

.hims-table th {
    position:sticky; 
    padding:0.8em;
    top:0; 
    width: auto; 
    text-align:left;
    white-space: 'pre-wrap';
    font-size: 14px;
    border:none; 
    color: white;
    font-weight: 500;
    /* background-color : #1E90FF;  */
    background-color : #cc0202; 
}

</style>
