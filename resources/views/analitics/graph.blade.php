
<link rel="stylesheet" href="{{ asset($constants['CSSFILEPATH'] . 'common.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .custom-safety-btn.active {
        border-color: yellow;
        background-color: #f7f7f7;
        color: #333;
    }

    .custom-safety-btn {
        border-color: transparent;
        background-color: #ffffff;
        color: #333;
    }
</style>
<div class="graph-area rounded mt-6 relative">
    <div class="graph-hidden-area">
        <div class="pr-3 pt-4 flex gap-3 justify-end">
            <!-- <i class="ri-eye-off-fill ri-lg" id="md-trigger"></i> -->
            <!-- <i class="ri-eye-fill ri-lg hidden" id="md-close"></i> -->
            <i class="ri-close-circle-fill ri-lg close-graph"></i>
        </div>
        <div class="graph-show">
            <div id="user-login-over-time-graph" class="">
                <div class="text-c-black font-medium text-xl text-center py-3">
                </div>
                <div class="pt-2">
                    <div style="height: 370px;" id="chartContainer">
                        <canvas id="chartId" width="500" height="250"></canvas>
                    </div>
                </div>
                <div class="text-c-black font-normal text-lg text-center py-3">
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-center items-center h-full hidden suggestion">
        <h1 class="text-4xl text-c-black">
            Select Graph From Filter
        </h1>
    </div>
</div>

@section('scripts')
<script src="{{ asset($constants['JSFILEPATH'] . 'analitics.js') }}"></script>
<script src="{{ asset($constants['JSFILEPATH'] . 'graph-setup.js') }}"></script>
<script src="{{ asset($constants['JSFILEPATH'] . 'reports-analytics.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/helpers.esm.min.js">
</script>
@endsection