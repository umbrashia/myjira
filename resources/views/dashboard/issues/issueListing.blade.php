
@extends('dashboard.layouts.master')
@section('styles')
<link rel="stylesheet" href="{{ asset('dashboard/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Issues Listing</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Issues Listing</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listing data....</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body" >
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php
                                $cols = array(
//                                    'main_issue_id',
//                                    'expand',
//                                    'id',
//                                    'self',
                                    'key',
                                    'summary',
//                                    'issue_type',
                                    'timespent',
//                                    'aggregatetimespent',
//                                    'workratio',
                                    'created',
                                    'timeestimate',
//                                    'updated',
//                                    'customfield_10011',
//                                    'aggregatetimeestimate',
//                                    'customfield_10000',
//                                    'assignee_id',
//                                    'reporter_id',
                                );
                                foreach ($cols as $val) {
                                    ?>
                                    <th>{{$val}}</th>
                                <?php } ?>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issues as $value) { ?>
                                <tr>
                                    <?php foreach ($cols as $val) { ?>
                                        <td><?php echo $value->{$val}; ?></td>
                                    <?php } ?>
                                    <td>
                                        <div class="btn-group">
                                            <a type="button" class="btn btn-warning" href="{{url('dashboard/issues/view-issue',$value->main_issue_id)}}"><i class="fas fa-pencil-alt"></i></a>
                                            <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                <span class="sr-only">Toggle Dropdown</span>
                                                <div class="dropdown-menu" role="menu">
                                                    <a class="dropdown-item text-danger" href="{{url('dashboard/issues/issue-action',$value->main_issue_id)}}"><i class="fas fa-street-view"></i> Issue Action</a>
<!--                                                    <a class="dropdown-item" href="#"><i class="nav-icon fab fa-phoenix-framework"></i></a>
                                                    <a class="dropdown-item" href="#"><i class="nav-icon fab fa-phoenix-framework"></i></a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#"><i class="nav-icon fab fa-phoenix-framework"></i></a>-->
                                                </div>
                                            </button>
                                        </div>


                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@section('scripts')
<!-- DataTables -->
<script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{ asset('dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script>
$(function () {
//    $("#example1").DataTable();
    $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
    });
});
</script>
@endsection