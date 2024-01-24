<template>
    <div
      :class="{
        'uk-flex uk-flex-wrap uk-margin-small': variant === 'horizontal',
      }"
    >
      <ul
        class="list-none uk-background-opacity-30 uk-padding-small uk-box-shadow-large uk-text-center uk-overflow-auto uk-flex uk-flex-nowrap uk-flex-middle uk-margin-bottom tab-list"
        style="border-radius: 20px; height: 13px;width: auto;position: fixed;z-index: 999;margin-top:-115px;background-color: #D2ECF2"
      >
        <li
          v-for="(tab, index) in tabList"
          :key="index"
          class="uk-width-1-1 uk-flex uk-flex-middle uk-flex-center uk-padding-small tab-item"
          style="border-radius: 20px; height: 5px;"
          :class="{
            'uk-text-primary lato-md-14-medium uk-background-default uk-box-shadow-large':
              index + 1 === activeTab,
            'uk-text-emphasis lato-md-14-medium': index + 1 !== activeTab,
          }"
          
          @click="activeTab = index + 1"
          
        > 
        <!-- JIKA ANTRIAN KOSONG MAKA ICON CIRCLE MONITORING INI AKAN HILANG. 
            INI SEBAGAI PENANDA USER BAHWA ADA SEJUMLAH ANTRIAN YANG BELUM DILAYANI.
            PATH TUJUAN: /DASHBOARD/INDEX.VUE
            TAG KOMPONEN TUJUAN: <tab-switcher> DAN <Monitoring>
          -->
        <font-awesome-icon
          v-if="tab === 'Monitoring' && jumlahBoxMonitoring1 >= 1" 
          :icon="['fas', 'circle']"
          beat-fade
          size="2xs"
          style="color: #ef4e4e;margin-right:5px"
        />
          <label
            :for="`${_uid}${index}`"
            v-text="tab"
            class="cursor-pointer block"
          />
          <input
            :id="`${_uid}${index}`"
            type="radio"
            :name="`${_uid}-tab`"
            :value="index + 1"
            v-model="activeTab"
            class="hidden"
            style="display: none;"
          />
        </li>
      </ul>
      <transition name="fade-slide" mode="out-in">
        <div
          :key="activeTab"
        >
          <slot :name="`tabPanel-${activeTab}`" />
        </div>
      </transition>
    </div>
  </template>
  
  <script>
   import "../tabs/dashboardStyle.css";

  export default {
    props: {
      tabList: {
        type: Array,
        required: true,
      },
      variant: {
        type: String,
        required: false,
        default: () => "vertical",
        validator: (value) => ["horizontal", "vertical"].includes(value),
      },
      jumlahBoxMonitoring1: {
        type: Number, 
        required: true,
      },
    },
    data() {
      return {
        activeTab: 1,
        _uid : '',
      };
    },
  };
  </script>
  
  <style scoped>
  .tab-list {
    display: flex;
    overflow: hidden;
    transition: color 200ms;
  }
  
  .tab-list li {
    transition: transform 0.3s ease-in-out;
  }
  
  .tab-panel {
    position: relative;
  }
  
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: opacity 0.3s, transform 0.3s;
  }
  
  .fade-slide-enter,
  .fade-slide-leave-to {
    opacity: 0;
    transform: translateY(20px);
  }

  </style>
  