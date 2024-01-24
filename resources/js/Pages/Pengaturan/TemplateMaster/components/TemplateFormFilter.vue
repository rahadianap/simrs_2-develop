<template>
  <div>
    <label class="uk-form-label">Nama Template</label>            
    <div class="uk-form-controls">
      <input class="uk-input uk-form-small" type="text" name="template_nama" title="template">
    </div>
  </div>
  <div>
    <label class="uk-form-label">Status</label>
    <div class="uk-form-controls">
      <select class="uk-select uk-form-small" name="is_aktif" title="status">
        <option value=""></option>
        <option value="true">Aktif</option>
        <option value="false">Nonaktif</option>
      </select>
    </div>
  </div>    
</template>

<script>
export default {    
  name : 'template-form-filter',
}
</script>