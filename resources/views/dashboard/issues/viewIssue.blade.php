
@extends('dashboard.layouts.master')
@section('styles')
<link rel="stylesheet" href="{{ asset('dashboard/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manage Issue</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">View Issue ("Edit issue")</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">{{$issue->key}}</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>summary</label>
                            <input type="text" class="form-control" value="{{$issue->summary}}" placeholder="Enter ...">
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label>Disabled</label>
                            <input type="text" class="form-control" placeholder="Enter ...">
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Multiple</label>
                            <input type="text" class="form-control" placeholder="Enter ...">
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label>Disabled Result</label>
                            <input type="text" class="form-control" placeholder="Enter ...">
                        </div>
                        <!-- /.form-group -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->


                <!-- /.row -->
            </div>
            <!-- /.card-body -->

        </div>

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">{{$issue->key}} Sub-Task</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <!-- /.row -->

                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php
                            $cols = array(
//                                    'main_issue_id',
//                                    'expand',
//                                    'id',
//                                    'self',
//                                'key',
                                'summary',
//                                    'issue_type',
                                'timespent',
//                                    'aggregatetimespent',
//                                    'workratio',
//                                'created',
                                'timeestimate',
//                                'story_point',
                                'start_date',
                                'duedate',
                                'actual_start_date',
                                'actual_end_date',
                                
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
                        <?php 
//                        dd($issue->getLinkedIssue()->where('issue_type','Sub-task')->get());
                        foreach ($issue->getLinkedIssue()->where('issue_type','Sub-task')->get() as $value) { ?>
                            <tr>
                                <?php foreach ($cols as $val) { ?>
                                    <td><?php echo $value->getIssue()->first()->{$val}; ?></td>
                                <?php } ?>
                                <td><a href="{{url('dashboard/issues/view-issue',$value->main_issue_id)}}"><i class="fas fa-pencil-alt"></i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- /.row -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
                the plugin.
            </div>
        </div>
        <!-- /.card -->

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
        "pageLength": 50
    });
});
</script>
@endsection

