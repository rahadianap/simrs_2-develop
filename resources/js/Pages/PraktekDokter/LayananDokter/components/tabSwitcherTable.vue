<template>
    <div :class="{'uk-flex uk-flex-wrap uk-margin-small': variant === 'horizontal', }" >
      <ul
        class="tab-switcher list-none uk-background-opacity-30 uk-padding-small uk-text-center uk-overflow-auto uk-flex uk-flex-nowrap uk-flex-middle uk-margin-bottom tab-list"
        style="border:1px solid rgba(0, 0, 0, 0.1);background-color: white;border-radius: 10px; height: 25px;position: sticky;z-index: 99;"
      >
        <li
          v-for="(tab, index) in tabList"
          :key="index"
          class="uk-width-1-2 uk-flex uk-flex-middle uk-flex-center uk-padding-small tab-item"
          style="height: auto"
          :class="{
            'uk-text-primary lato-md-14-semibold': index + 1 === activeTab,
            'uk-text-emphasis lato-md-14-semibold': index + 1 !== activeTab,
          }">
          <!-- <a href="#"><label class="cursor-pointer block">{{ tab.title }}</label></a> -->
          <router-link 
            :to = "{ name:tab.linkName }" 
            class="cursor-pointer block" 
            :class=" tabName.name == tab.linkName ? 'tab-link-active tab-underline' : 'tab-link-inactive'"
            >
              {{ tab.title }}
          </router-link>
          <!-- <input :id="`${index}`" type="radio" :value="index + 1" v-model="activeTab" class="hidden" style="display: none;"/> -->
        </li>
      </ul>
      <!-- <transition name="fade-slide" mode="out-in">
        <div :key="activeTab" >
          <slot :name="`tabPanel-${activeTab}`" />
        </div>
      </transition> -->
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

  
/* ukuran ipad mini potrait */
@media all and (device-width: 768px) 
and (device-height: 1024px) 
and (orientation:portrait) {
  .tab-switcher {
    width: 370px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}

/* ukuran ipad air potrait */
@media all and (device-width: 820px) 
and (device-height: 1180px) 
and (orientation:portrait) {
  .tab-switcher {
    width: 450px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}

/* ukuran ipad pro potrait */
@media all and (device-width: 1024px) 
and (device-height: 1366px) 
and (orientation:portrait) {
  .tab-switcher {
    width: 475px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}

/* ukuran ipad mini landscape */
@media all and (device-width: 1024px) 
and (device-height: 768px) 
and (orientation:landscape) {
  .tab-switcher {
    width: 355px!important;
  }
  .tab-item {
    font-size: var(--font-sm-12);
    font-weight: 700;
  }
}

.tab-switcher {
  width: 600px;
}

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


/* ukuran ipad potrait */
@media all and (device-width: 768px) 
and (device-height: 1024px) 
and (orientation:portrait) {
  .tab-link-active {
    font-size: 12px!important;
  }
}

/* ukuran ipad landscape */
@media all and (device-width: 1024px) 
and (device-height: 768px) 
and (orientation:landscape) {
  .tab-link-active {
    font-size: 12px!important;
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
  