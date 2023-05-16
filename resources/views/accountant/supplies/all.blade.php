@extends('layouts.parent')
@section('title')
{{__('orders_delevired_agent').' '.$agentname }}
@endsection
@section('content')
<form action="{{ route('orderssearch') }}" class="form-group" method="post" enctype="multipart/form-data">
    @csrf
    <div class="card-body" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="form-group col-md-6" bis_skin_checked="1">
                <label for="exampleInputFile">{{ __("sheet") }}</label>
                <div class="input-group" bis_skin_checked="1">
                    <div class="custom-file" bis_skin_checked="1">
                        <input name="sheet" value="{{ old('sheet') }}" type="file" id="exampleInputFile"
                            accept=".xlsx,.xls">
                        <label class="custom-file-label" for="exampleInputFile">{{ __("choose_file") }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __("search") }}</button>
                </div>
                @error('sheet')
                <div class="text-danger font-weight-bold">*{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</form>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <div class="card-header">
                        <h3 class="card-title">{{ __("orders_all_data") }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div id="printBar" class="pl-2"></div>
                    <div class="card-body">
                        <form action="{{ route('accountant_supplies_supply') }}" method="post" id="checks">
                            @csrf
                            <label for="payed">{{ __("payed") }}</label>
                            <input required="required" type="number" name="payed" id="payed">
                            <input type="button" class="btn btn-flex btn-success" onclick="myFunction()" value="توريد">
                            <input type="hidden" name="name" value="{{ $agentname }}">
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input class="btn btn-flex btn-primary w-15" type="button" onclick="selects();"
                                value="{{ __(" select_all") }}">
                            <input class="btn btn-flex btn-info w-15" type="button" onclick="deSelect();"
                            value="{{ __("deselect_all") }}">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="checkbox[]"></th>
                                        <th>{{ _("order_id") }}</th>
                                        <th>{{ __("company") }}</th>
                                        <th>{{ __("id_police") }}</th>
                                        <th>{{ __("name_client") }}</th>
                                        <th>{{ __("phone") }}</th>
                                        <th>{{ __("phone2") }}</th>
                                        <th>{{ __("address") }}</th>
                                        <th>{{ __("cost") }}</th>
                                        <th>{{ __("agent_commission") }}</th>
                                        <th>{{ __("total") }}</th>
                                        <th>{{ __("state_order") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                    $total=0;
                    $total1=0;
                    $total2=0;
                    $balance = 0;
                    ?>
                                    @foreach (session()->get('orders') as $order)
                                    <tr>
                                        <td><input type="checkbox" name="{{'checkbox-'.$order->id }}"
                                                value="{{$order->id}}"></td>
                                        <td>
                                            <a class="btn btn-info btn-sm"
                                                href="{{ route('orderedit',$order->id) }}">{{ $order->id }}</a>
                                        </td>
                                        <td>{{ $order->company_name }}</td>
                                        <td>{{ $order->id_police }}</td>
                                        <td>{{ $order->name_client }}</td>
                                        <td>{{ $order->phone }}</td>
                                        <td>{{ $order->phone2 }}</td>
                                        <td>
                                            <textarea disabled type="text" name="" id="">{{$order->address }}</textarea>
                                        </td>
                                        <td>{{ $order->cost }}
                                            <?php $total+=$order->cost;?>
                                        </td>
                                        <td>{{ $order->commision }}
                                            <?php $total1+=$order->commision;?>
                                        </td>
                                        <td>{{ $order->cost - $order->commision}}
                                            <?php $total2+=$order->cost - $order->commision;?>
                                        </td>
                                        <td>{{ $order->state }}</td>
                                    </tr>
                                    <?php $balance+=$order->comp_commission - $order->commision;?>
                                    @endforeach
                                    <tr>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>@isset($orders[0]->company_name)
                                            {{ $orders[0]->company_name}}
                                            @endisset
                                        </td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>{{ $total }}</td>
                                        <td>{{ $total1 }}</td>
                                        <td>{{ $total2 }}
                                            <input type="hidden" name="commission" value="{{ $total1 }}">
                                            <input type="hidden" name="total" value="{{ $total2 }}">
                                            <input type="hidden" name="balance" value="{{ $balance }}">
                                        </td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th><input type="checkbox" name="checkbox[]"></th>
                                        <th>{{ __("order_id") }}</th>
                                        <th>{{ __("company") }}</th>
                                        <th>{{ __("id_police") }}</th>
                                        <th>{{ __("name_client") }}</th>
                                        <th>{{ __("phone") }}</th>
                                        <th>{{ __("phone2") }}</th>
                                        <th>{{ __("address") }}</th>
                                        <th>{{ __("cost") }}</th>
                                        <th>{{ __("agent_commission") }}</th>
                                        <th>{{ __("total") }}</th>
                                        <th>{{ __("state_order") }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
@endsection
@section('js')
<script>
    $('#example1 thead tr').clone(true).appendTo('#example1 thead');
            // $('#example1 thead tr').clone(true).appendTo('#example1 thead').css('display','none');
            $('#example1 thead tr:eq(1) th').each(function(i) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' +
                    title + '" />');
                $('input', this).on('keyup click change', function(e) {
                    e.stopPropagation();
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            })
        var table = $("#example1").DataTable({
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            orderCellsTop: true,
            paging:false,
            buttons: ["excel","copy", "colvis"],
        });
        table.buttons().container().appendTo('#printBar');
</script>
<script>
    function myFunction() {
    document.getElementById("checks").submit();
  }
  var x = document.getElementById("payed").value="{{ $total2 }}";
</script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(function () {
          bsCustomFileInput.init();
        });
</script>
<script>
    $( document ).ready(function() {
        @if (session()->get('orders')!=null)
        @foreach (session()->get('orders') as $order)
          var ele = document.getElementsByName('{{ 'checkbox-' . $order->id }}');
          for (var i = 0; i < ele.length; i++) {
              if (ele[i].type == 'checkbox')
                  ele[i].checked = true;
          }
        @endforeach
        @endif
});
    function selects() {
      @foreach (session()->get('orders') as $order)
          var ele = document.getElementsByName('{{ 'checkbox-' . $order->id }}');
          for (var i = 0; i < ele.length; i++) {
              if (ele[i].type == 'checkbox')
                  ele[i].checked = true;
          }
      @endforeach
  }

  function deSelect() {
      @foreach (session()->get('orders') as $order)
          var ele = document.getElementsByName('{{ 'checkbox-' . $order->id }}');
          for (var i = 0; i < ele.length; i++) {
              if (ele[i].type == 'checkbox')
                  ele[i].checked = false;
          }
      @endforeach
  }
</script>
@endsection
