<template>
    
    <div class="uk-container-small" style="padding-bottom:100px;">
      <div>
          <router-view></router-view>
      </div>      
          
      <div class="uk-grid-small" uk-grid style="background-color:#f0f0f0;padding:1em;margin-top:5em; border-top:2px solid #9f9f9f;">        
        <div class="uk-width-1-2@s uk-width-1-4@l" style="padding:0 0.3em 0 0.3em;margin:0;">
          <div class="uk-card uk-card-hover uk-card-body" style="margin:0;">
            <p style="font-size:18px;font-weight:bold;margin:0;">FAQ</p>
            <p style="margin:0;border-bottom:1px solid #39f;margin-top:0.5em;"></p>
            <p style="font-size:14px;font-weight:200;margin:1em 0 0 0;">Diskusi dan pertanyaan seputar aplikasi dapat dibaca <a href="#" style="text-decoration:underline;">disini</a></p>
          </div>
        </div>
        <div class="uk-width-1-2@s uk-width-1-4@l" style="padding:0 0.3em 0 0.3em;">
          <div class="uk-card uk-card-hover uk-card-body" style="margin:0;">
            <p style="font-size:18px;font-weight:bold;margin:0;">TUTORIAL</p>
            <p style="margin:0;border-bottom:1px solid #39f;margin-top:0.5em;"></p>
            <p style="font-size:14px;font-weight:200;margin:1em 0 0 0;">Tutorial dan video cara penggunaan aplikasi dapat diakses <a href="#" style="text-decoration:underline;">disini</a></p>
          </div>
        </div>
        <div class="uk-width-1-2@s uk-width-1-4@l" style="padding:0 0.3em 0 0.3em;">
          <div class="uk-card uk-card-hover uk-card-body" style="margin:0;">
            <p style="font-size:18px;font-weight:bold;margin:0;">CHANGE LOG</p>
            <p style="margin:0;border-bottom:1px solid #39f;margin-top:0.5em;"></p>
            <p style="font-size:14px;font-weight:200;margin:1em 0 0 0;">Daftar perubahan, penambahan fitur dan perbaikan aplikasi ada <a href="#" style="text-decoration:underline;">disini</a></p>
          </div>
        </div>
        <div class="uk-width-1-2@s uk-width-1-4@l" style="padding:0 0.3em 0 0.3em;">
          <div class="uk-card uk-card-hover uk-card-body" style="margin:0;">
            <p style="font-size:18px;font-weight:bold;margin:0;">VERSION</p>
            <p style="margin:0;border-bottom:1px solid #39f;margin-top:0.5em;"></p>
            <p style="font-size:14px;font-weight:200;margin:1em 0 0 0;">App versi v.1.0.0</p>
            <p style="font-size:14px;font-weight:200;margin:0;">Laravel 9.x</p>
            <p style="font-size:14px;font-weight:200;margin:0;">Vue 3.x</p>
            <p style="font-size:14px;font-weight:200;margin:0;">db postgreSQL 14.x</p>            
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  name: 'WelcomePage',
}
</script>