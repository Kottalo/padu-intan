@extends('main')

@php
  use Carbon\Carbon;

  $projects = App\Models\Project::get();
  $suppliers = App\Models\Supplier::get();

  $today = Carbon::now()->format('Y-m-d');
@endphp

@section('content')
<x-confirm-modal id="deleteConfirmModal" title="确定删除" function="confirmDelete()">确定要删除此工程材料吗？</x-confirm-modal>

<div class="col-md-10 mx-auto">
  <form id="form" action="/order_items/getItems" method="POST">
    @csrf
    <div class="row">
      <div class="col-md-5">
        <div class="row">
          <div class="input-group mb-3 col-md-6">
            <div class="container-fluid pl-0">
              <select id="projectIds" name="projectIds[]" class="custom-select ml-0" multiple="multiple">
                @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="input-group mb-3 col-md-6">
            <div class="container-fluid pl-0">
              <select id="supplierIds" name="supplierIds[]" class="custom-select ml-0" multiple="multiple">
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript">
        $('#projectIds').multiselect(projectMultiSelectConfig);
        $('#supplierIds').multiselect(supplierMultiSelectConfig);

        $('#projectIds').multiselect('select', [{{ join(',', Session::has('projectIds') ? Session::get('projectIds') : [] ) }}]);
        $('#supplierIds').multiselect('select', [{{ join(',', Session::has('supplierIds') ? Session::get('supplierIds') : [] ) }}]);
      </script>

@php
  use App\Models\Order;

  $date_from = Session::has('date_from') ? Session::get('date_from') : Order::count() ? Order::orderBy('date')->first()->date : $today;
  $date_to = Session::has('date_to') ? Session::get('date_to') : Order::count() ? Order::orderBy('date', 'DESC')->first()->date : $today;
@endphp

      <div class="col-md-7 px-0">
        <div class="row">
          <div class="input-group mb-3 col-md-5 pr-0">
            <label for="start_on" class="col-md-5 col-form-label text-md-right">日期从</label>

            <div class="col-md-7 px-0">
              <input id="date_from" name="date_from" type="date" class="form-control" value="{{ $date_from }}" required>
            </div>
          </div>

          <div class="input-group mb-3 pl-0 col-md-4">
            <label for="start_on" class="col-md-2 col-form-label text-md-left">到</label>

            <div class="col-md-10 pl-0">
              <input id="date_to" name="date_to" type="date" class="form-control" value="{{ $date_to }}" required>
            </div>
          </div>

          <div class="input-group mb-3 col-md-2">
            <button id="confirmBtn" type="submit" class="btn btn-primary btn-block">
              提交
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@php
  use App\Http\Controllers\OrderItemController;

  $projectIds = Session::get('projectIds');
  $supplierIds = Session::get('supplierIds');

  $items = OrderItemController::getItemsByParams($projectIds, $supplierIds, $date_from, $date_to);

  $table_colspan = 15;

  $headers = [
    '日期' => [ 'width' => 160, 'align' => 'left' ],
    '供应商' => [ 'width' => 160, 'align' => 'left' ],
    '货单号码' => [ 'width' => 120, 'align' => 'left' ],
    '货名' => [ 'width' => 260, 'align' => 'left' ],
    '退' => [ 'width' => 40 ],
    '数量' => [ 'width' => 110 ],
    '价格' => [ 'width' => 80 ],
    '总价格' => [ 'width' => 100 ],
    'SST 银额' => [ 'colspan' => 2, 'width' => 120 ],
    '退货' => [ 'width' => 80 ],
    '总数' => [ 'width' => 100 ],
    '总结' => [ 'width' => 80 ],
    '记录表' => [ 'width' => 120 ],
    '操作' => [ 'width' => 100 ],
  ];

  $gap_color = 'table-secondary';
@endphp

<style media="screen">
  #orderTable td, #orderTable th
  {
    padding-top: 0px;
    padding-bottom: 0px;
  }

  #orderTable td
  {
    font-size: 14px;
  }

  #orderTable .project-name
  {
    font-size: 18px;
  }

  #orderTable .table-headers
  {
    font-size: 13px;
    background: #3490dc;
    color: white;
  }
</style>

<div class="px-1">
  <table id="orderTable" class="table table-sm table-bordered table-hover">
    <tbody>
      @foreach ($items as $project)
      <tr>
        <th class="project-name" colspan="{{ $table_colspan }}">{{ $project->name }}</th>
      </tr>

      <tr class="table-headers">
        @foreach ($headers as $header => $value)
        <th
          colspan="@isset($value['colspan']) {{ $value['colspan'] }} @endisset"
          style="min-width: {{ $value['width'] }}px;
          @isset($value['align']) text-align: {{ $value['align'] }} @endisset">{{ $header }}</th>
        @endforeach
      </tr>
        @php
          $total_counter = 0;
        @endphp

        @foreach ($project->orders as $order)
          @foreach ($order->order_items as $order_item)
            @php
              $total_counter += $order_item->return ? -$order_item->sub_total : $order_item->sub_total;
              $id = $order_item->id;
            @endphp
          <tr id="order_item{{$id}}">
            <td id="order_item{{$id}}-date" class="text-left">{{ $order->date }}</td>
            <td id="order_item{{$id}}-supplier_id" class="text-left" data-id="{{$order->supplier->id}}">{{ $order->supplier->name }}</td>
            <td id="order_item{{$id}}-ref_no" class="text-left">{{ $order->ref_no }}</td>
            <td id="order_item{{$id}}-item_name" class="text-left">{{ $order_item->item->name }}</td>
            <td class="text-left pt-1 pl-2">
              <input id="order_item{{$id}}-return" class="" type="checkbox" @if ($order_item->return) checked @endif disabled aria-label="Checkbox for following text input">
            </td>
            <td id="order_item{{$id}}-quantity" class="" data-unit="{{$order_item->unit ? $order_item->unit->name : ''}}">{{ $order_item->quantity . ' ' . ($order_item->unit ? $order_item->unit->name : '') }}</td>
            <td id="order_item{{$id}}-price" class="text-right @if ($order_item->return) text-danger @endif">{{ $order_item->price }}</td>
            <td id="order_item{{$id}}-total_price" class="text-right @if ($order_item->return) text-danger @endif">{{ $order_item->total_price }}</td>
            <td id="order_item{{$id}}-sst_perc" class="text-right" style="max-width: 20px;">{{ $order_item->sst_perc }}%</td>
            <td id="order_item{{$id}}-sst_amount" class="text-right">{{ $order_item->sst_amount }}</td>
            <td id="order_item{{$id}}-return_amount" class="text-right text-danger">@if ($order_item->return) {{ '-' . $order_item->sub_total }} @endif</td>
            <td id="order_item{{$id}}-sub_total" class="text-right @if ($order_item->return) text-danger @endif">{{ ($order_item->return ? '-' : '') . $order_item->sub_total }}</td>
            <td id="order_item{{$id}}-total_counter" class="text-right">{{ number_format($total_counter, 2) }}</td>
            <td id="order_item{{$id}}-remarks" class="text-right">{{ $order_item->remarks }}</td>
            <td class="text-left">
              <div class="row mx-auto">
                <div class="col p-0 m-0 text-center">
                  <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" href="javascript: toEdit({{ $id }})">修改</a>
                </div>
                <div class="col p-0 m-0 text-left">
                  <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" data-toggle="modal" data-target="#deleteConfirmModal" onclick="javascript: toDelete({{ $order_item->id }})">删除</a>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
          <tr>
            <th class="text-right" colspan="7">Total</th>
            <th class="text-right">{{ $order->total_price }}</th>
            <th></th>
            <th class="text-right">{{ $order->sst_amount }}</th>
            <th></th>
            <th class="text-right">{{ $order->sub_total }}</th>
            <th></th>
            <th></th>
            <th></th>
          </tr>

          <tr>
            <td class="table-gap" style="height: 20px;" colspan="{{ $table_colspan }}"></td>
          </tr>
        @endforeach
        <tr>
          <th class="text-right" colspan="7">Sub Total</th>
          <th class="text-right">{{ $project->total_price }}</th>
          <th></th>
          <th class="text-right">{{ $project->sst_amount }}</th>
          <th></th>
          <th class="text-right">{{ $project->sub_total }}</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
        @php
          $id = $project->id;
        @endphp
        <tr>
          <td>
            <input name="date" type="date" class="form-control form-control-sm" value="{{ $today }}" form="createForm{{$id}}" required>
          </td>
          <td>
            <select name="supplier_id" class="form-control form-control-sm" form="createForm{{$id}}">
              @foreach ($suppliers as $supplier)
              <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
              @endforeach
            </select>
          </td>
          <td>
            <input name="ref_no" type="text" class="form-control form-control-sm" form="createForm{{$id}}" required>
          </td>
          <td>
            <input name="item_name" type="text" class="form-control form-control-sm" form="createForm{{$id}}" required>
          </td>
          <td>
            <input id="return{{$id}}" name="return" class="mt-2" type="checkbox" form="createForm{{$id}}" onchange="updateValues({{$id}})">
          </td>
          <td>
            <div class="input-group input-group-sm">
              <input id="quantity{{$id}}" name="quantity" type="text" class="form-control" oninput="updateValues({{$id}})" form="createForm{{$id}}">
              <input name="unit_name" type="text" class="form-control" form="createForm{{$id}}">
            </div>
          </td>
          <td>
            <input id="price{{$id}}" name="price" type="text" class="form-control form-control-sm text-right" required oninput="updateValues({{$id}})" form="createForm{{$id}}">
          </td>
          <th id="total_price{{$id}}" class="text-right pt-1">
            0.00
          </td>
          <td class="text-right" style="width: 40px">
            <input id="sst_perc{{$id}}" type="text" class="form-control form-control-sm text-right" form="createForm{{$id}}" oninput="updateValues({{$id}})">
          </td>
          <th id="sst_amount{{$id}}" class="text-right pt-1">
            0.00
          </td>
          <th id="return_amount{{$id}}" class="text-right pt-1 text-danger">

          </td>
          <th id="sub_total{{$id}}" class="text-right pt-1">
            0.00
          </td>
          <td>

          </td>
          <td>
            <input name="remarks" type="text" class="form-control form-control-sm" form="createForm{{$id}}">
          </td>
          <td>
            <button class="btn btn-sm btn-success btn-block text-white" type="submit" form="createForm{{$id}}">添加</button>
          </td>
        </tr>

        <tr>
          <td class="table-gap" style="height: 20px;" colspan="{{ $table_colspan }}"></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

@foreach($items as $project)
  <form id="createForm{{ $project->id }}" action="/order_items" method="POST">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <input id="scrollOffset" type="hidden" name="scrollOffset" value="">
  </form>
@endforeach

<script>
  $(window).scroll(function() {
      $('#scrollOffset').val($(document).scrollTop());
  });

  // scrollOffset would be set once the createForm is submitted.
  // It is for auto-scroll to the previous position before the page was reloaded.
  {{ Session::has('scrollOffset') ? 'window.scrollTo(0,' . Session::get('scrollOffset') . ');' : '' }}

  function updateValues(id)
  {
    var quantity = $('#quantity'+id).val() || 0;
    var price = $('#price'+id).val() || 0;
    var sst_perc = $('#sst_perc'+id).val() || 0;

    var total_price = quantity * price;
    var sst_amount = total_price * sst_perc / 100;
    var sub_total = total_price + sst_amount;

    $('#total_price'+id).text(total_price.toFixed(2));
    $('#sst_amount'+id).text(sst_amount.toFixed(2));
    $('#sub_total'+id).text(sub_total.toFixed(2));
    $('#return_amount'+id).text($('#return'+id).is(':checked') ? '-'+sub_total.toFixed(2) : '');
  }

  function toEdit(id)
  {
    var tr = $('#order_item'+id);

    var date = $(`#order_item${id}-date`).text();
    var supplier_id = $(`#order_item${id}-supplier_id`).attr('data-id');
    var ref_no = $(`#order_item${id}-ref_no`).text();
    var item_name = $(`#order_item${id}-item_name`).text();
    var returnItem = $(`#order_item${id}-return`).is(':checked');
    var quantity = parseInt($(`#order_item${id}-quantity`).text());
    var unit = $(`#order_item${id}-quantity`).attr('data-unit');
    var price = parseFloat($(`#order_item${id}-price`).text());
    var sst_perc = parseFloat($(`#order_item${id}-sst_perc`).text()) * 1;
    var remarks = $(`#order_item${id}-remarks`).text();

    $('body').append(`
    <form id="editForm${id}" action="/order_items/${id}" method="POST">
      @csrf
      @method('PUT')
      <input id="scrollOffset" type="hidden" name="scrollOffset" form="editForm${id}">
    </form>
    `);

    tr.hide();
    tr.after(`
    <tr id="editItem${id}">
      <td>
        <input name="date" type="date" class="form-control form-control-sm" value="${date}" form="editForm${id}" required>
      </td>
      <td>
        <select id="editItem${id}-supplier_id" name="supplier_id" class="form-control form-control-sm" form="editForm${id}">
          @foreach ($suppliers as $supplier)
          <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
          @endforeach
        </select>
      </td>
      <td>
        <input id="ref_no" name="ref_no" type="text" value="${ref_no}" class="form-control form-control-sm" form="editForm${id}" required>
      </td>
      <td>
        <input id="item_name" name="item_name" type="text" value="${item_name}" class="form-control form-control-sm" form="editForm${id}" required>
      </td>
      <td>
        <input id="editItem${id}-return" name="return" class="mt-2" type="checkbox" onchange="updateEditItem(${id})" form="editForm${id}">
      </td>
      <td>
        <div class="input-group input-group-sm">
          <input id="editItem${id}-quantity" name="quantity" value="${quantity}" type="text" class="form-control" oninput="updateEditItem(${id})" form="editForm${id}">
          <input name="unit_name" value="${unit}" type="text" class="form-control" form="editForm${id}">
        </div>
      </td>
      <td>
        <input id="editItem${id}-price" name="price" value="${price.toFixed(2)}" type="text" class="form-control form-control-sm text-right" required oninput="updateEditItem(${id})" form="editForm${id}">
      </td>
      <th id="editItem${id}-total_price" class="text-right pt-1">
        0.00
      </td>
      <td class="text-right" style="width: 40px">
        <input id="editItem${id}-sst_perc" value="${sst_perc}" type="text" class="form-control form-control-sm text-right" oninput="updateEditItem(${id})" form="editForm${id}">
      </td>
      <th id="editItem${id}-sst_amount" class="text-right pt-1">
        0.00
      </td>
      <th id="editItem${id}-return_amount" class="text-right pt-1 text-danger">

      </td>
      <th id="editItem${id}-sub_total" class="text-right pt-1">
        0.00
      </td>
      <td>

      </td>
      <td>
        <input name="remarks" value="${remarks}" type="text" class="form-control form-control-sm" form="editForm${id}">
      </td>
      <td>
        <div class="row mx-auto">
          <div class="col p-0 m-0 text-center">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" type="submit" form="editForm${id}"">更新</a>
          </div>
          <div class="col p-0 m-0 text-left">
            <a class="btn btn-sm btn-link py-1 px-2" style="font-size: 12px;height: 24px;" onclick="javascript: cancelEdit(${id})">取消</a>
          </div>
        </div>
      </td>
    </tr>
    `);

    setTimeout(function() {
      $(`#editItem${id}-supplier_id`).val(supplier_id);
      $(`#editItem${id}-return`).prop('checked', returnItem);
      window.updateEditItem(id);
    }, 1);
  }

  function updateEditItem(id)
  {
    var returnItem = $(`editItem${id}-return`).is(':checked');
    var quantity = parseInt($(`#editItem${id}-quantity`).val());
    var price = parseFloat($(`#editItem${id}-price`).val());
    var sst_perc = parseFloat($(`#editItem${id}-sst_perc`).val()) * 1;

    var total_price = quantity * price;
    var sst_amount = total_price * sst_perc / 100;
    var sub_total = total_price + sst_amount;

    $(`#editItem${id}-total_price`).text(total_price.toFixed(2));
    $(`#editItem${id}-sst_amount`).text(sst_amount.toFixed(2));
    $(`#editItem${id}-sub_total`).text(sub_total.toFixed(2));
    $(`#editItem${id}-return_amount`).text($('#return{{$id}}').is(':checked') ? '-'+sub_total.toFixed(2) : '');
  }

  function cancelEdit(id)
  {
    $('#order_item'+id).show();
    $('#editItem'+id).remove();
    $('#editForm'+id).remove();
  }

  var idToDelete;

  function toDelete(id)
  {
    idToDelete = id;
  }

  function confirmDelete()
  {
    axios.delete(`/order_items/${idToDelete}`)
    .then(function() {
      location.reload(true);
    });
  }
</script>

@endsection
