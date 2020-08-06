<template>
  <v-row justify="center">
    <v-dialog :value="value" persistent max-width="290">
      <v-card>
        <v-card-title class="headline">确定删除</v-card-title>
        <v-card-text>是否确定删除此货品？</v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="danger" text @click="$emit('execute');close();">确定</v-btn>
          <v-btn color="secondary" text @click="close();">取消</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
  export default {
    props: ["value"],
    data()
    {
      return {

      }
    },
    methods: {
      close()
      {
        this.$emit('input', false);
      }
    }
  }
</script>
