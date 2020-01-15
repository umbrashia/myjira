
@extends('dashboard.layouts.master')
@section('styles')
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Fixed Layout</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Layout</a></li>
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
                                <form>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Choose Project</label>
                                        <select class="form-control">

                                            <option value="">Select Project</option>
                                            @foreach ($projects as $pro)                                        
                                            <option value="{{$pro->key}}">{{$pro->name}}</option>

                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm">do Import</button>
                                    </div>


                                </form>
                            </div>



                        </div>
                    </div>
                    <div class="d-flex justify-content-center time-label">

                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <!-- END timeline item -->
                    <!-- timeline item -->
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
    var step=0;
function recallData(){
    $.post("dashboard/issue-listing/"+step,{},function(res){console.log(res)});
}
recallData();
</script>
@endsection