<template>
    <div
      :class="{
        'uk-flex uk-flex-wrap uk-margin-small': variant === 'horizontal',
      }"
    >
      <ul
        class="list-none uk-background-opacity-30 uk-padding-small uk-text-center uk-overflow-auto uk-flex uk-flex-nowrap uk-flex-middle uk-margin-bottom tab-list"
        style="border:1px solid rgba(0, 0, 0, 0.1);background-color: white;border-radius: 10px; height: 25px;width: 650px;position: sticky;z-index: 999;"
      >
        <li
          v-for="(tab, index) in tabList"
          :key="index"
          class="uk-width-1-2 uk-flex uk-flex-middle uk-flex-center uk-padding-small tab-item"
          style="height: 70px"
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
   import "../../Dashboard/tabs/dashboardStyle.css";

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
    data() {
      return {
        activeTab: 1,
      };
    },
  };
  </script>
  
  <style scoped>
  .tab-underline {
    content: "";
    display: block;
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

  </style>
  