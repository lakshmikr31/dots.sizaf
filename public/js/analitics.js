


//code for analitics graph filter start -------------------------------------------------
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
        alert(startDateInput);
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
// code for analitics graph filter end --------------------------------------------------


// code for analitics graph filter js click start ---------------------------------------

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
// code for analitics graph filter js click end -----------------------------------------