<div>
    <i class="fas fa-check  bg-green"></i>
    <div class="timeline-item">
        <span class="time"><i class="fas fa-clock"></i> {{($next && $count!=0 && $response)?$response['startAt']."/".$response['total']:"Done"}}</span>
        <h3 class="timeline-header"><a href="">{{$title}} </a></h3>
        @if($next && $count!=0 && $response) 
       


            {{-- $response['startAt']--}}
            <!--            <div class="progress">
            <?php $percent = ceil(($response['startAt'] * 100) / $response['total']); ?>
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: {{$percent}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$percent}}%</div>
                        </div>-->
            @else
            <div class="timeline-body ">
                <div class="progress"> 
                    <?php $percent = 100; ?>
                    <div class="progress-bar" role="progressbar" style="width: {{$percent}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$percent}}%</div>
                </div>
            </div>
            @endif

            <!--<div class="timeline-footer"></div>-->

        </div>
    </div>

