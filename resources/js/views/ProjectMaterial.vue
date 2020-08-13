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
              @click="getItems()"
            >提交</v-btn>
          </v-row>
        </v-col>
      </v-row>
    </v-container>

    <div class="px-1">
      <table>
        <thead style="border-width: 0px">
          <tr style="border-width: 0px">
            <th
              v-for="th in thead"
              :style="{width: th.width+'%'}"
              style="border-width: 0px"
            ></th>
          </tr>
        </thead>

        <tbody class="datatable table table-sm table-bordered table-hover bg-white">
          <template v-for="project in items">
            <tr>
              <th class="project-name" :colspan="cols">{{ project.name }}</th>
            </tr>

            <tr class="table-headers">
              <th
                v-for="header in headers"
                :colspan="header.colspan ? header.colspan : ''"
              >{{ header.title }}</th>
            </tr>

            <template v-for="order in project.orders">
              <OrderItemDataRow
                :projectId="order.project_id"
                v-for="order_item in order.order_items"
                :key="'o'+order_item.id"
                dataMode
                :orderItem="order_item"
                :accumulator="accumulator[order_item.id]"
                @update="getItems()"
                @confirm-delete="toDelete($event)"
              ></OrderItemDataRow>

              <tr>
                <th class="text-right" colspan="7">Total</th>
                <th class="text-right" :class="{'text-danger': order.total_price < 0}">{{ order.total_price }}</th>
                <th></th>
                <th class="text-right" :class="{'text-danger': order.total_price < 0}">{{ order.sst_amount }}</th>
                <th></th>
                <th class="text-right" :class="{'text-danger': order.sub_total < 0}">{{ order.sub_total }}</th>
                <th></th>
                <th></th>
                <th></th>
              </tr>

              <tr>
                <td class="table-gap" style="height: 20px;" :colspan="cols"></td>
              </tr>
            </template>

            <tr>
              <th class="text-right" colspan="7">Sub Total</th>
              <th class="text-right" :class="{'text-danger': project.total_price < 0}">{{ project.total_price }}</th>
              <th></th>
              <th class="text-right" :class="{'text-danger': project.sst_amount < 0}">{{ project.sst_amount }}</th>
              <th></th>
              <th class="text-right" :class="{'text-danger': project.sub_total < 0}">{{ project.sub_total }}</th>
              <th></th>
              <th></th>
              <th></th>
            </tr>

            <OrderItemDataRow
              :projectId="project.id"
              :key="'p'+project.id"
              createMode
              @update="getItems()"
            ></OrderItemDataRow>

            <tr>
              <td class="table-gap" style="height: 20px;" :colspan="cols"></td>
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
      thead: [
        { title: '日期', width: 7, min: 0, max: 0 },
        { title: '供应商', width: 12, min: 0, max: 0 },
        { title: '货单号码', width: 8, min: 0, max: 0 },
        { title: '货名', width: 12, min: 0, max: 0 },
        { title: '退', width: 3, min: 0, max: 0 },
        { title: '数量', width: 7, min: 0, max: 0 },
        { title: '价格', width: 6, min: 0, max: 0 },
        { title: '总价格', width: 6, min: 0, max: 0 },
        { title: 'SST 百分比', width: 4, min: 0, max: 0 },
        { title: 'SST 银额', width: 6, min: 0, max: 0 },
        { title: '退货', width: 6, min: 0, max: 0 },
        { title: '总数', width: 6, min: 0, max: 0 },
        { title: '总结', width: 6, min: 0, max: 0 },
        { title: '记录表', width: 8, min: 0, max: 0 },
        { title: '操作', width: 12, min: 0, max: 0 },
      ],
      headers: [
        { title: '日期', },
        { title: '供应商', },
        { title: '货单号码', },
        { title: '货名', },
        { title: '退', },
        { title: '数量', },
        { title: '价格', },
        { title: '总价格', },
        { title: 'SST 银额', colspan: 2,},
        { title: '退货', },
        { title: '总数', },
        { title: '总结', },
        { title: '记录表', },
        { title: '操作', },
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

        axios.post('/order_items/getItems', data)
        .then((res) => {
          this.items = {}
          this.$nextTick()
          .then(() => {
            this.items = res.data;
          })

          var acc = 0;
          this.accumulator = {};

          res.data.forEach((project) => {
            project.orders.forEach((order) => {
              order.order_items.forEach((order_item) => {
                this.accumulator[order_item.id] = acc += parseFloat(order_item.sub_total);
              });
            });
          });
        });
      },
    },
  }
</script>

<style>

.btn-link
{
  color: black;
  font-size: 13px !important;
  /*font-weight: bold;*/
}
</style>
