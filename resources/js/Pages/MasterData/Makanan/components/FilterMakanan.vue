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
        <label class="uk-form-label">Nama Makanan</label>            
        <div class="uk-form-controls">
            <input class="uk-input uk-form-small" type="text" name="keyword" title="nama makanan">
        </div>
    </div>
    <div>
        <label class="uk-form-label">Kelompok</label>            
        <div class="uk-form-controls">
            <input class="uk-input uk-form-small" type="text" name="keyword" title="kelompok makanan">
        </div>
    </div>
    <div>
        <label class="uk-form-label">Jenis Makanan</label>            
        <div class="uk-form-controls">
            <input class="uk-input uk-form-small" type="text" name="keyword" title="jenis makanan">
        </div>
    </div>
</template>

<script>
export default {    
    name : 'filter-makanan',
}
</script>