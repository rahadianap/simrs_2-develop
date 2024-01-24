<template>        
    <div>
        <label class="uk-form-label">Status</label>
        <div class="uk-form-controls">
            <select class="uk-select uk-form-small" name="is_aktif" title="status">
                <option value="true">Aktif</option>
                <option value="false">Nonaktif</option>
                <option value=""></option>
            </select>
        </div>
    </div>    
    <div>
        <label class="uk-form-label">Nama Pemasok/Pabrikan</label>            
        <div class="uk-form-controls">
            <input class="uk-input uk-form-small" type="text" name="vendor_nama" title="nama pemasok">
        </div>
    </div>
</template>

<script>
export default {    
    name : 'pemasok-form-filter',
}
</script>