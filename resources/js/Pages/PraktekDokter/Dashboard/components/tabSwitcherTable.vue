<template>
    <div :class="{'uk-flex uk-flex-wrap uk-margin-small': variant === 'horizontal', }" >
      <ul
        class="uk-child-width-1-2 list-none uk-background-opacity-30 uk-padding-small uk-text-center uk-overflow-auto uk-flex uk-flex-nowrap uk-flex-middle uk-margin-bottom tab-list"
        style="border:1px solid rgba(0, 0, 0, 0.1);background-color: white;border-radius: 10px; height: 25px;width: 500px;position: sticky;z-index: 99;"
      >
      <li
          v-for="(tab, index) in tabList"
          :key="index"
          class="uk-flex uk-flex-middle uk-flex-center uk-padding-small tab-item"
          style="height: auto"
          :class="{
            'uk-text-primary lato-md-14-semibold tab-underline': index + 1 === activeTab,
            'uk-text-emphasis lato-md-14-semibold': index + 1 !== activeTab,
          }"
          @click="activeTab = index + 1"
        >
          <label
            :for="`${index}`"
            v-text="tab"
            class="cursor-pointer block"
            :class="{
            'tab-underline': index + 1 === activeTab,
            'uk-text-emphasis': index + 1 !== activeTab,
          }"
          />
          <input
            :id="`${index}`"
            type="radio"
            :name="`${index}-tab`"
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
    },

    created() {
        this.tabName = this.$router.currentRoute;
    },

    data() {
      return {
        activeTab: 1,
        tabName : null,
      };
    },
  };
  </script>
  
  <style scoped>
  .tab-underline {
    content: "";
    border-bottom: 4px solid rgba(30, 135, 240, 1);
    box-shadow: inset 0 -4.2px 0 0 rgba(30, 135, 240, 1);
    padding: 15px 0px;
  }

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

/* ukuran ipad potrait */
@media all and (device-width: 768px) 
and (device-height: 1024px) 
and (orientation:portrait) {
  .tab-link-active {
    font-size: 12px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}

/* ukuran ipad landscape */
@media all and (device-width: 1024px) 
and (device-height: 768px) 
and (orientation:landscape) {
  .tab-link-active {
    font-size: 12px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}


  .tab-link-active {
    font-weight: 700;
  }

  .tab-link-inactive {
    color:#666;
    font-weight: 700;
  }

  </style>
  