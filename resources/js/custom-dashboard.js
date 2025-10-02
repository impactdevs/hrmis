import ApexTree from 'apextree'

window.ApexTree = ApexTree;

document.addEventListener("DOMContentLoaded", () => {
    const {
        leaveTypes,
        allocatedDays,
        employeeData,
        showConsentModal,
        todayCounts,
        yesterdayCounts,
        lateCounts,
        hours,
        csrf,
        isAdminOrSecretary
    } = window.APP || {};

    console.log("isAdminOrSecretary:", isAdminOrSecretary);

    // Consent modal
    if (showConsentModal) {
        $("#consent").modal({ backdrop: "static", keyboard: false });
        $("#consent").modal("show");
        $("#applyButton").on("click", () => $("#consentForm").submit());
    }

    // Fallback vanilla JS for apply button
    const applyBtn = document.getElementById("applyButton");
    if (applyBtn) {
        applyBtn.addEventListener("click", () => {
            document.getElementById("consentForm")?.submit();
        });
    }

    // Radar chart
    const budgetEl = document.querySelector("#budgetChart");
    if (budgetEl && window.echarts) {
        const budgetChart = echarts.init(budgetEl);
        budgetChart.setOption({
            legend: { data: ["Allocated Leave Days"] },
            radar: { indicator: leaveTypes.map(type => ({ name: type, max: 30 })) },
            series: [
                { name: "Allocated Leave Days", type: "radar", data: [{ value: allocatedDays, name: "Allocated Leave Days" }] }
            ]
        });
    }

    // Traffic chart
    if (isAdminOrSecretary && window.echarts) {
        const trafficEl = document.querySelector("#trafficChart");
        if (trafficEl) {
            echarts.init(trafficEl).setOption({
                tooltip: { trigger: "item" },
                series: [{
                    name: "Employees by Department",
                    type: "pie",
                    radius: ["40%", "70%"],
                    avoidLabelOverlap: false,
                    label: { show: false },
                    data: employeeData
                }]
            });
        }
    }

    // Attendance chart
    if (isAdminOrSecretary && window.ApexCharts) {
        const reportsEl = document.querySelector("#reportsChart");
        if (reportsEl) {
            new ApexCharts(reportsEl, {
                series: [
                    { name: "Today", data: todayCounts },
                    { name: "Yesterday", data: yesterdayCounts },
                    { name: "Late Arrivals", data: lateCounts }
                ],
                chart: { height: 350, type: "area", toolbar: { show: false } },
                markers: { size: 4 },
                colors: ["#4154f1", "#ff771d", "#ffbc00"],
                fill: {
                    type: "gradient",
                    gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.4, stops: [0, 90, 100] }
                },
                dataLabels: { enabled: false },
                stroke: { curve: "smooth", width: 2 },
                xaxis: { type: "datetime", categories: hours },
                tooltip: { x: { format: "dd/MM/yy HH:mm" } }
            }).render();
        }
    }

    // Withdraw modal
    const withdrawModal = document.getElementById("withdrawConfirmModal");
    const withdrawForm = document.getElementById("withdrawForm");
    if (withdrawModal && withdrawForm) {
        withdrawModal.addEventListener("show.bs.modal", (event) => {
            const button = event.relatedTarget;
            const id = button?.getAttribute("data-appraisal-id") || "";
            withdrawForm.action = `/appraisals/${id}/withdraw`;
        });

        withdrawForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById("confirmWithdrawBtn");
            if (!submitBtn) return;

            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Withdrawing...';

            fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrf,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast("success", data.message || "Appraisal withdrawn successfully");
                    bootstrap.Modal.getInstance(withdrawModal).hide();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || "Failed to withdraw appraisal");
                }
            })
            .catch(err => {
                showToast("error", err.message || "Unknown error");
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    function showToast(type, message) {
        const toast = document.createElement("div");
        toast.className = `toast align-items-center text-white bg-${type === "success" ? "success" : "danger"} border-0`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === "success" ? "bi-check-circle" : "bi-exclamation-circle"} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener("hidden.bs.toast", () => document.body.removeChild(toast));
    }
});
