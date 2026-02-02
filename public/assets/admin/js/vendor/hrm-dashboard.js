document.addEventListener("DOMContentLoaded", function () {
    var options = {
        series: [{
            name: 'Present',
            data: [28, 29, 30, 27, 26, 25, 24, 28, 30, 31, 29, 27]
        }, {
            name: 'Absent',
            data: [3, 2, 1, 4, 5, 6, 7, 3, 1, 0, 2, 4]
        }, {
            name: 'Late',
            data: [2, 1, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2]
        }],
        chart: {
            type: 'bar',
            height: 369,
            stacked: true,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#4CAF50', '#F44336', '#FFC107'],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " days"
                }
            }
        },
        legend: {
            position: 'top',
        }
    };

    var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
    chart.render();
});

document.addEventListener("DOMContentLoaded", function () {
    const options = {
        series: [{
            name: 'Employees',
            data: [125, 55, 45, 35, 30, 25]
        }],
        chart: {
            type: 'bar',
            height: 369,
            toolbar: { show: false },
            fontFamily: 'Nunito Sans, sans-serif',
            foreColor: '#6B7280'
        },
        plotOptions: {
            bar: {
                borderRadius: 0,
                horizontal: true,
                barHeight: '70%',
                distributed: true,
                dataLabels: {
                    position: 'center'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => `${val}`,
            style: {
                fontSize: '12px',
                fontWeight: 600,
                colors: ['#fff']
            },
            offsetX: 10
        },
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
        xaxis: {
            categories: ['Active', 'On Leave', 'Probation', 'Contract', 'WFH', 'Terminated'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    fontSize: '13px',
                    fontWeight: 500
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontSize: '13px',
                    fontWeight: 500
                }
            }
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4,
            padding: {
                top: 0,
                right: 20,
                bottom: 0,
                left: 20
            }
        },
        tooltip: {
            style: {
                fontSize: '12px',
                fontFamily: 'Nunito Sans, sans-serif',
            },
            y: {
                formatter: (val) => `${val} employees`
            }
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: { height: 280 },
                plotOptions: {
                    bar: { barHeight: '60%' }
                },
                dataLabels: { enabled: false }
            }
        }]
    };

    const chart = new ApexCharts(document.querySelector("#employeeStatusChart"), options);
    chart.render();
});

var options = {
    series: [46, 55, 67, 83],
    chart: {
        height: 338,
        type: 'radialBar',

    },
    plotOptions: {
        radialBar: {
            dataLabels: {
                name: {
                    fontSize: '22px',
                },
                value: {
                    fontSize: '16px',
                },
                total: {
                    show: true,
                    label: 'Total',
                    colors: '#000',
                    formatter: function (w) {
                        // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                        return 250
                    }
                }
            }
        },
    },
    labels: ['Ongoing', 'On Hold', 'Overdue', 'Delivery'],
    colors: ["var(--color-primary)", "var(--color-info)", "var(--color-warning)", "var(--color-success)"],
    legend: {
        show: true,
        position: 'bottom',
        fontFamily: 'Nunito Sans, sans-serif',
    },
    stroke: {
        colors: ['var(--color-black)'],
    },
    fill: {
        opacity: 1,
    },
    markers: {
        strokeWidth: 0,
    },
    responsive: [{
        breakpoint: 640,
        options: {
            chart: { height: 280 },
            plotOptions: {
                bar: { barHeight: '60%' }
            },
            dataLabels: { enabled: false }
        }
    }]
};
var chart = new ApexCharts(document.querySelector("#taskOverview"), options);
chart.render();

var swiper = new Swiper(".birthdaySlider", {
    spaceBetween: 25,
    autoplay: true,
    loop: true,
    pagination: {
        el: ".bd-pagination",
        clickable: true,
    },
});

$(document).ready(function () {
    $('#meetingTable').DataTable({
        "bFilter": true,
        ordering: true,
        lengthChange: true,
        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: [0]
            }
        ],
        pageLength: 5,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });
});

$(document).ready(function () {
    $('#employeeActivityTable').DataTable({
        "bFilter": true,
        ordering: true,
        lengthChange: true,
        columnDefs: [
            {
                orderable: false,
                searchable: false,
                targets: [0]
            }
        ],
        pageLength: 5,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });
});