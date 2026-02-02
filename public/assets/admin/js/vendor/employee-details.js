document.addEventListener("DOMContentLoaded", function () {
    const accountNumberSpan = document.getElementById("accountNumber");

    if (accountNumberSpan) {
        const toggleBtn = document.getElementById("toggleAccount");
        const eyeIcon = document.getElementById("eyeIcon");

        const fullAccount = accountNumberSpan.getAttribute("data-full");
        const maskedAccount = `•••• •••• ${fullAccount.slice(-4)}`;
        let isMasked = true;

        toggleBtn.addEventListener("click", () => {
            if (isMasked) {
                accountNumberSpan.textContent = fullAccount;
                eyeIcon.classList.replace("ri-eye-line", "ri-eye-off-line");
            } else {
                accountNumberSpan.textContent = maskedAccount;
                eyeIcon.classList.replace("ri-eye-off-line", "ri-eye-line");
            }
            isMasked = !isMasked;
        });
    }
});

// Copy to clipboard function
document.addEventListener('DOMContentLoaded', function () {
    const copyBtn = document.getElementById('copyBankDetails');

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const bankDetails = Array.from(document.querySelectorAll('.bank-info li'))
                .map(li => {
                    const label = li.querySelector('.info-label span')?.textContent || '';
                    const value = li.querySelector('.info-value')?.textContent.replace(/\•/g, '').trim() || '';
                    return `${label} ${value}`;
                })
                .join('\n');

            navigator.clipboard.writeText(bankDetails).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="ri-check-line me-1"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            });
        });
    }
});

// Handle all interactive elements through document click
document.addEventListener('click', function (e) {
    // Password toggle
    if (e.target.closest('.toggle-password')) {
        const passwordDisplay = document.querySelector('.password-display');
        const icon = e.target.closest('.toggle-password').querySelector('i');

        if (passwordDisplay.textContent.includes('•')) {
            passwordDisplay.textContent = 's3cur3p@ss';
            icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
        } else {
            passwordDisplay.textContent = '••••••••';
            icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
        }
    }

    // Email copy
    if (e.target.closest('.icon-btn .ri-file-copy-line')) {
        const email = 'ethan.mitchell@example.com';
        navigator.clipboard.writeText(email);

        const btn = e.target.closest('.icon-btn');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="ri-check-line"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    }
});

var options = {
    series: [
        {
            name: "Productivity",
            data: [85, 92, 78, 88, 94, 82, 75]
        },
        {
            name: "Quality",
            data: [92, 90, 95, 88, 82, 90, 93]
        },
        {
            name: "Attendance",
            data: [100, 100, 80, 100, 100, 100, 0] // 0 for weekend
        }
    ],
    chart: {
        type: 'radar',
        height: 391,
        toolbar: { show: false },
        dropShadow: {
            enabled: true,
            blur: 2,
            opacity: 0.2
        }
    },
    colors: ['#4F46E5', '#29DA82', '#FF830F'],
    dataLabels: {
        enabled: true,
        background: {
            enabled: true,
            borderRadius: 5,
            padding: 4
        }
    },
    plotOptions: {
        radar: {
            size: 130,
            polygons: {
                strokeColors: '#e5e7eb',
                connectorColors: '#e5e7eb'
            }
        }
    },
    markers: {
        size: 6,
        hover: { size: 8 }
    },
    xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        labels: {
            style: { fontSize: '12px' }
        }
    },
    yaxis: {
        min: 0,
        max: 100,
        tickAmount: 5,
        labels: {
            formatter: function (val) { return val + '%'; }
        }
    },
    tooltip: {
        y: { formatter: function (val) { return val + '%'; } }
    },
    legend: {
        position: 'bottom',
        markers: { radius: 4 }
    },
    responsive: [{
        breakpoint: 768,
        options: { chart: { height: 400 } }
    }]
};
var chart = new ApexCharts(document.querySelector("#performance-chart"), options);
chart.render();