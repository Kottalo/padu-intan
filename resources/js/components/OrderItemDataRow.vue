<template>
  <tr style="max-height: 16px;">
    <td class="text-left">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.order.date }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input type="date" v-model="date" style="max-width: 100%">
      </template>
    </td>

    <td class="text-left">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.order.supplier.name }}
      </template>

      <template v-if="create_mode || edit_mode">
        <select
          v-model="supplier_id" style="max-width: 100%"
        >
          <option v-for="supplier in suppliers" :value="supplier.id">{{ supplier.name }}</option>
        </select>
      </template>
    </td>

    <td class="text-left">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.order.ref_no }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          type="text"
          v-model="ref_no"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-left">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.item.name }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          class="ma-0 pa-0"
          v-model="item_name"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-left pt-1 pl-2">
      <input class="" v-model="Return" style="max-width: 100%" type="checkbox" :checked="order_item ? order_item.return : false" aria-label="Checkbox for following text input">
    </td>

    <td>
      <template v-if="data_mode && !edit_mode">
        {{ order_item.quantity + (order_item.unit ? order_item.unit.name : '') }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          class="ma-0 pa-0"
          v-model="quantity"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item ? order_item.return : false}">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.price }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          class="ma-0 pa-0"
          v-model="price"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item ? order_item.return : false}">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.total_price }}
      </template>

      <template v-if="create_mode || edit_mode">
        {{ total_price }}
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item ? parseInt(order_item.return) : false}" style="max-width: 20px;">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.sst_perc }}%
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          class="ma-0 pa-0"
          v-model="sst_perc"
        >
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item.sst_amount < 0}">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.sst_amount }}
      </template>

      <template v-if="create_mode || edit_mode">
        {{ sst_amount }}
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item.sub_total < 0}">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.sub_total }}
      </template>

      <template v-if="create_mode || edit_mode">
        {{ sub_total }}
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': order_item.sub_total < 0}">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.sub_total }}
      </template>

      <template v-if="create_mode || edit_mode">
        {{ sub_total }}
      </template>
    </td>

    <td class="text-right" :class="{'text-danger': accumulate < 0}">
      <template v-if="data_mode && !edit_mode">
        {{ accumulate }}
      </template>
    </td>

    <td class="text-right">
      <template v-if="data_mode && !edit_mode">
        {{ order_item.remarks }}
      </template>

      <template v-if="create_mode || edit_mode">
        <input
          class="ma-0 pa-0"
          v-model="remarks"
          style="max-width: 100%"
        >
      </template>
    </td>

    <td class="text-left">
      <template v-if="data_mode && !edit_mode">
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" @click="edit_mode = true;">修改</a>
          </div>
          <div class="col p-0 m-0 text-left">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" data-toggle="modal" data-target="#deleteConfirmModal">删除</a>
          </div>
        </div>
      </template>

      <template v-if="edit_mode">
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" @click="toEdit();">更新</a>
          </div>
          <div class="col p-0 m-0 text-left">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" @click="edit_mode = false;">取消</a>
          </div>
        </div>
      </template>

      <template v-if="create_mode">
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" @click="submitForm();">添加</a>
          </div>
          <div class="col p-0 m-0 text-left">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" data-toggle="modal" data-target="#deleteConfirmModal">清空</a>
          </div>
        </div>
      </template>
    </td>
  </tr>
</template>

<script>
  export default {
    props: {
      dataMode: Boolean,
      createMode: Boolean,
      orderItem: Object,
      projectId: Number,
      accumulator: Number,
    },
    data()
    {
      return {
        data_mode: this.dataMode,
        create_mode: this.createMode,
        edit_mode: false,
        order_item: this.orderItem,
        project_id: this.projectId,

        suppliers: [],

        date: '',
        supplier_id: null,
        ref_no: null,
        item_name: null,
        unit_name: null,
        Return: false,
        quantity: 0,
        price: 0,
        sst_perc: 0,
        remarks: '',

        total_price: 0,
        sst_amount: 0,
        sub_total: 0,

        accumulate: this.accumulator,
      }
    },
    created()
    {
      this.getSuppliers();
    },
    methods: {
      getSuppliers()
      {
        var data = {
          project_id: this.project_id,
        };

        axios.post('/suppliers/getSuppliersByProjectId', data)
        .then((res) => {
          this.suppliers = res.data;
          this.supplier_id = this.suppliers[0] ? this.suppliers[0].id : '';
        });
      },
      submitForm()
      {
        var data = {
          project_id: this.project_id,
          date: this.date,
          ref_no: this.ref_no,
          supplier_id: this.supplier_id,
          return: this.Return,
          item_name: this.item_name,
          unit_name: this.unit_name,
          quantity: this.quantity,
          price: this.price,
          sst_perc: this.sst_perc,
          remarks: this.remarks,
        };

        if (this.edit_mode)
        {
          axios.put('/order_items/'+this.order_item.id, data)
          .then((res) => {
            this.$emit('update');
          });
        }
        else
        {
          axios.post('/order_items', data)
          .then((res) => {
            this.$emit('update');
          });
        }
      },
      toEdit()
      {
        this.edit_mode = true;
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
