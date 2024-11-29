@extends('layouts.backendsettings')
@section('title', 'Activity Reports')
@section('content')
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

<div class="flex-grow h-100 main">
    <div class="flex w-full h-full flex-col content">
        <div class="px-9 py-3.5 lg:py-6 lg:px-5">
            <div class="flex items-center gap-4">
                <i class="ri-settings-3-fill ri-xl"></i>
                <span class="text-lg text-c-black font-normal">Activity Reports</span>
            </div>
        </div>

        <div class="pl-4 md:pl-6 py-4 pr-4 md:pr-6 w-full flex flex-row justify-between items-center taskbar">
            <div class="w-full md:w-6/12 xl:w-8/12">
                <div class="flex items-center gap-1 sm:gap-2">
                    <span class="text-c-light-black whitespace-nowrap font-normal">
                        Reports and Analytics
                    </span>
                    <i class="ri-arrow-right-line ri-lg text-c-light-black"></i>
                    <span class="font-semibold text-c-black">
                        Activity Reports
                    </span>
                </div>
            </div>
            <div class="relative taskicon hidden md:flex md:w-5/12 flex flex-row items-center justify-end gap-6">
                <button class="has-tooltip" id="add-btn">
                    <i class="ri-add-circle-fill ri-xl"></i>
                </button>
                <div class="absolute py-1 px-2 text-start text-xs tooltip -bottom-8 right-5 z-10 bg-white border rounded-md border-c-yellow z-0 font-normal">
                    Add Graph
                </div>
            </div>
        </div>

        @include('analitics.filter')

        <!-- Main content -->
        <div class="p-4 relative h-full flex flex-col main-content overflow-y-scroll scroll">
            <div class="bg-white cs-table-container border border-c-gray rounded-md mt-5">
                <!-- <div class="graph-modal graph-effect-1" id="modal">
                    <div style="height: 400px;">
                        <canvas id="successful-logout-chart"></canvas>
                    </div>
                </div> -->
                <div class="graph-container">
                    <div class="p-4 w-full">
                        <div id="renderGraph">
                            @include('analitics.graph')
                        </div>
                    </div>
                </div>
                <!-- <div class="md-overlay">
                    <button class="nav-btn left" id="prev-slide"><i class="ri-arrow-left-wide-line"></i></button>
                    <button class="nav-btn right" id="next-slide"><i class="ri-arrow-right-wide-line"></i></button>
                    <div class="nav-track" id="nav-track">
                        <span id="slide-indicator"></span>
                    </div>
                </div> -->
            </div>
            <div class="mt-auto flex justify-end pt-3 font-normal">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset($constants['JSFILEPATH'] . 'analitics.js') }}"></script>
<script src="{{ asset($constants['JSFILEPATH'] . 'graph-setup.js') }}"></script>
<script src="{{ asset($constants['JSFILEPATH'] . 'reports-analytics.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/helpers.esm.min.js">
</script>
@endsection