@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
  @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
@stop

@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-default">
            <div class="card-header">
              <h3 class="card-title">オークション登録</h3>
            </div>
            <div class="card-body">
              <!-- Date -->
              <div class="form-group">
                <p>オークション日付選択</p><br>
                <form method="GET" action="{{ route('admin.regist.auction.date') }}">
                  <input type="date" name="date" id="date" value="2021-01-30" step="7">
                  <button type="submit" name=button value="submit" class="btn btn-primary">送信</button><br>
                  <button type="submit" name=button value="test" class="btn btn-danger">テスト用</button><p>※現在時刻の1分後に1分間のオークション開始</p>
                </form>
              </div>
              <!-- /.form group -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              Visit <a href="https://github.com/Johann-S/bs-stepper/#how-to-use-it">bs-stepper documentation</a> for more examples and information about the plugin.
            </div>
          </div>
          <!-- /.card -->
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </section>
<!-- /.content -->
@stop

@section('css')

@stop

@section('js')

@stop