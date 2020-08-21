<template>
  <tr style="max-height: 16px;">
    <td class="text-left">
      {{ order.date }}
    </td>

    <td class="text-left">
      {{ order.supplier.name }}
    </td>

    <td class="text-left">
      {{ order.project.name }}
    </td>

    <td class="text-left">
      {{ order.ref_no }}
    </td>

    <td class="text-right text-danger">
      {{ order.sub_total }}
    </td>

    <td class="text-right text-danger">
      {{ order_accumulate.toFixed(2) }}
    </td>

    <td style="border: 0">

    </td>

    <td class="text-right">
      <template v-if="!edit_mode">
        {{ order.payment.voucher_no }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="voucher_no"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right">
      <template v-if="!edit_mode">
        {{ bank_name }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="bank_name"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right">
      <template v-if="!edit_mode">
        {{ order.payment.ref_no }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="ref_no"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right text-danger">
      <template v-if="!edit_mode">
        {{ order.payment.cheque }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="cheque"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right text-danger">
      <template v-if="!edit_mode">
        {{ order.payment.cash }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="cash"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right text-danger">
      <template v-if="!edit_mode">
        {{ order.payment.online }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0 text-right"
          v-model="online"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right">
      {{ payment_total.toFixed(2) }}
    </td>

    <td class="text-right">
      {{ payment_accumulate.toFixed(2) }}
    </td>

    <td class="text-right">
      <template v-if="!edit_mode">
        {{ order.payment.remarks }}
      </template>

      <template v-else>
        <input
          class="ma-0 pa-0"
          v-model="remarks"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-left">
      <template v-if="!edit_mode">
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <v-btn text class="btn btn-sm btn-link py-1 px-2 row-btn" @click="edit_mode = true;">修改</v-btn>
          </div>
          <div class="col p-0 m-0 text-left">
            <v-btn text class="btn btn-sm btn-link py-1 px-2 row-btn" data-toggle="modal" data-target="#deleteConfirmModal" @click="$emit('confirm-delete', order_item.id)">清空</v-btn>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <v-btn text class="btn btn-sm btn-link py-1 px-2 row-btn" @click="submitForm();">更新</v-btn>
          </div>
          <div class="col p-0 m-0 text-left">
            <v-btn text class="btn btn-sm btn-link py-1 px-2 row-btn" @click="edit_mode = false;resetData();">取消</v-btn>
          </div>
        </div>
      </template>
    </td>
  </tr>
</template>

<script>
  export default {
    props: {
      Order: Object,
      accumulator: Object,
    },
    data()
    {
      return {
        edit_mode: false,
        order: this.Order,

        voucher_no: '',
        bank_name: '',
        ref_no: '',
        cheque: 0,
        cash: 0,
        online: 0,
        remarks: '',

        order_accumulate: this.accumulator.order,
        payment_accumulate: this.accumulator.payment,
      }
    },
    computed:
    {
      payment_total()
      {
        return parseInt(this.cheque) + parseInt(this.cash) + parseInt(this.online);
      }
    },
    created()
    {
      this.resetData();
    },
    methods: {
      resetData()
      {
        this.voucher_no = this.order.payment.voucher_no;
        this.bank_name = this.order.payment.bank_id ? this.order.payment.bank.name : '';
        this.ref_no = this.order.payment.ref_no;
        this.cheque = this.order.payment.cheque;
        this.cash = this.order.payment.cash;
        this.online = this.order.payment.online;
        this.remarks = this.order.payment.remarks;
      },
      submitForm()
      {
        var data = {
          voucher_no: this.voucher_no,
          bank_name: this.bank_name,
          ref_no: this.ref_no,
          cheque: this.cheque,
          cash: this.cash,
          online: this.online,
          remarks: this.remarks,
        };

        axios.put('/payments/'+this.order.payment.id, data)
        .then((res) => {
          this.$emit('update');
        })
        .catch((err) => {
          console.log(err.response)
        });
      },
    },
  }
</script>

<style scoped>
  input, select
  {
    border: 1px solid black;
    width: 100%;
  }
</style>
