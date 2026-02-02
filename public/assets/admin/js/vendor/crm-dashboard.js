document.addEventListener("DOMContentLoaded", function () {
    var options = {
        series: [{
            name: 'Completed Orders',
            data: [120, 165, 210, 190, 230, 280, 320]
        }, {
            name: 'Pending Orders',
            data: [80, 95, 110, 70, 85, 65, 45]
        }, {
            name: 'Cancelled Orders',
            data: [25, 30, 15, 20, 25, 10, 5]
        }],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            }
        },
        colors: ['var(--color-success)', 'var(--color-warning)', 'var(--color-danger)'],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: '55%',
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
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisBorder: {
                show: true,
            },
            axisTicks: {
                show: false
            },
            labels: {
                show: true,
                style: {
                    colors: 'var(--color-body)',
                    fontSize: '12px',
                    fontFamily: 'var(--ff-body)',
                    fontWeight: 400,
                    cssClass: 'apexcharts-xaxis-label',
                },
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: 'var(--color-body)',
                    fontSize: '12px',
                    fontFamily: 'var(--ff-body)',
                    fontWeight: 400,
                    cssClass: 'apexcharts-yaxis-label',
                },
            },
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " orders"
                }
            }
        },
        legend: {
            position: 'top',
            fontSize: '14px',
            markers: {
                radius: 12
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            },
            labels: {
                colors: "var(--color-body)",
            },
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        responsive: [{
            breakpoint: 768,
            options: {
                plotOptions: {
                    bar: {
                        columnWidth: '65%'
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#orderAnalyticsChart"), options);
    chart.render();
});


/* Revenue Analytics */
var options = {
    series: [
        {
            name: "Revenue",
            type: "column",
            data: [18, 23, 28, 36, 44, 52, 61, 71, 76, 88, 91, 100],
        },
        {
            name: "Profit",
            type: "area",
            data: [34, 38, 46, 55, 59, 68, 73, 85, 92, 105, 125, 135],
        }
    ],
    chart: {
        toolbar: {
            show: false,
        },
        height: 350,
        stacked: false,
        dropShadow: {
            enabled: true,
            enabledOnSeries: undefined,
            top: 7,
            left: 1,
            blur: 3,
            color: ["transparent", "#000"],
            opacity: 0.2
        },
    },
    stroke: {
        width: [1.5, 1.5],
        curve: "smooth",
    },
    plotOptions: {
        bar: {
            columnWidth: "20%",
            borderRadius: 3,
        },
    },
    colors: [
        "var(--color-primary)",
        "var(--color-success)"
    ],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [0, 90, 100],
            colorStops: [
                [
                    {
                        offset: 0,
                        color: "var(--color-primary)",
                        opacity: 1
                    },
                    {
                        offset: 75,
                        color: "var(--color-primary)",
                        opacity: 1
                    },
                    {
                        offset: 100,
                        color: "var(--color-primary)",
                        opacity: 1
                    }
                ],
                [
                    {
                        offset: 0,
                        color: "rgba(var(--success-rgb),0.15)",
                        opacity: 1
                    },
                    {
                        offset: 75,
                        color: "rgba(var(--success-rgb),0.15)",
                        opacity: 1
                    },
                    {
                        offset: 100,
                        color: "rgba(var(--success-rgb),0.15)",
                        opacity: 1
                    }
                ],
            ]
        }
    },
    labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ],
    markers: {
        size: 0,
    },
    xaxis: {
        type: "month",
        labels: {
            style: {
                colors: 'var(--color-body)',
                fontSize: '12px',
                fontFamily: 'var(--ff-body)',
                fontWeight: 400,
                cssClass: 'apexcharts-xaxis-label',
            },
        },
    },
    yaxis: {
        min: 0,
        labels: {
            style: {
                colors: 'var(--color-body)',
                fontSize: '12px',
                fontFamily: 'var(--ff-body)',
                fontWeight: 400,
                cssClass: 'apexcharts-yaxis-label',
            },
        },
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0) + " points";
                }
                return y;
            },
        },
    },
    legend: {
        position: 'top',
        fontSize: '14px',
        markers: {
            radius: 12
        },
        itemMargin: {
            horizontal: 10,
            vertical: 5
        },
        labels: {
            colors: "var(--color-body)",
        },
    },
};
var chart = new ApexCharts(document.querySelector("#revenueProfitChart"), options);
chart.render();
/* Revenue Analytics */

/* Contacts By Source */
var options = {
    series: [25, 20, 15, 40],
    chart: {
        type: 'polarArea',
        height: 357
    },
    stroke: {
        colors: ['var(--color-white)'],
    },
    fill: {
        opacity: 1
    },
    legend: {
        position: 'bottom',
        itemMargin: {
            horizontal: 5,
            vertical: 5
        },
        fontFamily: 'Nunito Sans, sans-serif',
        labels: {
            colors: "var(--color-body)",
        },
    },
    labels: ['Prospects', 'Qualified', 'Negotiation', 'Won'],
    colors: ["var(--color-primary)", "var(--color-info)", "var(--color-warning)", "var(--color-success)"],
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};
var chart = new ApexCharts(document.querySelector("#contacts-source"), options);
chart.render();
/* Contacts By Source */


/* Visitors By Country Map */
var markers = [
    {
        name: "USA",
        coords: [37.0902, -95.7129],
    },
    {
        name: "Palestine",
        coords: [31.5, 34.4667],
    },
    {
        name: "Ireland",
        coords: [53.1424, -7.6921],
    },
    {
        name: "Brazil",
        coords: [-14.2350, -51.9253],
    },
];
var map = new jsVectorMap({
    map: "world_merc",
    selector: "#salesWorldMap",
    markersSelectable: true,
    zoomOnScroll: false,
    zoomButtons: false,

    onMarkerSelected(index, isSelected, selectedMarkers) {
        console.log(index, isSelected, selectedMarkers);
    },

    labels: {
        markers: {
            render: function (marker) {
                return marker.name;
            },
        },
    },

    markers: markers,
    markerStyle: {
        hover: {
            stroke: "#DDD",
            strokeWidth: 3,
            fill: "#FFF",
        },
        selected: {
            fill: "#ff525d",
        },
    },
    regionStyle: {
        initial: {
            stroke: "var(--primary01)",
            strokeWidth: 1.5,
        }
    },
    markerLabelStyle: {
        initial: {
            fontFamily: 'Nunito Sans, sans-serif',
            fontSize: 13,
            fontWeight: 500,
            fill: "#35373e",
        },
    },
});
/* Visitors By Country Map */

document.addEventListener("DOMContentLoaded", function () {
    var pieOptions = {
        series: [32, 24, 18, 14, 12],
        chart: {
            type: 'pie',
            height: 232,
        },
        colors: ['var(--color-primary)', 'var(--color-info)', 'var(--color-warning)', 'var(--color-success)', 'var(--color-danger)'],
        labels: ['Organic Search', 'Social Media', 'Email Campaigns', 'Referrals', 'Direct Traffic'],
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false,
            labels: {
                colors: "var(--color-body)",
            },
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 200
                }
            }
        }]
    };

    var pieChart = new ApexCharts(document.querySelector("#leadsPieChart"), pieOptions);
    pieChart.render();
});