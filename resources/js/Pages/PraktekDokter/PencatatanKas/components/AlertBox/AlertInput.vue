<template>
    <div v-if="showComponentAlertInput" id="alertInput" style="padding:1rem" class="alert-overlay uk-flex uk-flex-center uk-align-center">
            <!-- BOX ALERT INPUT -->
            <div class="box-alert uk-flex-middle uk-flex-center uk-flex">
                <div class="uk-padding uk-padding-remove-vertical uk-width-1-1">
                    <div class="uk-flex uk-flex-column">
                        <div class="uk-margin-medium-top uk-margin-small-bottom uk-flex uk-flex-center">
                            <font-awesome-icon :icon="['far', 'circle-xmark']" size="xl" style="height:82px;color: #ff0001;" />       
                        </div>   
                        <div class="uk-margin-small-top uk-flex uk-flex-center">
                            <p style="color: rgba(0, 0, 0, 1);" class="lato-lg-26-bold">
                                {{titleAlert}}
                            </p>
                        </div>
                        <div class="uk-padding-small uk-margin-remove-bottom uk-padding-remove-vertical uk-flex uk-align-left">
                            <p style="color: rgba(0, 0, 0, 1);margin-right:5px" class="lato-lg-22-semibold">
                                {{subtitleAlert }}
                            </p><span v-if="showInput" class="lato-lg-22-semibold"> {{username}}</span>
                        </div>
                        <div class="uk-padding-small uk-padding-remove-vertical uk-margin-remove-bottom uk-flex uk-align-left">
                            <p style="color: rgba(0, 0, 0, 1);" class="lato-lg-22-semibold">
                                {{subjectAlert}}
                            </p><span v-if="showInput" class="lato-lg-22-semibold" style="color:#FF0000">*</span>
                        </div>
                        <div v-if="errors.invalid" class="uk-alert-primary uk-width-1-1" uk-alert>
                            <p>{{ errors.invalid }}</p>
                        </div> 
                        <div class="uk-padding-small uk-padding-remove-top uk-margin-remove-bottom uk-align-left uk-flex"> 
                            <form v-if="showInput">
                                <div class="uk-inline">
                                    <input v-model="formData" contenteditable class="alert-input alert-content-subject lato-lg-22-semibold" style="background:#f5f5f5f1"/>
                                </div>
                            </form>
                        </div>
                        <div class="uk-padding-small uk-padding-remove-top uk-margin-remove-bottom" style="color: red;">{{ errorText }}</div>

                        <div class="uk-padding-small uk-flex uk-flex-center"> 
                            <div class="uk-flex uk-padding-small">
                                <div class="uk-flex-middle">
                                    <button class="alert-button-cancel lato-md-14-semibold" type="button" @click="tutupAlert">{{cancel}}</button>
                                </div>

                                <div class="uk-flex-middle">
                                    <button class="alert-button lato-md-14-regular" type="button" @click="handleAlertInputModal" >{{confirm}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="uk-margin-top">
              <AlertSuccess
                ref="alertSuccess"
                :showComponentAlertSuccess="showAlertSuccess"
                titleSuccess="Success"
                subtitleSuccess="Penambahan data Sukses dibatalkan "
                confirmSuccess="Lanjutkan"
                @tutup-alert="showAlertSuccess = false"
              />
            </div> -->

    </div>

</template>
<script>
import { mapGetters } from 'vuex';
import headerPage from '@/Components/HeaderPage.vue';
import '../../../Dashboard/tabs/dashboardStyle.css';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { far } from '@fortawesome/free-regular-svg-icons';
import { library } from '@fortawesome/fontawesome-svg-core';
import AlertSuccess from './AlertSuccess.vue'
library.add(far);

export default {
    name: "alert-input",
    props: {
        showComponentAlertInput: Boolean,
        titleAlert: String,
        subtitleAlert: String,
        subjectAlert: String,
        confirm: String,
        cancel: String,
        showInput: Boolean,
        //formData: String,
        // showSuccessBox: Boolean,
    },
    components : {
        headerPage,
        FontAwesomeIcon,
        library,
        AlertSuccess
    },
    computed : {
        ...mapGetters(['errors']),
        ...mapGetters(['user','clientId']), 
       
        username() {
            if(this.user !== null) { return this.user.username; } 
            else { return ''; }
        },

    },
    data() {
        return {
            //showComponentAlertInput: false, 
            showAlertSuccess: false,
            formData: "",
            errorText: "",
        }
    },

    mounted() {
        this.getData();
        // this.$refs.autofocus.focus();
    },

    methods : {
        getData() {
        },

        // handleAlertInputModal() {
        //   if (this.formData.trim() === "") {
        //     this.errorText = "* Field wajib diisi";
        //   } else {
        //     this.errorText = "";
        //     //this.showAlertSuccess = true;
        //     this.$emit('confirm',this.formData);
        //   }
        // },
        handleAlertInputModal() {
            if (this.showInput && this.formData.trim() === "") {
                this.errorText = "* Field wajib diisi";
            } else {
                this.errorText = "";
                this.$emit('confirm', this.formData);
            }
        },
        tutupAlert() {
          this.$emit("tutup-alert");
        },

        showAlert(){
          this.showComponentAlertInput = true;
        }
    }
}
</script>
<style scoped>
@keyframes shake {
  0% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.20);
  }
  45% {
    transform: scale(1.20) rotate(5deg);
  }
  60% {
    transform: scale(1.20) rotate(-5deg);
  }
  70% {
    transform: scale(1.20) rotate(2deg);
  }
  80% {
    transform: scale(1.20) rotate(-5deg);
  }
  95% {
    transform: scale(1.20) rotate(-2deg);
  }
  100% {
    transform: scale(1.20);
  }
}
@-moz-keyframes shake {
  0% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.20);
  }
  45% {
    transform: scale(1.20) rotate(5deg);
  }
  60% {
    transform: scale(1.20) rotate(-5deg);
  }
  70% {
    transform: scale(1.20) rotate(2deg);
  }
  80% {
    transform: scale(1.20) rotate(-5deg);
  }
  95% {
    transform: scale(1.20) rotate(-2deg);
  }
  100% {
    transform: scale(1.20);
  }
}
@-webkit-keyframes shake {
  0% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.20);
  }
  45% {
    transform: scale(1.20) rotate(5deg);
  }
  60% {
    transform: scale(1.20) rotate(-5deg);
  }
  70% {
    transform: scale(1.20) rotate(2deg);
  }
  80% {
    transform: scale(1.20) rotate(-5deg);
  }
  95% {
    transform: scale(1.20) rotate(-2deg);
  }
  100% {
    transform: scale(1.20);
  }
}
@-ms-keyframes shake {
  0% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.20);
  }
  45% {
    transform: scale(1.20) rotate(5deg);
  }
  60% {
    transform: scale(1.20) rotate(-5deg);
  }
  70% {
    transform: scale(1.20) rotate(2deg);
  }
  80% {
    transform: scale(1.20) rotate(-5deg);
  }
  95% {
    transform: scale(1.20) rotate(-2deg);
  }
  100% {
    transform: scale(1.20);
  }
}
@-o-keyframes shake {
  0% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.20);
  }
  45% {
    transform: scale(1.20) rotate(5deg);
  }
  60% {
    transform: scale(1.20) rotate(-5deg);
  }
  70% {
    transform: scale(1.20) rotate(2deg);
  }
  80% {
    transform: scale(1.20) rotate(-5deg);
  }
  95% {
    transform: scale(1.20) rotate(-2deg);
  }
  100% {
    transform: scale(1.20);
  }
}
.alert-overlay {
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Overlay gelap */
  backdrop-filter: blur(5px); /* Efek blur */
}

/* ukuran ipad potrait */
@media all and (device-width: 768px) 
and (device-height: 1024px) 
and (orientation:portrait) {
  .box-alert {
    display:block;
    align-self: center;
    width: 550px!important;
    background: rgba(255, 255, 255, 1);
    border-top: 8px solid rgba(255, 0, 0, 1);
    border-radius: 8px;    
    position: static;
    z-index: 999;
    transform: scale(1.18);
    transition: transform 350ms ease;
    animation: shake 950ms ease-in backwards;  
    -moz-animation: shake 950ms ease-in backwards; 
    -webkit-animation: shake 950ms ease-in backwards;    
    -ms-animation: shake 950ms ease-in backwards;    
    -o-animation: shake 950ms ease-in backwards;  
  }
  .alert-input {
      border: 1px solid rgba(217, 217, 217, 1);
      border-radius: 3px;
      padding: 10px 0!important;
      width: 450px!important;
  }
}

/* ukuran ipad landscape */
@media all and (device-width: 1024px) 
and (device-height: 768px) 
and (orientation:landscape) {
  .box-alert {
    display:block;
    align-self: center;
    width: 598px;
    background: rgba(255, 255, 255, 1);
    border-top: 8px solid rgba(255, 0, 0, 1);
    border-radius: 8px;    
    position: static;
    z-index: 999;
    transform: scale(1.18);
    transition: transform 350ms ease;
    animation: shake 950ms ease-in backwards;  
    -moz-animation: shake 950ms ease-in backwards; 
    -webkit-animation: shake 950ms ease-in backwards;    
    -ms-animation: shake 950ms ease-in backwards;    
    -o-animation: shake 950ms ease-in backwards;  
  }
}
.box-alert {
    display:flex;
    align-self: center;
    width: 605px;
    background: rgba(255, 255, 255, 1);
    border-top: 8px solid rgba(255, 0, 0, 1);
    border-radius: 8px;    
    position: static;
    z-index: 999;
    transform: scale(1.18);
    transition: transform 350ms ease;
    animation: shake 950ms ease-in backwards;  
    -moz-animation: shake 950ms ease-in backwards; 
    -webkit-animation: shake 950ms ease-in backwards;    
    -ms-animation: shake 950ms ease-in backwards;    
    -o-animation: shake 950ms ease-in backwards;  
}
/* .box-alert:hover {
    transform: scale(1.18);
    transition: transform 350ms ease;
    animation: shake 950ms ease-in backwards;  
    -moz-animation: shake 950ms ease-in backwards; 
    -webkit-animation: shake 950ms ease-in backwards;    
    -ms-animation: shake 950ms ease-in backwards;    
    -o-animation: shake 950ms ease-in backwards;     
} */
.alert-input {
    border: 1px solid rgba(217, 217, 217, 1);
    border-radius:3px;
    padding:8px 10px;
    width: 475px;
    height:auto;
}
.alert-input-text {
    color:var(--black);
    font-family: var(--lato);
    font-size: var(--font-sm-13);
    font-weight: var(--semibold);
    line-height: 16px;
    letter-spacing: 0.3px;
}
.alert-button {
    width: 118px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 3px;
    background: rgba(0, 0, 0, 0.5);
    color:#ffffff;
    border: 1px solid rgba(0, 0, 0, 0.5);
    text-align: center;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    margin:0 6px;
}
.alert-button:hover {
  background: rgb(97, 96, 96);
  color: #eeeaea;
}
.alert-button-cancel {
    width: 118px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 3px;
    background: white;
    color:rgba(0, 0, 0, 1);
    border: 1px solid rgba(0, 0, 0, 0.3);
    text-align: center;
    cursor: default;
    margin: 0em;
    padding-block: 1px;
    padding-inline: 6px;
    margin:0 6px;
}
.alert-button-cancel:hover {
  background: #AFDFFF;
  color: #000000;
}

/* content editable */

*[contenteditable] { border-radius: 0.25em; min-width: 1em; outline: 0; }

*[contenteditable] { cursor: pointer; }

*[contenteditable]:hover, *[contenteditable]:focus, td:hover *[contenteditable], td:focus *[contenteditable], img.hover { background: #DEF; box-shadow: 0 0 10em 0.5em #DEF; }

span[contenteditable] { display: inline-block; }
</style>