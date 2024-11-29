@extends('layouts.backendsettings')
@section('title', 'Activity Reports')
@section('content')
<link rel="stylesheet" href="{{ asset($constants['CSSFILEPATH'] . 'common.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<title>Activity Reports</title>

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

<main class="flex w-full h-full cm">

  <!-- main content -->
  <div class="flex-grow border h-full main">
    <div class="flex w-full h-full flex-col content">
      <div class="px-2 lg:px-5 py-6">
        <div class="flex items-center gap-4">
          <i class="ri-settings-3-fill ri-xl"></i>
          <span class="text-lg text-color-nav-black">Activity Reports </span>
        </div>
      </div>

      <!-- top taskbar -->
      <div class="taskbar flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-2 sm:gap-4 w-full md:w-1/2">
          <div class="flex items-center gap-1 sm:gap-2">
            <span class="text-c-light-black text-sm sm:text-base"> Reports and Analytics </span>
            <i class="ri-arrow-right-line ri-sm sm:ri-lg" style="color: #4D4D4D;"></i>
            <span class="font-semibold text-c-black text-sm sm:text-base"> Activity Reports </span>
          </div>
        </div>
        <div class="flex-grow md:w-1/2">
          <div class="flex items-center justify-end gap-2 md:gap-6">
            <button id="add-btn"
              class="flex items-center justify-center gap-2 bg-c-black text-c-yellow px-3 sm:px-4 py-1 sm:py-1.5 rounded-md w-22">
              <i class="ri-add-circle-fill"></i><span class="text-xs sm:text-sm">Add Graph</span>
            </button>
          </div>
        </div>

      </div>

      <!--content -->
      <div class="overflow-y-scroll scroll relative h-full">
        <div class="graph-modal graph-effect-1" id="modal">
          <div style="height: 400px;">
            <canvas id="successful-logout-chart"></canvas>
          </div>
        </div>
        <div class="graph-container">
          <div class="p-4 w-full">
            <div class="default-filter flex items-center gap-2 flex-wrap">
              <div class="activity-dropdown inline-block relative">
                <button
                  class="activity-btn rounded px-6 py-1 custom-outline custom-safety-btn">
                  <span>Activity Group</span>
                  <i class="ri-arrow-down-s-fill"></i>
                </button>
                <ul
                  class="activity-dropdown-menu activity-custom-dropdown-menu absolute hidden text-c-black shadow bg-custom-pure-white text-xs">
                  <li id="user" class="activity-item user-activity">
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#">User Activity</a>
                  </li>
                  <li id="group" class="dropdown-submenu group activity-item group-activity">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Group Activity
                    </a>
                  </li>
                  <li id="rbac" class="dropdown-submenu group activity-item rbac-activity">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      RBAC
                    </a>
                  </li>
                  <li id="filemanagement" class="dropdown-submenu group activity-item filemanagement-activity">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      File management
                    </a>
                  </li>

                </ul>
              </div>
              <div class="graph-dropdown inline-block relative">
                <button
                  class="select-graph rounded px-6 py-1 custom-outline custom-safety-btn user">
                  <span>Activity Type</span>
                  <i class="ri-arrow-down-s-fill"></i>
                </button>
                <ul
                  class="user-activity-menu graph-dropdown-menu graph-custom-dropdown-menu absolute hidden text-c-black shadow bg-custom-pure-white text-xs">
                  <!-- <li id="user-login-over-time">
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#">User Logins Over Time</a>
                  </li> -->
                  <!-- <li id="failed-login-attempt" class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Failed Login Attempts
                    </a>
                  </li> -->
                  <li id="successful-logout" class="dropdown-submenu group">
                    <a class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center" href="#" id="showLogoutChart" data-value="Log Out">
                      Successful Logouts
                    </a>
                  </li>
                  <!--  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Average Session Duration per User
                    </a>
                  </li> -->
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center" data-value="Active Users" id="showActiveUsersChart"
                      href="#">
                      Most Active Users
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Inactive Users" id="showInactiveUsersChart">
                      Inactive Users
                    </a>
                  </li>
                  <!--  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center" 
                      href="#">
                      File Accessed by Users
                    </a>
                  </li> -->
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Download" id="showDownloadChart">
                      Files Downloaded by Users
                    </a>
                  </li>
                  <li id="files-uploaded-by-users" class="dropdown-submenu group">
                    <a class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center" href="#" id="showUploadChart" data-value="File Upload">
                      Files Uploaded by Users
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="rounded-b custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" id="showFileTypeAccessChart" data-value="File Types Accessed">
                      File Types Accessed
                    </a>
                  </li>
                </ul>

                <ul
                  class="group-activity-menu graph-dropdown-menu graph-custom-dropdown-menu absolute hidden text-c-black shadow bg-custom-pure-white text-xs">
                  <li>
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#" data-value="Groups date" id="showGroupDateChart">Groups Created Over Time</a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Groups Active" id="showActiveGroupChart">
                      Most Active Group
                    </a>
                  </li>
                  <!--  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Group File Access Patterns
                    </a>
                  </li> -->
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Groups List" id="showUsersGroupChart">
                      Number of Users in Groups
                    </a>
                  </li>
                  <!-- <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Group Participation by Location
                    </a>
                  </li> -->
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Groups Share" id="showGroupShareChart">
                      File Sharing Between Groups
                    </a>
                  </li>

                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Groups Size" id="showGroupSizeChart">
                      Size Use Graph
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="rounded-b custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Groups Max" id="showGroupSizeMaxChart">
                      Group Max Size
                    </a>
                  </li>
                </ul>

                <ul
                  class="rbac-activity-menu graph-dropdown-menu graph-custom-dropdown-menu absolute hidden text-c-black shadow bg-custom-pure-white text-xs">
                  <li>
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#" data-value="Roles Upload" id="showRolesUploadChart">Roles Upload Limit</a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="User Roles" id="showUserRolesChart">
                      Number of Users in Roles
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Roles Active" id="showActiveRolesChart">
                      Most Active Roles
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Role Usage by Time" id="showRoleUsageByTimeChart">
                      Role Usage by Time
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Users Assigned to Roles" id="showUserAssignToRoleChart">
                      Users Assigned to Roles
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Files Accessed by Role" id="showFileAccessByRoleChart">
                      Files Accessed by Role
                    </a>
                  </li>
                </ul>

                <ul
                  class="filemanagement-activity-menu graph-dropdown-menu graph-custom-dropdown-menu absolute hidden text-c-black shadow bg-custom-pure-white text-xs">
                  <li>
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#" data-value="Edit Files" id="showEditFilesChart">Files Edit By User</a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Upload Files" id="showUploadFilesChart">
                      Upload Files By User
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Average File Access Time" id="showAvgFileAccessTimeChart">
                      Average File Access Time
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="File Modifications" id="showFileModiChart">
                      File Modifications
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Delete Files" id="showDeleteFilesChart">
                      Deleted Files By Users
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Total Files" id="showTotalFilesChart">
                      Total files in Extension
                    </a>
                  </li>
                  <li class="dropdown-submenu group">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#" data-value="Unauthorized File Access" id="showUnauthorizedFileAccessChart">
                      Unauthorized File Access
                    </a>
                  </li>

                </ul>
              </div>

              <div class="data-label-dropdown inline-block relative ">
                <select id="graph_type" name="graph_type" class="label-custom-dropdown-menu px-6 py-1 custom-outline custom-safety-btn">
                  <option value="">Graph Type</option>
                  <option value="1">Bar</option>
                  <option value="2">Line</option>
                  <option value="3">Pie</option>
                </select>
              </div>

              <!-- <div class="data-label-dropdown inline-block relative ">
                <button
                  class="label-btn rounded px-6 py-1 custom-outline custom-safety-btn">
                  <span>Data Label</span>
                  <i class="ri-arrow-down-s-fill"></i>
                </button>
                <ul
                  class="label-dropdown-menu label-custom-dropdown-menu w-full absolute hidden text-c-black shadow bg-c-lighten-gray rounded overflow-hidden text-xs">
                  <li id="on-label" class="activity-item">
                    <a
                      class="custom-bg-hover rounded-t py-2 px-4 block whitespace-no-wrap px-4"
                      href="#">On</a>
                  </li>
                  <li id="off-label" class="dropdown-submenu group activity-item">
                    <a
                      class="custom-bg-hover py-2 px-4 block whitespace-no-wrap flex justify-between items-center"
                      href="#">
                      Off
                    </a>
                  </li>
                </ul>
              </div> -->

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
                <!-- <input id="start-date" class="outline-none bg-gray-100 w-24 py-1 pl-2" datepicker datepicker-autohide type="text" placeholder="Select date start" readonly>
                <input id="start-time" type="time" class="outline-none bg-gray-100" min="09:00" max="18:00" value="00:00" required /> -->
              </div>
              <div class="date-select custom-safety-btn rounded px-6 mr-1 hover:border-yellow-300 hidden">
                <input type="datetime-local" id="end-date" name="end-date" class="outline-none bg-gray-100 w-44 py-1 pl-2">
                <!-- <input id="end-date" class="outline-none bg-gray-100 w-24 py-1 pl-2" datepicker datepicker-autohide type="text" placeholder="Select date end" readonly>
                <input id="end-time" type="time" class="outline-none bg-gray-100" min="09:00" max="18:00" value="00:00" required /> -->
              </div>
            </div>

            <div class="graph-area rounded mt-6 relative">
              <div class="graph-hidden-area">
                <div class="pr-3 pt-4 flex gap-3 justify-end">
                  <i class="ri-eye-off-fill ri-lg" id="md-trigger"></i>
                  <i class="ri-eye-fill ri-lg hidden" id="md-close"></i>
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
          </div>
        </div>
        <div class="md-overlay">
          <button class="nav-btn left" id="prev-slide"><i class="ri-arrow-left-wide-line"></i></button>
          <button class="nav-btn right" id="next-slide"><i class="ri-arrow-right-wide-line"></i></button>
          <div class="nav-track" id="nav-track">
            <span id="slide-indicator"></span>
          </div>
        </div>
      </div>


</main>
<script src="{{ asset($constants['JSFILEPATH'] . 'graph-setup.js') }}"></script>
<script src="{{ asset($constants['JSFILEPATH'] . 'reports-analytics.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/helpers.esm.min.js">
</script>

<script>
  const analitics = @json(route('analitics'));

  var chrt = document.getElementById("chartId").getContext("2d");

  var chartId = new Chart(chrt, {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: "",
        data: [],
        backgroundColor: [],
        borderColor: [],
        borderWidth: 2
      }]
    },
    options: {
      responsive: false,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  var backgroundColors = ['yellow', 'aqua', 'pink', 'lightgreen', 'lightblue', 'gold'];
  var borderColors = ['red', 'blue', 'fuchsia', 'green', 'navy', 'black'];

  var selectedValue = '';
  var selectedDateRange = '';
  var startDateInput = endDateInput = '';

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("customize").addEventListener("click", function() {
      document.querySelectorAll(".date-select").forEach(function(element) {
        element.classList.toggle("hidden");
      });
    });
    document.getElementById("start-date").addEventListener("change", handleCustomDateSelection);
    document.getElementById("end-date").addEventListener("change", handleCustomDateSelection);
  });

  document.querySelectorAll('.timeFrame').forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      document.querySelectorAll('.timeFrame').forEach(function(btn) {
        btn.classList.remove('active', 'hover:border-yellow-300');
      });
      this.classList.add('active', 'hover:border-yellow-300');
      if (this.id !== 'customize') {
        var dateRange = this.innerText.trim().toLowerCase().replace(' ', '');
        selectedDateRange = dateRange;
        fetchData(selectedValue, selectedDateRange);
      }
    });
  });


  function handleCustomDateSelection() {
    var startDateInput = document.getElementById("start-date").value;
    var endDateInput = document.getElementById("end-date").value;
    // alert(startDateInput);
  }


  function handleCustomDateSelectionOLDD() {
    // Get the start and end date input fields
    var startDateInput = document.getElementById("start-date");
    // var startTimeInput = document.getElementById("start-time");
    var endDateInput = document.getElementById("end-date");
    // var endTimeInput = document.getElementById("end-time");
    // alert(startDateInput);
    fetchData(startDateInput, endDateInput);

    // if (startDateInput && startTimeInput && endDateInput && endTimeInput) {
    //   console.log("All fields are selected");

    //   // Call the fetchData or any other function after validation
    //   fetchData(startDateInput, startTimeInput, endDateInput, endTimeInput);  // Pass data to your fetchData function
    // } else {
    //   console.log("Start date/time or end date/time is missing");

    //   // Show error or prompt user to select all fields
    //   alert("Please select both start date, start time, end date, and end time.");
    // }

    // Enable end date input when start date is selected
    // startDateInput.addEventListener("change", function() {
    //   if (startDateInput.value) {
    //     endDateInput.removeAttribute("disabled"); // Enable end date input
    //   } else {
    //     endDateInput.setAttribute("disabled", true); // Disable end date input if start date is cleared
    //   }
    // });

    // // Automatically send the data when both dates are selected
    // endDateInput.addEventListener("change", function() {
    //   // Check if both start date and end date are selected
    //   if (startDateInput.value && endDateInput.value) {
    //     // Ensure time is added to the date values
    //     const startDateTime = new Date(`${startDateInput.value}T${startTimeInput.value}`);
    //     const endDateTime = new Date(`${endDateInput.value}T${endTimeInput.value}`);

    //     // Validate that end date/time is after start date/time
    //     if (startDateTime >= endDateTime) {
    //       alert("End date and time must be after the start date and time.");
    //       return; // Exit the function if the dates are not valid
    //     }

    //     // Create the custom date range (format: "custom:start-date start-time,end-date end-time")
    //     const customDateRange = `custom:${startDateInput.value} ${startTimeInput.value},${endDateInput.value} ${endTimeInput.value}`;
    //     selectedDateRange = customDateRange;

    //     // Call your method to send the data (e.g., fetchData)
    //     fetchData(selectedValue, selectedDateRange);

    //     // Hide the date selection inputs after data is sent
    //     document.querySelectorAll(".date-select").forEach(function(element) {
    //       element.classList.add("hidden");
    //     });
    //   }
    // });
  }


  document.getElementById('showLogoutChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showUploadChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showDownloadChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showActiveUsersChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showInactiveUsersChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showUsersGroupChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showGroupDateChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showGroupShareChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showActiveGroupChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showGroupSizeChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showGroupSizeMaxChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showUserRolesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showRolesUploadChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showActiveRolesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showEditFilesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showUploadFilesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showDeleteFilesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  document.getElementById('showTotalFilesChart').addEventListener('click', function(event) {
    event.preventDefault();
    selectedDateRange = '';
    document.querySelectorAll('.timeFrame').forEach(function(btn) {
      btn.classList.remove('active', 'hover:border-yellow-300');
    });
    var value = this.getAttribute('data-value');
    selectedValue = value;
    fetchData(selectedValue, selectedDateRange);
  });

  // Function to fetch data based on value or dateRange
  function fetchData(value, dateRange, startDateInput, endDateInput) {
    console.log("Fetching data with:", startDateInput, endDateInput);
    $.ajax({
      url: analitics,
      method: 'GET',
      data: {
        value: value,
        dateRange: dateRange,
        startDateInput: startDateInput,
        endDateInput: endDateInput
      },
      success: function(response) {
        var getLabels = response.getLabels;
        var getData = response.getData;
        var type = response.type;
        if (chartId.config.type !== type) {
          chartId.destroy();
          chartId = new Chart(chrt, {
            type: type,
            data: {
              labels: getLabels,
              datasets: [{
                label: value + " Data",
                data: getData,
                backgroundColor: getData.map((_, index) => backgroundColors[index % backgroundColors.length]),
                borderColor: getData.map((_, index) => borderColors[index % borderColors.length]),
                borderWidth: 2
              }]
            },
            options: {
              responsive: false,
              plugins: {
                legend: {
                  display: false
                }
              }
            }
          });
        } else {
          chartId.data.labels = getLabels;
          chartId.data.datasets[0].data = getData;
          chartId.data.datasets[0].backgroundColor = getData.map((_, index) => backgroundColors[index % backgroundColors.length]);
          chartId.data.datasets[0].borderColor = getData.map((_, index) => borderColors[index % borderColors.length]);
          chartId.update();
        }
        if (value) {
          chartId.data.datasets[0].label = value + " Data";
        } else if (dateRange) {
          chartId.data.datasets[0].label = dateRange.replace(/([a-z])([A-Z])/g, '$1 $2') + " Data";
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
      }
    });
  }
</script>



<script defer>
  document.getElementById("md-trigger").addEventListener("click", function(e) {
    document.getElementById("modal").classList.toggle("graph-show");
    document.getElementById('md-close').classList.remove('hidden')
    document.getElementById('md-trigger').classList.add('hidden')
    e.preventDefault();
  });

  document.getElementById('md-close').addEventListener("click", function(e) {
    document.getElementById("modal").classList.toggle("graph-show");
    document.getElementById('md-close').classList.add('hidden')
    document.getElementById('md-trigger').classList.remove('hidden')
    e.preventDefault();
  })



  let currentSlide = 1;
  const totalSlides = 10;

  // Function to update the slide indicator and navigation logic
  function updateSlideIndicator(slide) {
    document.getElementById('slide-indicator').textContent = `${slide}/${totalSlides}`;
  }

  updateSlideIndicator(currentSlide);

  document.getElementById('prev-slide').addEventListener('click', function(e) {
    e.stopPropagation();
    if (currentSlide > 1) {
      currentSlide--;
      updateSlideIndicator(currentSlide);
    }
  });

  document.getElementById('next-slide').addEventListener('click', function(e) {
    e.stopPropagation();
    if (currentSlide < totalSlides) {
      currentSlide++;
      updateSlideIndicator(currentSlide);
    }
  });

  // Event listener to close modal when clicking outside
  document.querySelector('.md-overlay').addEventListener('click', function(e) {
    document.getElementById('modal').classList.remove('graph-show');
    document.getElementById('md-trigger').classList.remove('hidden')
    document.getElementById('md-close').classList.add('hidden')
  });

  document.getElementById('modal').addEventListener('click', function(e) {
    e.stopPropagation();
  });
</script>
@endsection