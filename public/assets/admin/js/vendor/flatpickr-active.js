(function () {
    'use strict'

    /* targetDateTime Picker */
    flatpickr("#targetDateTime", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    /* targetDateTimeTwo Picker */
    flatpickr("#targetDateTimeTwo", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    /* targetDate Picker */
    flatpickr("#targetDate", {
        enableTime: false,
        dateFormat: "F Y",
    });
    /* targetDate Picker */
    flatpickr("#targetDateTwo", {
        enableTime: false,
        dateFormat: "F Y",
    });
    /* startDate Picker */
    flatpickr("#startDate", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    /* startDate Picker */
    flatpickr("#startDateTwo", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    /* endDate Picker */
    flatpickr("#endDate", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    /* endDate Picker */
    flatpickr("#endDateTwo", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    flatpickr("#holidayDate", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    flatpickr("#holidayDay", {
        dateFormat: "Y-m-d",
        onChange: function (selectedDates, dateStr, instance) {
            const dayName = selectedDates[0].toLocaleDateString('en-US', { weekday: 'long' });
            instance.input.value = dayName;
        }
    });
    flatpickr("#timesheetDate", {
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    flatpickr("#payDate", {
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    flatpickr("#scheduleDate", {
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    flatpickr("#scheduleDateTwo", {
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    flatpickr("#startTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K", // 12-hour format with AM/PM
    });
    flatpickr("#startTimeTwo", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K", // 12-hour format with AM/PM
    });
    flatpickr("#endTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K", // 12-hour format with AM/PM
    });
    flatpickr("#endTimeTwo", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K", // 12-hour format with AM/PM
    });
    /* For Date Range Picker */
    flatpickr("#dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        disableMobile: true
    });
    /* For Date Range Picker */
    flatpickr("#payPeriod", {
        mode: "range",
        dateFormat: "Y-m-d",
        disableMobile: true
    });

    /* invoiceDate Picker */
    flatpickr("#invoiceDate", {
        enableTime: false,
        dateFormat: "d M Y",
    });
    /* dueDate Picker */
    flatpickr("#dueDate", {
        enableTime: false,
        dateFormat: "d M Y",
    });



    $("#basicInput").flatpickr({
        dateFormat: "Y-m-d",
    });
    $("#dateTime").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    $("#humanFriendlyDates").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    $("#humanFriendlyDate2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#startingDate").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#startingDate2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#startingDate3").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#completeDate").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#completeDate2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#completeDate3").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodTo").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodTo2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodTo3").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodFrom").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodFrom2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#periodFrom3").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#issueDate").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#expiryDate").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#purchaseDate").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#purchaseDate2").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });
    $("#loanDate").flatpickr({
        altInput: true,
        altFormat: "F, Y",
        dateFormat: "Y-m",
    });
    $("#loanDate").flatpickr({
        altInput: true,
        altFormat: "F, Y",
        dateFormat: "Y-m",
    });
    $("#dateDuration").flatpickr({
        mode: "range",
        altInput: true,
        dateFormat: "m-Y",
    });
    $("#minimumDates").flatpickr({
        minDate: "2022-01"
    });
    $("#maximumDates").flatpickr({
        dateFormat: "d.m.Y",
        maxDate: "21.02.2024"
    });
    $("#miniMaxDates").flatpickr({
        minDate: "today",
        maxDate: new Date().fp_incr(14)
    });
    $("#disablingSpecificDates").flatpickr({
        disable: ["2024-01-14", "2023-12-15", "2025-03-08", new Date(2025, 4, 9)],
        dateFormat: "Y-m-d",
    });
    $("#selectingMultipleDates").flatpickr({
        mode: "multiple",
        dateFormat: "Y-m-d"
    });
    $("#preloadingMultipleDates").flatpickr({
        mode: "multiple",
        dateFormat: "Y-m-d",
        defaultDate: ["2023-12-16", "2023-12-17"]
    });
    $("#rangeCalendar").flatpickr({
        mode: "range"
    });
    $("#preloadingRangeCalendar").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: ["2023-12-11", "2023-12-16"]
    });
    $("#timePicker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
    });
    $("#timePicker24").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
    $("#preloadingTimePicker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        defaultDate: "01:45"
    });
    $("#meetingTime").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
    });
    $("#meetingTime2").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
    });
    $("#displayWeekNumbers").flatpickr({
        wrap: true,
        weekNumbers: true,
    });
    $("#functionDate").flatpickr({
        enable: [
            function (date) {
                // return true to enable
                return (date.getMonth() % 2 === 0 && date.getDate() < 15);
            }
        ]
    });
    $("#functionDisablingDate").flatpickr({
        "disable": [
            function (date) {
                // return true to disable
                return (date.getDay() === 0 || date.getDay() === 6);

            }
        ],
        "locale": {
            "firstDayOfWeek": 1 // start week on Monday
        }
    });
    $("#inlineCalendar").flatpickr({
        inline: true
    });

})();