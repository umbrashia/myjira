
@extends('dashboard.layouts.master')
@section('styles')
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Jira Import Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Fixed Layout</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Timelime example  -->
        <div class="row">
            <div class="col-md-12">
                <!-- The time line -->
                <div class="timeline">
                    <!-- timeline time label -->
                    <!--                    <div class="time-label">
                                            <span class="bg-red">10 Feb. 2014</span>
                                        </div>-->
                    <!-- /.timeline-label -->
                    <!-- timeline item -->
                    <div>
                        <i class="fas fa-bolt  bg-blue"></i>

                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 12:05</span>
                            <h3 class="timeline-header"><a href="">First You Select Project</a></h3>

                            <!--                            <div class="timeline-body ">
                                                            you have to select first project then you have to click import button
                                                        </div>-->
                            <div class="timeline-footer">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Choose Project</label>
                                    <select class="form-control" id="projectSelect">
                                        @foreach ($projects as $pro)                                        
                                        <option value="{{$pro->key}}">{{$pro->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button  class="btn btn-primary btn-sm" type="button" onclick="doEventDataRecall(this)">do Import</button>
                                </div>
                            </div>



                        </div>
                    </div>

                    <!-- END timeline item -->
                    <!-- timeline item -->
                </div>

                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-success myJiraLoader" style="display: none;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <br/><br/><br/>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection


@section('scripts')
<!-- DataTables -->
<!--<script src="{{ asset('dashboard/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{ asset('dashboard/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>-->
<script>
    var importDataEngine = {
        step: 0,
        count: 0,
        project: "",
        next: null,
        html: ""
    };
    var temp=importDataEngine;

    function recallData() {
        $.post("dashboard/issue-listing", importDataEngine, function (res) {
//            console.log(res)
//            debugger;
            importDataEngine = res;
            $('.timeline').append(importDataEngine.html);
//            $(document).animate({scrollTop: }, 1000);
            if (importDataEngine.next)
                recallData();
            else{
                $(".myJiraLoader").fadeOut();
                importDataEngine=temp;
                
            }
            $(document).scrollTop($(document).height());
        });
        $(".myJiraLoader").fadeIn();
    }

    function doEventDataRecall(This) {
        $(This).prop('disabled',true).text("...");
        importDataEngine.project = $('#projectSelect').val();
        recallData();
    }

</script>
@endsection