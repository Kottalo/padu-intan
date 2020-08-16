<template>
  <v-app>
    <v-container>
      <v-row>
        <v-col cols="5">
          <v-row>
            <v-col cols="6">
              <v-combobox
                v-model="projectIds"
                :items="projects"
                label="选择工程"
                item-text="name"
                item-value="id"
                multiple
                @change="getSuppliers();"
              ></v-combobox>
            </v-col>

            <v-col cols="6">
              <v-combobox
                v-model="supplierIds"
                :items="suppliers"
                label="选择供应商"
                item-text="name"
                item-value="id"
                multiple
              ></v-combobox>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="5">
          <v-row>
            <v-col cols="6">
              <Datepicker
                label="日期从"
                v-model="date_from"
              ></Datepicker>
            </v-col>

            <v-col cols="6">
              <Datepicker
                label="到"
                v-model="date_to"
              ></Datepicker>
            </v-col>
          </v-row>
        </v-col>

        <v-col class="pt-8">
          <v-row>
            <v-btn
              block
              @click="getItems();"
            >提交</v-btn>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <div class="px-1">
      <table class="datatable">
        <thead>
          <tr class="table-headers">
            <th
              v-for="header in headers"
              :colspan="header.colspan ? header.colspan : ''"
              :style="{width: header.width+'%', backgroundColor: header.title == '' ? 'white': '', border: header.title == '' ? 0 : 0}"
              style="border-width: 0px"
            >{{ header.title }}</th>
          </tr>
        </thead>

        <tbody class="datatable table table-sm table-bordered table-hover bg-white">
          <template v-for="supplier in items">

            <PaymentDataRow
              v-for="(order, key) in supplier.orders"
              :key="key"
              :Order="order"
              :accumulator="accumulator[order.id]"
              @update="getItems()"
            ></PaymentDataRow>

            <tr>
              <th class="text-right" colspan="4">Total</th>
              <th class="text-right">{{ supplier.order_total }}</th>
              <th class="text-right" colspan="7">Total</th>
              <th class="text-right">{{ supplier.payment_total }}</th>
              <th colspan="3"></th>
            </tr>

            <tr>
              <td colspan="16"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <ConfirmDialog
      v-model="deleteDialog"
      @execute="confirmDelete()"
    ></ConfirmDialog>
  </v-app>
</template>

<script>
  export default {
    data: () => ({
      deleteDialog: false,

      accumulator: {},
      cols: 15,
      headers: [
        { title: '日期', width: 8 },
        { title: '供应商', width: 8 },
        { title: '工程', width: 8 },
        { title: '货单号码', width: 8 },
        { title: '总数', width: 6 },
        { title: '总结', width: 6 },
        { title: '', width: 1 },
        { title: '支银单号码', width: 8 },
        { title: '银行', width: 8 },
        { title: '编号码', width: 8 },
        { title: '出支票', width: 6 },
        { title: '出现钱', width: 6 },
        { title: '转账', width: 6 },
        { title: '总数', width: 6 },
        { title: '总结', width: 6 },
        { title: '记录表', width: 2 },
        { title: '操作', width: 8 },
      ],

      projects: [],
      suppliers: [],
      projectIds: [],
      supplierIds: [],
      items: [],
      date_from: null,
      date_to: null,
    }),
    created()
    {
      this.getProjects();
    },
    methods: {
      getProjects()
      {
        axios.get('/projects')
        .then((res) => {
          this.projects = res.data;
        });
      },
      getSuppliers()
      {
        var projectIds = this.projectIds.map((value) => {return value.id});

        axios.post('/suppliers/getSuppliersByProjectIds', {projectIds: projectIds})
        .then((res) => {
          this.suppliers = res.data;
        });
      },
      toDelete(id)
      {
        this.idToDelete = id;
        this.deleteDialog = true;
      },
      confirmDelete()
      {
        axios.delete('/order_items/'+this.idToDelete)
        .then((res) => {
          this.deleteDialog = false;
          this.getItems();
        });
      },
      getItems()
      {
        var projectIds = this.projectIds.map((value) => {return value.id});
        var supplierIds = this.supplierIds.map((value) => {return value.id});

        var data = {
          projectIds: projectIds,
          supplierIds: supplierIds,
          date_from: this.date_from,
          date_to: this.date_to,
        };

        axios.post('/orders/getItems', data)
        .then((res) => {
          this.items = {}
          this.$nextTick()
          .then(() => {
            this.items = res.data;
          });

          var acc1 = 0;
          var acc2 = 0;
          this.accumulator = {};

          res.data.forEach((supplier) => {
            supplier.orders.forEach((order) => {
              this.accumulator[order.id] = {};
              this.accumulator[order.id]['order'] = acc1 += parseFloat(order.sub_total);
              this.accumulator[order.id]['payment'] = acc2 += parseFloat(order.payment.total);
            });
          });
        })
        .catch((err) => {
          console.log(err.response)
        })
      },
    },
  }
</script>

<style>
.btn-link
{
  color: black;
  font-size: 13px !important;
}
</style>
