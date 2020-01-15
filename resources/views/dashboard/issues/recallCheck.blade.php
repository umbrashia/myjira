<div>
    <i class="fas fa-check  bg-green"></i>
    <div class="timeline-item">
        <span class="time"><i class="fas fa-clock"></i> complete</span>
        <h3 class="timeline-header"><a href="">Story</a></h3>

        <div class="timeline-body ">
            <div class="progress">
                @if($totalCount)
                <?php $percent=ceil(($request->startAt*100)/$request->total);?>
                <div class="progress-bar" role="progressbar" style="width: {{$percent}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$percent}}%</div>
            </div>
        </div>
        <!--<div class="timeline-footer"></div>-->

    </div>
</div>

