<template>
    <div class="modal" :class="{ 'modal-open': isOpen }">
      <div class="modal-content">
        <h3>Add Schedule</h3>
        <input type="date" v-model="tglMulai" @input="validateDate">
        <input type="date" v-model="tglSelesai" @input="validateDate">
        <br>
        <br>
        <input type="text" v-model="jabatan">
        <br>
        <br>
        <input type="text" v-model="unit">
        <br>
        <br>
        <textarea v-model="keterangan" name="keterangan" id="keterangan" cols="30" rows="10"></textarea>
        <br>
        <button @click="submitForm">Submit</button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        tglMulai: '',
        tglSelesai: '',
        jabatan: '',
        unit: '',
        keterangan: '',
        isOpen: false,
      };
    },
    methods: {
      validateDate() {
        if (this.tglMulai > this.tglSelesai) {
          // Jika tanggal mulai lebih besar dari tanggal selesai, reset nilainya
          this.tglMulai = '';
          this.tglSelesai = '';
        }
      },
      submitForm() {
        const newSchedule = {
          tglMulai: this.tglMulai,
          tglSelesai: this.tglSelesai,
          jabatan: this.jabatan,
          unit: this.unit,
          keterangan: this.keterangan,
        };
  
        this.$emit('submit', newSchedule);
        this.resetForm();
        this.closeModal();
      },
      resetForm() {
        this.tglMulai = '';
        this.tglSelesai = '';
        this.jabatan = '';
        this.unit = '';
        this.keterangan = '';
      },
      closeModal() {
        this.isOpen = false;
      },
    },
  };
  </script>
  