
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
        <form method="POST" action="{{url('dashboard/issues/update-issues-with-sub')}}">
            @csrf
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
                    @if(Session::has("successMessage"))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        {{Session::get('message')}}
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Developer Working Hours</label>
                                <input type="number" class="form-control myDevHours"  value="16" placeholder="Enter ...">
                            </div>
                            <!-- /.form-group -->
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>My Start Date</label>
                                <div class="input-group">
                                    <input type="date" value="2020-02-01" class="form-control myStartDate">
                                    <span class="input-group-append">
                                        <button type="button" id="calculateEstimation" class="btn btn-info btn-flat">Go!</button>
                                    </span>
                                </div>

<!--<input type="text" class="form-control myStartDate"  value="" placeholder="Enter ...">-->
                            </div>
                            <!-- /.form-group -->
                        </div>
                        <?php
                        $cols = array(
//                                    'main_issue_id',
//                                    'expand',
//                                    'id',
//                                    'self',
//                                'key',
                            "summary" => 'summary',
//                                    'issue_type',
                            "timespent" => 'timespent',
//                                    'aggregatetimespent',
//                                    'workratio',
//                                'created',
                            'story_point' => 'story_point',
                            "start_date" => 'start_date',
                            "estimate" => 'timeestimate',
                            "duedate" => 'duedate',
                            "actual_start_date" => 'actual_start_date',
                            "actual_end_date" => 'actual_end_date',
//                                "assignee"=>"assignee_name",
//                                    'updated',
//                                    'customfield_10011',
//                                    'aggregatetimeestimate',
//                                    'customfield_10000',
//                                    'assignee_id',
//                                    'reporter_id',
                        );

                        foreach ($cols as $key => $val) {
                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{$key}}</label>
                                    <input type="text" name="{{$val}}[]" class="form-control story_{{$val}}"  value="<?php echo $issue->{$val}; ?>" placeholder="Enter ...">
                                </div>
                                <!-- /.form-group -->
                            </div>
                        <?php } ?>
                        <input type="hidden" name="key[]" value="{{$issue->key}}"/>
                        <input type="hidden" name="main_issue_id[]" value="{{$issue->main_issue_id}}"/>
                        <!-- /.col -->
                        @if(false)
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
                        @endif
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
                                <th>no</th>
                                <th>summary</th>
                                <?php
                                $cols = array(
//                                    'main_issue_id',
//                                    'expand',
//                                    'id',
//                                    'self',
//                                'key',
//                                "summary" => 'summary',
//                                    'issue_type',
//                                    'order'=>'subtask_type_order',
//                                    "timespent" => 'timespent',
//                                    'aggregatetimespent',
//                                    'workratio',
//                                'created',
                                    "estimate" => 'timeestimate',
//                                'story_point',
                                    "start" => 'start_date',
                                    "duedate" => 'duedate',
                                    "a_s_date" => 'actual_start_date',
                                    "a_e_date" => 'actual_end_date',
                                    "assignee" => "assignee_name",
//                                    'updated',
//                                    'customfield_10011',
//                                    'aggregatetimeestimate',
//                                    'customfield_10000',
//                                    'assignee_id',
//                                    'reporter_id',
                                );
                                foreach ($cols as $key => $val) {
                                    ?>
                                    <th>{{$key}}</th>
                                <?php } ?>
<!--<th>Action</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//                        dd($issue->getLinkedIssue()->where('issue_type','Sub-task')->get());
                            foreach ($subIssues as $value) {
                                ?>


                                <tr class="<?php echo \Illuminate\Support\Str::snake($value->subtask_type); ?>"> 
                                    <td>{{$value->subtask_type_order}}</td>
                                    <td>
                                        <input type="hidden" name="key[]" value="{{$value->key}}"/>
                                        <input type="hidden" name="main_issue_id[]" value="{{$value->main_issue_id}}"/>
                                        <a target="_blank" href="{{url(env('JIRA_DOMAIN'))}}browse/{{$value->key}}">{{$value->summary}}</a></td>
                                    <?php foreach ($cols as $val) { ?>
                                        <td ><input class="sub_{{$val}} form-control" name="{{$val}}[]" value="<?php echo $value->{$val}; ?>"/></td>
                                    <?php } ?>
    <!--<td><a href="{{url('dashboard/issues/view-issue',$value->main_issue_id)}}"><i class="fas fa-pencil-alt"></i></a></td>-->
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    --------------------
                    <button class="btn btn-flat btn-lg btn-info">SAVE</button>
                    --------------------
                </div>
            </div>
            <!-- /.card -->
        </form>
    </div>
</section>

@endsection
@section('scripts')
<!-- DataTables -->
<script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{ asset('dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<!--<script src="{{ asset('dashboard/plugins/moment-js/moment-with-locales.js')}}"></script>-->
<script>
$(function () {

    $('#calculateEstimation').click(function () {
        ;
        parseInt($('.myDevHours').val());
        $.get('dashboard/issues/get-estimation',
                {
                    codingHours: $('.myDevHours').val(),
                    startDate: $('.myStartDate').val()
                }, function (data) {
            $('.story_start_date').val(data['story']['startDate']);
            $('.story_duedate').val(data['story']['dueDate']);
            $('.story_timeestimate').val(data['story']['hours_round']);
            // C O D E   R E V I E W 
            $('.code_review').find('.sub_timeestimate').val(data["code_review"]['hours_round']);
            $('.code_review').find('.sub_start_date').val(data["code_review"]['startDate']);
            $('.code_review').find('.sub_duedate').val(data["code_review"]['dueDate']);
            // I M P A C T   A N A L I S I S 
            $('.impact').find('.sub_timeestimate').val(data["impact"]['hours_round']);
            $('.impact').find('.sub_start_date').val(data["impact"]['startDate']);
            $('.impact').find('.sub_duedate').val(data["impact"]['dueDate']);
            // P R O J E C T   M A N  A G E M E N T
            $('.project_management').find('.sub_timeestimate').val(data["project_management"]['hours_round']);
            $('.project_management').find('.sub_start_date').val(data["project_management"]['startDate']);
            $('.project_management').find('.sub_duedate').val(data["project_management"]['dueDate']);
            // E S T I M A T I O N
            $('.estimation').find('.sub_timeestimate').val(data["estimation"]['hours_round']);
            $('.estimation').find('.sub_start_date').val(data["estimation"]['startDate']);
            $('.estimation').find('.sub_duedate').val(data["estimation"]['dueDate']);
            // C O D E I N G
            $('.coding').find('.sub_timeestimate').val(data["coding"]['hours_round']);
            $('.coding').find('.sub_start_date').val(data["coding"]['startDate']);
            $('.coding').find('.sub_duedate').val(data["coding"]['dueDate']);
            // Q A   T E S T I N G
            $('.testing').find('.sub_timeestimate').val(data["testing"]['hours_round']);
            $('.testing').find('.sub_start_date').val(data["testing"]['startDate']);
            $('.testing').find('.sub_duedate').val(data["testing"]['dueDate']);
            // R E L E A S E   M A N A G E M E N T 
            $('.release_management').find('.sub_timeestimate').val(data["release_management"]['hours_round']);
            $('.release_management').find('.sub_start_date').val(data["release_management"]['startDate']);
            $('.release_management').find('.sub_duedate').val(data["release_management"]['dueDate']);

        });
    });

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

