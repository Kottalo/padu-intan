@extends('main')

@php
  use Carbon\Carbon;
  use App\Http\Controllers\SupplierController;

  $formCols = [2,8];

  $headers = [
    '供应商名称' => [ 'width' => 200 ],
    '电话' => [ 'width' => 100 ],
    '地址' => [ 'width' => 400 ],
    '顺序' => [ 'width' => 50 ],
    '操作' => [ 'width' => 160 ],
  ];

  $suppliers = SupplierController::index();

  /* Initial Variables */
  $table = 'suppliers'; // This refers to the route of the table
@endphp

@section('content')
<x-confirm-modal id="deleteConfirmModal" title="确定删除" function="confirmDelete()">确定要删除供应商吗？</x-confirm-modal>

<div id="crudAccordion" class="accordion col-md-8 mx-auto my-5">
  <div class="card">
    <div class="card-header" id="headingOne">
      <a type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <div class="mb-0 h5">
          添加供应商
        </div>
      </a>
    </div>

    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#crudAccordion">
      <div class="card-body">
        <form id="crudForm" method="POST" action="/{{ $table }}">
          @csrf

          <div class="form-group row">
            <label for="name" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">名称</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="name" name="name" type="text" class="form-control" required autofocus>
            </div>
          </div>

          <div class="form-group row">
            <label for="contact" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">电话</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="contact" name="contact" type="text" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label for="address" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">地址</label>

            <div class="col-md-{{ $formCols[1] }}">
              <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
            </div>
          </div>

          <div class="input-group row">
            <label for="orderCheckbox" class="col-md-2 col-form-label text-md-right">显示在上层</label>

            <div class="col-md-1 ml-0 mt-2 text-left">
              <input id="orderCheckbox" type="checkbox" aria-label="Checkbox for following text input">
            </div>

            <div class="input-group col-md-3">
              <div class="input-group-prepend">
                <label class="input-group-text" for="inputGroupSelect01">顺序</label>
              </div>
              <select id="order" name="order" class="custom-select" disabled>
                @for ($i=1;$i<=10;$i++)
                <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>

          <div class="form-group row mb-0 mt-4 justify-content-center">
            <div class="col-md-6">
              <div class="row">
                <div class="col">
                  <button id="confirmBtn" type="submit" class="btn btn-primary btn-block">
                    添加
                  </button>
                </div>
                <div class="col">
                  <button id="cancelBtn" type="button" class="btn btn-secondary btn-block" onclick="clearForm()">
                    清除
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="px-5 mx-auto col-md-10">
  <table class="table table-sm table-bordered">
    <thead>
      <tr>
        @foreach ($headers as $key => $value)
        <th scope="col" style="width: {{ $value['width'] }}px">{{ $key }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($suppliers as $supplier)
      <tr @if ($supplier->order) class="table-warning" @endif>
        <td>{{ $supplier->name }}</td>
        <td>{{ $supplier->contact }}</td>
        <td>{{ $supplier->address }}</td>
        <td>{{ $supplier->order }}</td>
        <td>
          <div class="row">
            <div class="col p-0 m-0 text-center">
              <a class="btn btn-sm btn-outline-primary" href="javascript: toEdit({{ $supplier->id }})">修改</a>
            </div>
            <div class="col p-0 m-0 text-left">
              <a data-toggle="modal" data-target="#deleteConfirmModal" onclick="javascript: toDelete({{ $supplier->id }})" class="btn btn-sm btn-outline-danger">删除</a>
            </div>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  $(document).ready(function() {
    clearForm();
  });

  $('#orderCheckbox').on('change', function() {
    $('#order').attr('disabled', !$(this).is(":checked"));
  });

  var idToEdit;

  function toEdit(id)
  {
    idToEdit = id;

    $('#confirmBtn').text('更新');
    $('#cancelBtn').text('取消');

    $('<input>').attr({
      type: 'hidden',
      id: 'put',
      name: '_method',
      value: "PUT"
    }).appendTo('#crudForm');

    var url = `/{{ $table }}/${id}`;

    $('#crudForm').attr('action', url)

    axios.get(`${url}/edit`)
    .then(function(res) {
      var supplier = res.data;

      $('#name').val(supplier.name);
      $('#contact').val(supplier.contact);
      $('#address').val(supplier.address);
      if (supplier.order) $('#order').val(supplier.order);

      $('#orderCheckbox').prop('checked', supplier.order);
      $('#order').prop('disabled', !supplier.order);
    });

    $('#name').focus();

    $("#crudAccordion")[0].scrollIntoView()
  }

  function clearForm()
  {
    $('#crudForm').attr('action', "/{{ $table }}")

    $('#put').remove();

    $('#confirmBtn').text('添加');
    $('#cancelBtn').text('清除');

    $('#orderCheckbox').prop('checked', false);
    $('#order').attr('disabled', true);

    $('#name').val('');
    $('#contact').val('');
    $('#address').val('');
    $('#order').val(10);
  }

  var idToDelete;

  function toDelete(id)
  {
    idToDelete = id;
  }

  function confirmDelete()
  {
    axios.delete(`/{{ $table }}/${idToDelete}`)
    .then(function() {
      location.reload(true);
    });
  }

</script>
@endsection
