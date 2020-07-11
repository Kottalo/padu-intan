<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $id }}">{{ $title }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ $slot }}
      </div>
      <div class="modal-footer text-center">
        <div class="container-fluid">
          <div class="col-md-6 mx-auto">
            <div class="row">
              <div class="col">
                <button type="button" class="btn btn-primary btn-block" onclick="{{ $function }}">确定</button>
              </div>
              <div class="col">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">取消</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
