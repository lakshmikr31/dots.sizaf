<div class="default-filter flex items-center gap-2 flex-wrap">
    <!-- parent  -->
    <div class="px-4 py-3 md:px-6 md:py-4 flex justify-between items-center bg-c-light-white-smoke">
        <!-- <label for="actGroupDropdown" class="font-semibold text-c-black">Select Activity Group:</label> -->
        <select id="actGroupDropdown" class="w-full md:w-1/3 p-2 bg-white border border-c-gray rounded-md outline-none text-c-black">
            <option value="">All Activity Group</option>
            @foreach($activityGroups->where('parent', 0) as $activityGroup)
            <option value="{{ $activityGroup->id }}">{{ $activityGroup->name }}</option>
            @endforeach
        </select>

        <!-- <label for="actTypeDropdown" class="font-semibold text-c-black">Select Activity Type:</label> -->
        <select id="actTypeDropdown" class="w-full md:w-1/3 p-2 bg-white border border-c-gray rounded-md outline-none text-c-black hidden">
            <option value="">All Activity Type</option>
        </select>

        <!-- <label for="graphTypeDropdown" class="font-semibold text-c-black">Select Graph Type:</label> -->
        <select id="graphTypeDropdown" class="w-full md:w-1/3 p-2 bg-white border border-c-gray rounded-md outline-none text-c-black hidden">
            <option value="">All Graph Type</option>
        </select>

        <button
            class="custom-safety-btn rounded px-6 py-1 mr-1 hover:border-yellow-300 timeFrame">
            Today
        </button>
        <button
            class="custom-safety-btn focus:border-yellow-500 rounded px-6 py-1 mr-1 hover:border-yellow-300 timeFrame">
            Last 7 days
        </button>
        <button
            class="custom-safety-btn rounded px-6 py-1 mr-1 hover:border-yellow-300 timeFrame">
            Last 30 days
        </button>
        <button id="customize"
            class="custom-safety-btn rounded px-6 py-1 mr-1 hover:border-yellow-300 timeFrame">
            Custom Date
        </button>
        <div class="date-select custom-safety-btn rounded px-6  mr-1 hover:border-yellow-300 hidden">
            <input type="datetime-local" id="start-date" name="start-date" class="outline-none bg-gray-100 w-44 py-1 pl-2">
        </div>
        <div class="date-select custom-safety-btn rounded px-6 mr-1 hover:border-yellow-300 hidden">
            <input type="datetime-local" id="end-date" name="end-date" class="outline-none bg-gray-100 w-44 py-1 pl-2">
        </div>
    </div>

</div>

@section('scripts')
<script>
    $(document).ready(function() {
        const actGroupDropdown = $('#actGroupDropdown');
        const actTypeDropdown = $('#actTypeDropdown');
        const graphTypeDropdown = $('#graphTypeDropdown');


        // populateTable();

        // function populateTable(activityGroupId = '', activityTypeId = '', graphType = '') {
        //     const graphRoute = @json(route('graph.view'));
        //     $.ajax({
        //         url: graphRoute,
        //         method: 'GET',
        //         data: {
        //             activityGroupId: activityGroupId,
        //             activityTypeId: activityTypeId,
        //             graphType: graphType
        //         },
        //         success: function(response) {
        //             $('#searchableTableBody').html(response.html);
        //         },
        //         error: function(xhr) {
        //             console.error(xhr.responseText);
        //         }
        //     });
        // }

        // Show/hide company dropdown based on activity group selection
        actGroupDropdown.on('change', function() {
            const activityGroupId = $(this).val();
            // populateTable(activityGroupId);
            if (activityGroupId) {
                actTypeDropdown.removeClass('hidden');
                const fetchActivityGroupRoute = @json(route('fetch.actType.by.actGroup'));
                $.ajax({
                    url: fetchActivityGroupRoute,
                    method: 'GET',
                    data: {
                        activityGroupId: activityGroupId
                    },
                    success: function(activityTypes) {
                        actTypeDropdown.empty().append('<option value="">All Activity Type</option>');
                        $.each(activityTypes, function(index, activityGroup) {
                            actTypeDropdown.append('<option value="' + activityGroup.id + '">' + activityGroup.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            } else {
                actTypeDropdown.addClass('hidden').empty();
                graphTypeDropdown.addClass('hidden');
            }
        });

        // Show/hide graph type dropdown based on activity type selection
        actTypeDropdown.on('change', function() {
            const activityTypeId = $(this).val();
            if (activityTypeId) {
                graphTypeDropdown.removeClass('hidden');
                const fetchActivityTypeRoute = @json(route('fetch.graphType.by.actType'));
                $.ajax({
                    url: fetchActivityTypeRoute,
                    method: 'GET',
                    data: {
                        activityTypeId: activityTypeId
                    },
                    success: function(graphTypes) {
                        console.log('Graph Types Received:', graphTypes);
                        graphTypeDropdown.empty().append('<option value="">All Graph Type</option>');
                        if (graphTypes.length > 0) {
                            $.each(graphTypes, function(index, graphType) {
                                graphTypeDropdown.append('<option value="' + graphType + '">' + graphType + '</option>');
                            });
                        } else {
                            console.warn('No graph types available.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching graph types:', xhr.responseText);
                    }
                });
            } else {
                graphTypeDropdown.addClass('hidden').empty();
            }
        });


        // Show/hide graph type dropdown based on activity type selection
        graphTypeDropdown.on('change', function() {
            const activityGroupId = $(this).val();
            const activityTypeId = $(this).val();
            const graphType = $(this).val();
            if (activityTypeId) {
                const fetchGraphRoute = @json(route('graph.view'));
                $.ajax({
                    url: fetchGraphRoute,
                    method: 'GET',
                    data: {
                        activityGroupId: activityGroupId,
                        activityTypeId: activityTypeId,
                        graphType: graphType
                    },
                    success: function(response) {
                        console.log('Graph Data Received:', response);
                        $('#renderGraph').html(response.html);
                    },
                    error: function(xhr) {
                        console.error('Error fetching graph types:', xhr.responseText);
                    }
                });
            } else {
                graphTypeDropdown.addClass('hidden').empty();
            }
        });


    });
</script>
@endsection