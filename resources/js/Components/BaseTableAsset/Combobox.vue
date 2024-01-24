<template>
  <div id="combobox">
    <combobox :color-options="colors" placeholder="pilih prioritas" :input-id="'colorInput'" v-model="selectedPriorityName" v-model.trim="mtickets.prioritas"></combobox>
  </div>
</template>

<script>
export default {
  components: {
    Combobox: {
      template: `
        <div class="wrapper-dropdown">
          <span class="uk-input uk-form-small" @click="toggleDropdown()" v-html="selector"> {{ placeholder }}</span>
          <ul class="dropdown" v-show="active">
            <li v-if="emptyOption" @click="setColor()">
              <span class="noColor" v-if="emptyOption !== 'true'"></span> {{ emptyOption === 'true' ? '' : emptyOption }}
            </li>
            <li v-for="color in colors" @click="setColor(color.label, color.priority)">
              <span :style="{ background: color.label,  borderRadius: '3px' }"></span> {{ color.priority }}
            </li>
          </ul>
          <input type="hidden" :name="inputId" :id="inputId" v-model="selectedPriorityName">
        </div>
      `,
      props: ['colorOptions', 'placeholder', 'emptyOption', 'inputId'],
      data() {
        return {
          active: false,
          noSelection: true,
          selectedColorLabel: "",
          selectedPriorityName: "", 
          colors: [
              {
                label: "#66ff00",
                priority: "Rendah"
              },
              {
                label: "#FFFF00",
                priority: "Sedang"
              },
              {
                label: "#FF0000",
                priority: "Tinggi"
              }
            ],
        };
      },
      computed: {
        selector() {
          if (!this.selectedColorLabel && !this.emptyOption) {
            return this.placeholder;
          } else if (!this.selectedColorLabel && this.emptyOption) {
            if (this.emptyOption === 'true') {
              return this.placeholder;
            } else if (this.emptyOption !== 'true' && this.noSelection === false) {
              return '<span class="noColor"></span> ' + this.emptyOption;
            } else {
              return this.placeholder;
            }
          } else {
            return '<span style="background: ' + this.selectedColorLabel + '; border-radius: 3px"></span> ' + this.selectedPriorityName;
          }
        }
      },
      methods: {
        setColor(color, colorPriority) {
          this.selectedColorLabel = color;
          this.selectedPriorityName = colorPriority;
          this.active = false;
          this.noSelection = false;
          this.$emit('input', this.selectedColorLabel);
        },
        toggleDropdown() {
          this.active = !this.active;
        }
      }
    }
  }
};
</script>
<style>
* {
	box-sizing: border-box;
	font-family: "Arial";
}
.wrapper-dropdown {
    position: relative;
    width: 100%;
    background: #FFF;
    color: #2e2e2e;
    outline: none;
    cursor: pointer;
}
.wrapper-dropdown > span {
	width: 100%;
	display: block;
	border: 1px solid #ababab;
	/* padding: 5px; */
}
.wrapper-dropdown > span > span {
  padding: 0 12px;
  margin-right: 5px;
}
.wrapper-dropdown > span > span.noColor {
	background: #CCC;
	position: relative;
}
.wrapper-dropdown > span > span.noColor:after {
	content: "";
	background: red;
	-webkit-transform: rotate(-32deg);
  transform: rotate(-32deg);
  display: inline-block;
  width: 28px;
  height: 2px;
  position: absolute;
  bottom: 7px;
  left: -2px;
}
.wrapper-dropdown > span:after {
    content: "";
    width: 0;
    height: 0;
    position: absolute;
    right: 16px;
    top: calc(50% + 4px);
    margin-top: -6px;
	  border-width: 6px 6px 0 6px;
    border-style: solid;
	  border-color: #2e2e2e transparent;
}

.wrapper-dropdown .dropdown {
    position: absolute;
	  z-index: 10;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    font-weight: normal;
	  list-style-type: none;
	  padding-left: 0;
	  margin: 0;
	  border: 1px solid #ababab;
	  border-top: 0;
}

.wrapper-dropdown .dropdown li {
    display: block;
    text-decoration: none;
    color: #2e2e2e;
	  padding: 5px;
	  cursor: pointer;
	  min-height: 28px;
}

.wrapper-dropdown .dropdown li > span {
  padding: 0 12px;
  margin-right: 5px;
}
.wrapper-dropdown .dropdown li > span.noColor {
	background: #CCC;
	position: relative;
}
.wrapper-dropdown .dropdown li > span.noColor:after {
	content: "";
	background: red;
	-webkit-transform: rotate(-32deg);
  transform: rotate(-32deg);
  display: inline-block;
  width: 28px;
  height: 2px;
  position: absolute;
  bottom: 7px;
  left: -2px;
}

.wrapper-dropdown .dropdown li:hover {
    background: #eee;
	  cursor: pointer;
}
</style>

