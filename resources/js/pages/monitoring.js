document.addEventListener("DOMContentLoaded", () => {
    const dataBtn = document.getElementById("data-dropdown-btn");
    const dataMenu = document.getElementById("data-dropdown-menu");
    const dataLabel = document.getElementById("data-dropdown-label");
    const dataIcon = document.getElementById("data-dropdown-icon");
    const dataOptions = document.querySelectorAll(".data-option");
    const chartPlot = document.getElementById("chart-plot");
    const chartBars = document.getElementById("chart-bars");
    const chartXAxis = document.getElementById("chart-x-axis");
    const chartYAxis = document.getElementById("chart-y-axis");
    const chartSeriesLabel = document.getElementById("chart-series-label");
    const chartRangeLabel = document.getElementById("chart-range-label");
    const realDateFilterWrap = document.getElementById("real-date-filter-wrap");
    const realDateFilter = document.getElementById("real-date-filter");
    const predictionEmptyNote = document.getElementById("prediction-empty-note");
    const realEmptyNote = document.getElementById("real-empty-note");
    const comparisonEmptyNote = document.getElementById("comparison-empty-note");
    const chartTipLabel = document.getElementById("chart-tip-label");
    const advicePanel = document.getElementById("advice-panel");
    const adviceStatus = document.getElementById("advice-status");
    const adviceValue = document.getElementById("advice-value");
    const adviceText = document.getElementById("advice-text");
    const adviceEffect = document.getElementById("advice-effect");
    const adviceLeft = document.getElementById("advice-left");
    const adviceRight = document.getElementById("advice-right");
    const monitoringDataElement = document.getElementById("monitoring-data");
    const monitoringAdvicesElement = document.getElementById("monitoring-advices");
    const monitoringMetaElement = document.getElementById("monitoring-meta");

    let currentDataset = "real";
    let currentRealDate = null;

    const monitoringData = monitoringDataElement
        ? JSON.parse(monitoringDataElement.textContent || "{}")
        : {};
    const monitoringAdvices = monitoringAdvicesElement
        ? JSON.parse(monitoringAdvicesElement.textContent || "{}")
        : {};
    const monitoringMeta = monitoringMetaElement
        ? JSON.parse(monitoringMetaElement.textContent || "{}")
        : {};

    const colorByCategory = {
        Baik: "bg-[#48C774]",
        Sedang: "bg-[#5A67F2]",
        "Tidak Sehat": "bg-[#D8C800]",
        "Sangat Tidak Sehat": "bg-[#FF6B61]",
        Berbahaya: "bg-[#2B2B2B]",
    };

    const surfaceByCategory = {
        Baik: "from-[#F3FFF7] to-[#E8FAEF]",
        Sedang: "from-[#F5F6FF] to-[#ECEEFF]",
        "Tidak Sehat": "from-[#FFFEEB] to-[#FFF9D8]",
        "Sangat Tidak Sehat": "from-[#FFF3F2] to-[#FFE6E3]",
        Berbahaya: "from-[#F4F4F4] to-[#EAEAEA]",
    };

    const effectByCategory = {
        Baik: "Tingkat kualitas udara yang sangat baik, tidak memberikan efek negatif terhadap manusia, hewan, dan tumbuhan.",
        Sedang: "Kualitas udara masih dapat diterima, namun kelompok sensitif dapat mulai mengalami keluhan ringan.",
        "Tidak Sehat": "Kelompok sensitif berisiko mengalami gangguan pernapasan dan iritasi saat terpapar dalam durasi lama.",
        "Sangat Tidak Sehat": "Kualitas udara buruk dan berpotensi menimbulkan dampak kesehatan pada masyarakat umum.",
        Berbahaya: "Kualitas udara sangat berbahaya dan dapat memicu gangguan kesehatan serius jika terpapar terus-menerus.",
    };

    const defaultAdviceByCategory = {
        Baik: "Aktivitas luar ruangan dapat dilakukan seperti biasa.<br/>Tetap menjaga pola hidup sehat dan kebersihan lingkungan.<br/>Pantau informasi kualitas udara secara berkala.",
        Sedang: "Kurangi aktivitas berat di luar ruangan untuk kelompok sensitif.<br/>Gunakan masker saat beraktivitas di area padat kendaraan.<br/>Perbanyak konsumsi air putih.",
        "Tidak Sehat": "Batasi aktivitas luar ruangan terutama anak-anak, lansia, dan penderita asma.<br/>Gunakan masker saat keluar rumah.<br/>Tutup ventilasi saat kualitas udara menurun.",
        "Sangat Tidak Sehat": "Hindari aktivitas luar ruangan yang tidak mendesak.<br/>Gunakan masker dengan filtrasi yang baik.<br/>Utamakan berada di dalam ruangan dengan sirkulasi udara bersih.",
        Berbahaya: "Hentikan aktivitas luar ruangan sementara waktu.<br/>Gunakan perlindungan pernapasan maksimal jika harus keluar.<br/>Ikuti imbauan resmi dari otoritas setempat.",
    };

    const classify = (value) => {
        if (value <= 50) return "Baik";
        if (value <= 100) return "Sedang";
        if (value <= 200) return "Tidak Sehat";
        if (value <= 300) return "Sangat Tidak Sehat";
        return "Berbahaya";
    };

    const normalizeAdviceText = (text) =>
        String(text || "")
            .split(/\r?\n+/)
            .filter(Boolean)
            .join("<br/>");

    const createTooltip = () => {
        if (!chartPlot) return null;

        const tooltip = document.createElement("div");
        tooltip.id = "bar-tooltip";
        tooltip.className =
            "pointer-events-none absolute z-30 hidden -translate-x-1/2 rounded-xl border border-white/70 bg-white/95 px-3 py-1.5 text-xs font-semibold text-[#183427] shadow-[0_10px_30px_rgba(22,49,38,0.12)] backdrop-blur";

        chartPlot.appendChild(tooltip);

        return tooltip;
    };

    const tooltip = createTooltip();

    const showTooltip = (point, value, label) => {
        if (!tooltip) return;

        tooltip.innerHTML = `<div>${label}</div><div class="mt-0.5 text-[#183427]">${Number(value).toFixed(2)}</div>`;
        const left = point.offsetLeft + point.offsetWidth / 2;
        const top = Math.max(4, point.offsetTop - 44);

        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
        tooltip.classList.remove("hidden");
    };

    const hideTooltip = () => {
        if (!tooltip) return;

        tooltip.classList.add("hidden");
    };

    const buildScale = (values) => {
        if (!values.length) {
            return { min: 0, max: 10, ticks: Array.from({ length: 11 }, (_, index) => 10 - index) };
        }

        const minValue = Math.min(...values);
        const maxValue = Math.max(...values);
        const range = Math.max(maxValue - minValue, 1);
        const padding = Math.max(range * 0.15, 1);
        const min = Math.max(0, minValue - padding);
        const max = maxValue + padding;
        const step = (max - min) / 10;
        const ticks = Array.from({ length: 11 }, (_, index) => (max - step * index).toFixed(1));

        return { min, max, ticks };
    };

    const renderAxis = (ticks, labels) => {
        if (chartYAxis) {
            chartYAxis.innerHTML = ticks
                .map((tick) => `<span class="leading-none">${tick}</span>`)
                .join("");
        }

        if (chartXAxis) {
            const sampledLabels = labels.map((label, index) => ({ label, index }))
                .filter((_, index, array) => {
                    if (array.length <= 8) return true;
                    const step = Math.ceil(array.length / 6);
                    return index % step === 0 || index === array.length - 1;
                });

            chartXAxis.style.gridTemplateColumns = `repeat(${Math.max(sampledLabels.length, 1)}, minmax(0, 1fr))`;
            chartXAxis.innerHTML = sampledLabels
                .map((item) => `<span class="whitespace-nowrap">${item.label}</span>`)
                .join("");
        }
    };

    const renderAdvice = (value, clickedPoint) => {
        if (
            !advicePanel ||
            !adviceStatus ||
            !adviceValue ||
            !adviceText ||
            !adviceEffect ||
            !adviceLeft ||
            !chartBars
        ) {
            return;
        }

        const category = classify(value);

        chartBars.querySelectorAll(".chart-point").forEach((item) => {
            item.classList.remove("ring-4", "ring-[#183427]/12", "scale-110", "border-white");
            item.classList.add("opacity-80");
        });

        clickedPoint.classList.remove("opacity-80");
        clickedPoint.classList.add("ring-4", "ring-[#183427]/12", "scale-110", "border-white");

        advicePanel.classList.remove("hidden");
        adviceStatus.textContent = category;
        adviceValue.textContent = Number(value).toFixed(2);
        adviceEffect.textContent = effectByCategory[category];
        adviceText.innerHTML = normalizeAdviceText(
            monitoringAdvices[category] || defaultAdviceByCategory[category]
        );

        adviceLeft.classList.remove(
            "bg-[#48C774]",
            "bg-[#5A67F2]",
            "bg-[#D8C800]",
            "bg-[#FF6B61]",
            "bg-[#2B2B2B]"
        );

        adviceLeft.classList.add(colorByCategory[category]);
        adviceRight?.classList.remove(
            "bg-[#F6FBF7]",
            "bg-gradient-to-br",
            "from-[#F3FFF7]",
            "to-[#E8FAEF]",
            "from-[#F5F6FF]",
            "to-[#ECEEFF]",
            "from-[#FFFEEB]",
            "to-[#FFF9D8]",
            "from-[#FFF3F2]",
            "to-[#FFE6E3]",
            "from-[#F4F4F4]",
            "to-[#EAEAEA]"
        );
        adviceRight?.classList.add(
            "bg-gradient-to-br",
            ...surfaceByCategory[category].split(" ")
        );
    };

    const buildComparisonScale = (actualValues, predictedValues) => {
        const values = [...actualValues, ...predictedValues]
            .filter((value) => Number.isFinite(Number(value)))
            .map(Number);

        return buildScale(values);
    };

    const renderComparisonChart = (series) => {
        const labels = Array.isArray(series.labels) ? series.labels : [];
        const detailLabels = Array.isArray(series.detail_labels) ? series.detail_labels : labels;
        const actualValues = Array.isArray(series.actual_values) ? series.actual_values : [];
        const predictedValues = Array.isArray(series.predicted_values) ? series.predicted_values : [];

        if (chartSeriesLabel) {
            chartSeriesLabel.textContent = "Data Aktual vs Peramalan";
        }

        if (chartRangeLabel) {
            chartRangeLabel.textContent = series.range_label || "-";
        }

        if (chartTipLabel) {
            chartTipLabel.textContent = "Grafik perbandingan data aktual dan peramalan";
        }

        if (predictionEmptyNote) {
            predictionEmptyNote.classList.add("hidden");
        }

        if (realEmptyNote) {
            realEmptyNote.classList.add("hidden");
        }

        const hasData =
            labels.length > 0 &&
            actualValues.length > 0 &&
            predictedValues.length > 0;

        if (comparisonEmptyNote) {
            comparisonEmptyNote.classList.toggle("hidden", hasData);
        }

        if (!hasData) {
            if (advicePanel) {
                advicePanel.classList.add("hidden");
            }

            renderAxis([], []);
            chartBars.innerHTML =
                '<div class="flex h-full w-full items-center justify-center text-center text-sm font-medium text-neutral-500">Data perbandingan aktual dan peramalan belum tersedia.</div>';
            return;
        }

        const scale = buildComparisonScale(actualValues, predictedValues);
        renderAxis(scale.ticks, labels);

        chartBars.innerHTML = "";
        const width = Math.max(chartPlot?.clientWidth || 900, 900);
        const height = Math.max(chartPlot?.clientHeight || 256, 256);
        const padding = { top: 12, right: 16, bottom: 16, left: 8 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const xFor = (index) => padding.left + (index / Math.max(labels.length - 1, 1)) * chartWidth;
        const yFor = (value) => padding.top + chartHeight - ((value - scale.min) / Math.max(scale.max - scale.min, 1)) * chartHeight;

        const buildPoints = (values, kind) =>
            values.map((value, index) => ({
                kind,
                value,
                label: labels[index] ?? "-",
                detailLabel: detailLabels[index] ?? labels[index] ?? "-",
                x: xFor(index),
                y: Number.isFinite(Number(value)) ? yFor(Number(value)) : null,
            }));

        const actualPoints = buildPoints(actualValues, "actual");
        const predictedPoints = buildPoints(predictedValues, "predicted");
        const validActualPoints = actualPoints.filter((point) => point.y !== null);
        const validPredictedPoints = predictedPoints.filter((point) => point.y !== null);

        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute("viewBox", `0 0 ${width} ${height}`);
        svg.setAttribute("preserveAspectRatio", "none");
        svg.classList.add("absolute", "inset-0", "h-full", "w-full");

        const addPolyline = (points, stroke, dashArray = null) => {
            if (!points.length) return;

            const polyline = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
            polyline.setAttribute(
                "points",
                points.map((point) => `${point.x},${point.y}`).join(" ")
            );
            polyline.setAttribute("fill", "none");
            polyline.setAttribute("stroke", stroke);
            polyline.setAttribute("stroke-width", "3");
            polyline.setAttribute("stroke-linecap", "round");
            polyline.setAttribute("stroke-linejoin", "round");

            if (dashArray) {
                polyline.setAttribute("stroke-dasharray", dashArray);
            }

            svg.appendChild(polyline);
        };

        addPolyline(validActualPoints, "#0D2D1E");
        addPolyline(validPredictedPoints, "#6F916F", "8 6");
        chartBars.appendChild(svg);

        const legend = document.createElement("div");
        legend.className = "absolute left-2 top-2 z-10 flex flex-wrap items-center gap-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-semibold text-[#183427] shadow-[0_8px_20px_rgba(22,49,38,0.08)]";
        legend.innerHTML = `
            <span class="inline-flex items-center gap-2"><span class="h-[3px] w-6 rounded-full bg-[#0D2D1E]"></span>Aktual</span>
            <span class="inline-flex items-center gap-2"><span class="h-[3px] w-6 rounded-full border-t-[3px] border-dashed border-[#6F916F]"></span>Peramalan</span>
        `;
        chartBars.appendChild(legend);

        if (advicePanel) {
            advicePanel.classList.add("hidden");
        }
    };

    const renderChart = (datasetType) => {
        if (!chartBars || !chartXAxis || !chartYAxis) return;

        const sourceSeries = monitoringData[datasetType] || { values: [], labels: [] };
        if (datasetType === "perbandingan") {
            if (realDateFilterWrap) {
                realDateFilterWrap.classList.add("hidden");
                realDateFilterWrap.classList.remove("inline-flex");
            }

            renderComparisonChart(sourceSeries);
            return;
        }

        const series = datasetType === "real"
            ? (() => {
                const availableDates = Array.isArray(sourceSeries.available_dates) ? sourceSeries.available_dates : [];
                const seriesByDate = sourceSeries.series_by_date || {};
                const selectedDate = currentRealDate && seriesByDate[currentRealDate]
                    ? currentRealDate
                    : sourceSeries.selected_date || availableDates[availableDates.length - 1] || null;

                currentRealDate = selectedDate;

                if (realDateFilterWrap) {
                    realDateFilterWrap.classList.toggle("hidden", false);
                    realDateFilterWrap.classList.toggle("inline-flex", true);
                }

                if (realDateFilter) {
                    realDateFilter.min = sourceSeries.min_date || "";
                    realDateFilter.max = sourceSeries.max_date || "";
                    realDateFilter.value = selectedDate || "";
                }

                return selectedDate && seriesByDate[selectedDate]
                    ? seriesByDate[selectedDate]
                    : { values: [], labels: [], detail_labels: [], range_label: "-" };
            })()
            : (() => {
                if (realDateFilterWrap) {
                    realDateFilterWrap.classList.toggle("hidden", true);
                    realDateFilterWrap.classList.remove("inline-flex");
                }

                return sourceSeries;
            })();
        const values = Array.isArray(series.values) ? series.values : [];
        const labels = Array.isArray(series.labels) ? series.labels : [];
        const detailLabels = Array.isArray(series.detail_labels) ? series.detail_labels : labels;
        const scale = buildScale(values.filter((value) => Number.isFinite(Number(value))).map(Number));

        if (chartSeriesLabel) {
            chartSeriesLabel.textContent = datasetType === "prediksi" ? "Data Peramalan" : "Data Real";
        }

        if (chartRangeLabel) {
            chartRangeLabel.textContent = series.range_label || "-";
        }

        if (chartTipLabel) {
            chartTipLabel.textContent = "Klik titik untuk saran pencegahan";
        }

        if (predictionEmptyNote) {
            predictionEmptyNote.classList.toggle(
                "hidden",
                !(datasetType === "prediksi" && values.length === 0)
            );
        }

        if (realEmptyNote) {
            realEmptyNote.classList.toggle(
                "hidden",
                !(datasetType === "real" && values.length === 0 && !monitoringMeta.hasActiveDataset)
            );
        }

        if (comparisonEmptyNote) {
            comparisonEmptyNote.classList.add("hidden");
        }

        renderAxis(scale.ticks, labels);

        if (!values.length) {
            if (advicePanel) {
                advicePanel.classList.add("hidden");
            }

            chartBars.innerHTML =
                `<div class="flex h-full w-full items-center justify-center text-center text-sm font-medium text-neutral-500">${
                    datasetType === "prediksi"
                        ? !monitoringMeta.hasPublishedModel
                          ? "Data Peramalan belum dipilih untuk monitoring dari halaman admin."
                          : "Data Peramalan belum tersedia."
                        : !monitoringMeta.hasActiveDataset
                          ? "Data aktual belum tersedia."
                          : "Belum ada data untuk ditampilkan."
                }</div>`;
            return;
        }

        chartBars.innerHTML = "";
        const width = Math.max(chartPlot?.clientWidth || 900, 900);
        const height = Math.max(chartPlot?.clientHeight || 256, 256);
        const padding = { top: 12, right: 16, bottom: 16, left: 8 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const xFor = (index) => padding.left + (index / Math.max(values.length - 1, 1)) * chartWidth;
        const yFor = (value) => padding.top + chartHeight - ((value - scale.min) / Math.max(scale.max - scale.min, 1)) * chartHeight;

        const points = values.map((value, index) => ({
            value,
            label: labels[index] ?? "-",
            detailLabel: detailLabels[index] ?? labels[index] ?? "-",
            x: xFor(index),
            y: Number.isFinite(Number(value)) ? yFor(Number(value)) : null,
        }));
        const validPoints = points.filter((point) => point.y !== null);

        const polyline = validPoints.map((point) => `${point.x},${point.y}`).join(" ");
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute("viewBox", `0 0 ${width} ${height}`);
        svg.setAttribute("preserveAspectRatio", "none");
        svg.classList.add("absolute", "inset-0", "h-full", "w-full");

        if (validPoints.length > 0) {
            const area = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
            area.setAttribute("points", `${padding.left},${height - padding.bottom} ${polyline} ${padding.left + chartWidth},${height - padding.bottom}`);
            area.setAttribute("fill", "rgba(118, 162, 127, 0.16)");
            svg.appendChild(area);

            const line = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
            line.setAttribute("points", polyline);
            line.setAttribute("fill", "none");
            line.setAttribute("stroke", "#567A5E");
            line.setAttribute("stroke-width", "3");
            line.setAttribute("stroke-linecap", "round");
            line.setAttribute("stroke-linejoin", "round");
            svg.appendChild(line);
        }

        chartBars.appendChild(svg);

        validPoints.forEach((point) => {
            const pointButton = document.createElement("button");
            pointButton.type = "button";
            pointButton.className = "chart-point absolute h-4 w-4 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-[#567A5E] bg-white opacity-80 shadow-[0_8px_20px_rgba(86,122,94,0.16)] transition hover:scale-110 hover:border-[#355B3F] hover:bg-[#F6FBF7] hover:opacity-100";
            pointButton.style.left = `${(point.x / width) * 100}%`;
            pointButton.style.top = `${((point.y ?? 0) / height) * 100}%`;

            pointButton.addEventListener("mouseenter", () => showTooltip(pointButton, point.value, point.detailLabel));
            pointButton.addEventListener("mousemove", () => showTooltip(pointButton, point.value, point.detailLabel));
            pointButton.addEventListener("mouseleave", hideTooltip);
            pointButton.addEventListener("click", () => renderAdvice(point.value, pointButton));

            chartBars.appendChild(pointButton);
        });
    };

    const setActiveOption = (selectedValue) => {
        dataOptions.forEach((item) => {
            const isActive = item.dataset.value === selectedValue;

            item.classList.toggle("bg-[#EAF5ED]", isActive);
            item.classList.toggle("text-[#183427]", isActive);
            item.classList.toggle("text-[#4C6755]", !isActive);
        });
    };

    if (dataBtn && dataMenu && dataLabel && dataIcon) {
        dataBtn.addEventListener("click", () => {
            const willOpen = dataMenu.classList.contains("hidden");
            dataMenu.classList.toggle("hidden");
            dataBtn.setAttribute("aria-expanded", String(willOpen));
            dataIcon.classList.toggle("rotate-90", willOpen);
        });

        dataOptions.forEach((option) => {
            option.addEventListener("click", () => {
                currentDataset = option.dataset.value || "real";
                dataLabel.textContent = currentDataset === "prediksi"
                    ? "Data Peramalan"
                    : currentDataset === "perbandingan"
                        ? "Aktual vs Peramalan"
                        : "Data Real";
                setActiveOption(currentDataset);
                renderChart(currentDataset);

                dataMenu.classList.add("hidden");
                dataBtn.setAttribute("aria-expanded", "false");
                dataIcon.classList.remove("rotate-90");
            });
        });

        document.addEventListener("click", (event) => {
            if (!dataBtn.contains(event.target) && !dataMenu.contains(event.target)) {
                dataMenu.classList.add("hidden");
                dataBtn.setAttribute("aria-expanded", "false");
                dataIcon.classList.remove("rotate-90");
            }
        });
    }

    if (realDateFilter) {
        realDateFilter.addEventListener("change", () => {
            currentRealDate = realDateFilter.value || null;
            if (currentDataset === "real") {
                renderChart("real");
            }
        });
    }

    setActiveOption(currentDataset);
    renderChart(currentDataset);
});
