@extends('layouts.parent')
@section('title')
{{ __("companies_all") }}
@endsection
@section('content')
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
                    <div class="card-header">
                        <h3 class="card-title">{{ __("companies_data") }}</h3>

                        <a href="{{ route('companies_add') }}" style="
                  position: absolute;
                  right: 2%;
                  width: auto;
                  bottom: 3px;
              " class="btn btn-primary btn-block">{{__("companies_add")}}</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __("id") }}</th>
                                    <th>{{ __("name") }}</th>
                                    <th>{{ __("companies_commission") }}</th>
                                    <th>{{ __("companies_numbers") }}</th>
                                    <th>{{ __("action") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($company1 as $company)
                                <tr>
                                    <td>{{ $company['id'] }}</td>
                                    <td>{{ $company['name'] }}</td>
                                    <td>{{ $company['commission']}}</td>
                                    <td>
                                        @foreach ($company['num']['0'] as $companynum )
                                        @if(app()->getLocale()=="en")
                                        {{ $companynum->note_en.' - '. $companynum->phone_number }}
                                        @elseif (app()->getLocale()=="ar")
                                        {{ $companynum->note_ar.' - '. $companynum->phone_number }}
                                        @endif
                                        <br>
                                        @endforeach
                                    </td>
                                    <td class="project-actions text-right">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('company_edit',$company['id']) }}">
                                            <i class="fas fa-pencil-alt">
                                            </i>
                                            {{ __("edit") }}
                                        </a>
                                        <form class="btn" action="{{ route('company_delete',$company['id']) }}"
                                            method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button class="btn btn-danger btn-sm" href="#">
                                                <i class="fas fa-trash">
                                                </i>
                                                {{ __("delete") }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ __("id") }}</th>
                                    <th>{{ __("name") }}</th>
                                    <th>{{ __("companies_commission") }}</th>
                                    <th>{{ __("companies_numbers") }}</th>
                                    <th>{{ __("action") }}</th>
                                </tr>
                            </tfoot>
                        </table>
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
    $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

@endsection
