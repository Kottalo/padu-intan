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
              @click="getItems();deleteDialog = true"
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
              v-for="header in headers"
              :colspan="header.colspan ? header.colspan : ''"
              :style="{width: header.width+'%'}"
              style="border-width: 0px"
            ></th>
          </tr>
        </thead>

        <tbody id="supplierRecordTable" class="table table-sm table-bordered table-hover bg-white">
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
      headers: [
        { title: '日期', width: 8 },
        { title: '供应商', width: 8 },
        { title: '工程', width: 8 },
        { title: '货单号码', width: 8 },
        { title: '总数', width: 8 },
        { title: '总结', width: 8 },
        { title: '支银单号码', width: 8 },
        { title: '银行编号码', width: 8 },
        { title: '出支票', width: 8 },
        { title: '转账', width: 8 },
        { title: '总数', width: 8 },
        { title: '总结', width: 8 },
        { title: '记录表', width: 8 },
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

        axios.post('/order_items/getItems', data)
        .then((res) => {
          this.items = {}
          this.$nextTick()
          .then(() => {
            this.items = res.data;
          });

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
#supplierRecordTable
{
  font-family: verdana;
  border: 1px solid #6099ee !important;
}

#supplierRecordTable td, #supplierRecordTable th
{
  font-size: 12px;
  border: 2px solid #6099ee;
}

#supplierRecordTable .project-name
{
  font-size: 16px;
}

#supplierRecordTable .table-headers
{
  font-size: 12px;
  background: #6099ee;
  color: white;
}

.btn-link
{
  color: black;
  font-size: 13px !important;
  /*font-weight: bold;*/
}
</style>
