@extends('main')

@php
  use Carbon\Carbon;
  use App\Http\Controllers\ProjectController;

  $suppliers = App\Models\Supplier::get();

  $formCols = [2,8];

  $headers = [
    '工程名称' => [ 'width' => 100 ],
    '开工日期' => [ 'width' => 100 ],
    '完工日期' => [ 'width' => 100 ],
    '工程数额' => [ 'width' => 100 ],
    '地址' => [ 'width' => 400 ],
    '联络人' => [ 'width' => 100 ],
    '电话' => [ 'width' => 100 ],
    '操作' => [ 'width' => 160 ],
  ];

  $projects = ProjectController::index();

  /* Initial Variables */
  $table = 'projects'; // This refers to the route of the table
@endphp

@section('content')

<x-confirm-modal id="deleteConfirmModal" title="确定删除" function="confirmDelete()">确定要删除工程吗？</x-confirm-modal>

<div id="crudAccordion" class="col-md-6 mx-auto my-5">
  <div class="card">
    <div class="card-header" id="headingOne">
      <a type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        <div class="mb-0 h5">
          添加工程
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
            <label for="start_on" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">开工日期</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="start_on" name="start_on" type="date" class="form-control" {{-- value="{{ Carbon::now()->format("Y-m-d") }}" --}}
              autocomplete="current-password">
            </div>
          </div>

          <div class="form-group row">
            <label for="end_on" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">完工日期</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="end_on" name="end_on" type="date" class="form-control" {{-- value="{{ Carbon::now()->format("Y-m-d") }}" --}}
              autocomplete="current-password">
            </div>
          </div>

          <div class="form-group row">
            <label for="cost" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">工程数额</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="cost" name="cost" type="text" class="form-control pos-number-dot">
            </div>
          </div>



          <div class="form-group row">
            <label for="address" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">地址</label>

            <div class="col-md-{{ $formCols[1] }}">
              <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="customer_name" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">联络人</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="customer_name" name="customer_name" type="text" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label for="contact" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">电话</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="contact" name="contact" type="text" class="form-control" required>
            </div>
          </div>

          <div class="form-group row">
            <label for="fax" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">Fax</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="fax" name="fax" type="text" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label for="email" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">Email</label>

            <div class="col-md-{{ $formCols[1] }}">
              <input id="email" name="email" type="email" class="form-control">
            </div>
          </div>

          <div class="form-group row">
            <label for="supplierIds" class="col-md-{{ $formCols[0] }} col-form-label text-md-right">供应商</label>

            <div class="col-md-{{ $formCols[1] }}">
              <div class="input-group px-0 col">
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
            $('#supplierIds').multiselect(supplierMultiSelectConfig);
          </script>

          <div class="form-group row mb-0 justify-content-center">
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

<div class="px-5 mx-auto">
  <table class="table table-sm table-bordered">
    <thead>
      <tr>
        @foreach ($headers as $key => $value)
        <th scope="col" style="width: {{ $value['width'] }}px">{{ $key }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($projects as $project)
      <tr>
        <td>{{ $project->name }}</td>
        <td>{{ $project->start_on }}</td>
        <td>{{ $project->end_on }}</td>
        <td>{{ $project->cost }}</td>
        <td>{{ $project->address }}</td>
        <td>{{ $project->customer->name }}</td>
        <td>{{ $project->customer->contact }}</td>
        <td>
          <div class="row">
            <div class="col p-0 m-0 text-right">
              <a class="btn btn-sm btn-outline-dark" href="#">结帐</a>
            </div>
            <div class="col p-0 m-0 text-center">
              <a class="btn btn-sm btn-outline-primary" href="javascript: toEdit({{ $project->id }})">修改</a>
            </div>
            <div class="col p-0 m-0 text-left">
              <a data-toggle="modal" data-target="#deleteConfirmModal" onclick="javascript: toDelete({{ $project->id }})" class="btn btn-sm btn-outline-danger">删除</a>
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

  var suppliers = {{ ProjectController::jsProjectSupplierIdDict() }};

  // var customers = {
  //   @foreach ($customers as $customer)
  //     {{$customer->name}}: {
  //       contact: '{{$customer->contact}}',
  //       fax: '{{$customer->fax}}',
  //       email: '{{$customer->email}}'
  //     },
  //   @endforeach
  // };

  // function autoComplete()
  // {
  //   var name = $('#customer_name').val();

  //   $('#contact').val('');
  //   $('#fax').val('');
  //   $('#email').val('');

  //   if (customers[name])
  //   {
  //     $('#contact').val(customers[name].contact);
  //     $('#fax').val(customers[name].fax);
  //     $('#email').val(customers[name].email);
  //   }
  // }

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
      var project = res.data;

      $('#name').val(project.name);
      $('#start_on').val(project.start_on);
      $('#end_on').val(project.end_on);
      $('#cost').val(project.cost);
      $('#address').val(project.address);
      $('#customer_name').val(project.customer.name);
      $('#contact').val(project.customer.contact);
      $('#fax').val(project.customer.fax);
      $('#email').val(project.customer.email);
      $('#supplierIds').multiselect('select', suppliers[id]);
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

    $('#name').val('');
    $('#start_on').val('');
    $('#end_on').val('');
    $('#cost').val('');
    $('#address').val('');
    $('#customer_name').val('');
    $('#contact').val('');
    $('#fax').val('');
    $('#email').val('');
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
