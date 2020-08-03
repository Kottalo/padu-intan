<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    max-width="290"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-text-field
        clearable
        :label="Label"
        readonly
        v-bind="attrs"
        v-on="on"
        v-model="date"
        @click:clear="value = null;"
        :dense="Dense"
      ></v-text-field>
    </template>
    <v-date-picker
      ref="datepicker"
      v-model="date"
      @change="updateDate();menu = false"
    ></v-date-picker>
  </v-menu>
</template>

<script>
  export default {
    props: ["value", "label", "dense"],
    data()
    {
      return {
        date: null,
        menu: false,
        Label: this.label,
        Dense: this.dense || false,
      }
    },
    methods:
    {
      updateDate() {
        this.$emit('input', this.date)
      }
    }
  }
</script>
