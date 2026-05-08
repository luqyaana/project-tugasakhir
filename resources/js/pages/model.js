document.addEventListener("DOMContentLoaded", () => {
    const processTabs = document.querySelectorAll(".process-tab");
    const tabPanels = document.querySelectorAll(".tab-panel");
    const openDatasetModalButton = document.getElementById("openDatasetModal");
    const closeDatasetModalButton = document.getElementById("closeDatasetModal");
    const datasetModal = document.getElementById("datasetModal");
    const datasetModalBackdrop = document.getElementById("datasetModalBackdrop");
    const preprocessingTab = document.querySelector('.process-tab[data-tab="preprocessing"]');
    const visualizationSource = document.getElementById("modelVisualizationData");
    const visualizationData = visualizationSource ? JSON.parse(visualizationSource.textContent || "{}") : null;

    const openDatasetModal = () => {
        if (!datasetModal) return;
        datasetModal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
    };

    const closeDatasetModal = () => {
        if (!datasetModal) return;
        datasetModal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    };

    const formatNumber = (value) => {
        if (value === null || value === undefined || Number.isNaN(Number(value))) {
            return "-";
        }

        return new Intl.NumberFormat("id-ID", {
            maximumFractionDigits: 4,
        }).format(Number(value));
    };

    const createSvg = (width, height, content) =>
        `<svg viewBox="0 0 ${width} ${height}" preserveAspectRatio="none" aria-hidden="true">${content}</svg>`;

    const escapeXml = (value) =>
        String(value ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&apos;");

    const buildLineChart = (container, series, options = {}) => {
        if (!container || !Array.isArray(series) || series.length === 0) return;

        const width = Math.max(container.clientWidth || 720, 320);
        const height = Math.max(container.clientHeight || 260, 220);
        const padding = { top: 28, right: 24, bottom: 56, left: 58 };
        const values = series.map((point) => Number(point.value)).filter((value) => Number.isFinite(value));

        if (values.length === 0) return;

        const minValue = Math.min(...values);
        const maxValue = Math.max(...values);
        const valueRange = maxValue - minValue || 1;
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const stepX = series.length > 1 ? chartWidth / (series.length - 1) : chartWidth;
        const yFor = (value) => padding.top + chartHeight - ((value - minValue) / valueRange) * chartHeight;
        const xFor = (index) => padding.left + index * stepX;
        const yTicks = Array.from({ length: 5 }, (_, index) => {
            const ratio = index / 4;
            const value = maxValue - ratio * valueRange;
            const y = padding.top + chartHeight * ratio;

            return {
                label: formatNumber(value),
                value,
                y,
            };
        });
        const xTickIndexes = [0, Math.floor((series.length - 1) / 2), series.length - 1]
            .filter((value, index, array) => array.indexOf(value) === index);

        const points = series
            .map((point, index) => `${xFor(index)},${yFor(Number(point.value))}`)
            .join(" ");

        const areaPoints = `${padding.left},${padding.top + chartHeight} ${points} ${padding.left + chartWidth},${padding.top + chartHeight}`;
        const grid = yTicks.map((tick) => {
            return `<line x1="${padding.left}" y1="${tick.y}" x2="${padding.left + chartWidth}" y2="${tick.y}" stroke="#cfe0d0" stroke-width="1" />`;
        }).join("");
        const xTicks = xTickIndexes.map((index) => `
            <line x1="${xFor(index)}" y1="${padding.top + chartHeight}" x2="${xFor(index)}" y2="${padding.top + chartHeight + 6}" stroke="#8ea48f" stroke-width="1" />
            <text x="${xFor(index)}" y="${height - 24}" fill="#5c6f5d" font-size="10" text-anchor="${index === 0 ? "start" : index === series.length - 1 ? "end" : "middle"}">${escapeXml(series[index]?.label ?? "")}</text>
        `).join("");
        const yAxisLabels = yTicks.map((tick) => `
            <text x="${padding.left - 8}" y="${tick.y + 4}" fill="#5c6f5d" font-size="10" text-anchor="end">${escapeXml(tick.label)}</text>
        `).join("");

        const stats = `
            <text x="${padding.left}" y="16" fill="#0D2D1E" font-size="12" font-weight="600">Min ${formatNumber(minValue)}</text>
            <text x="${padding.left + chartWidth}" y="16" fill="#0D2D1E" font-size="12" font-weight="600" text-anchor="end">Maks ${formatNumber(maxValue)}</text>
        `;
        const axisLabels = `
            <text x="${padding.left + chartWidth / 2}" y="${height - 6}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle">${escapeXml(options.xLabel || "Waktu")}</text>
            <text x="16" y="${padding.top + chartHeight / 2}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle" transform="rotate(-90 16 ${padding.top + chartHeight / 2})">${escapeXml(options.yLabel || "Nilai")}</text>
        `;

        container.innerHTML = createSvg(
            width,
            height,
            `
                <defs>
                    <linearGradient id="${container.id}-fill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="${options.fillStart || "#6F916F"}" stop-opacity="0.32" />
                        <stop offset="100%" stop-color="${options.fillEnd || "#6F916F"}" stop-opacity="0.02" />
                    </linearGradient>
                </defs>
                ${grid}
                <line x1="${padding.left}" y1="${padding.top}" x2="${padding.left}" y2="${padding.top + chartHeight}" stroke="#8ea48f" stroke-width="1.5" />
                <line x1="${padding.left}" y1="${padding.top + chartHeight}" x2="${padding.left + chartWidth}" y2="${padding.top + chartHeight}" stroke="#8ea48f" stroke-width="1.5" />
                <polygon points="${areaPoints}" fill="url(#${container.id}-fill)" />
                <polyline points="${points}" fill="none" stroke="${options.stroke || "#0D2D1E"}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                ${xTicks}
                ${yAxisLabels}
                ${stats}
                ${axisLabels}
            `
        );
    };

    const buildBarChart = (container, series, options = {}) => {
        if (!container || !Array.isArray(series) || series.length === 0) return;

        const width = Math.max(container.clientWidth || 720, 320);
        const height = Math.max(container.clientHeight || 320, 240);
        const padding = { top: 24, right: 24, bottom: 52, left: 52 };
        const values = series.map((item) => Number(item.value)).filter((value) => Number.isFinite(value));

        if (values.length === 0) return;

        const maxAbs = Math.max(...values.map((value) => Math.abs(value)), 0.1);
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const baseline = padding.top + chartHeight / 2;
        const barWidth = Math.max(8, chartWidth / Math.max(series.length * 1.8, 1));
        const gap = series.length > 1 ? (chartWidth - barWidth * series.length) / (series.length - 1) : 0;
        const yTickValues = [maxAbs, maxAbs / 2, 0, -(maxAbs / 2), -maxAbs];
        const yTicks = yTickValues.map((value) => {
            const ratio = (maxAbs - value) / (maxAbs * 2 || 1);
            return {
                value,
                y: padding.top + chartHeight * ratio,
            };
        });

        const bars = series.map((item, index) => {
            const value = Number(item.value);
            const scaled = (Math.abs(value) / maxAbs) * (chartHeight / 2 - 12);
            const x = padding.left + index * (barWidth + gap);
            const y = value >= 0 ? baseline - scaled : baseline;
            return `
                <rect x="${x}" y="${y}" width="${barWidth}" height="${scaled}" rx="4" fill="${options.barColor || "#3D5F3E"}" opacity="0.88" />
                <text x="${x + barWidth / 2}" y="${height - 14}" fill="#5c6f5d" font-size="10" text-anchor="middle">${item.lag}</text>
            `;
        }).join("");

        container.innerHTML = createSvg(
            width,
            height,
            `
                ${yTicks.map((tick) => `<line x1="${padding.left}" y1="${tick.y}" x2="${padding.left + chartWidth}" y2="${tick.y}" stroke="#cfe0d0" stroke-width="1" />`).join("")}
                <line x1="${padding.left}" y1="${baseline}" x2="${padding.left + chartWidth}" y2="${baseline}" stroke="#8ea48f" stroke-width="1.5" />
                <line x1="${padding.left}" y1="${padding.top}" x2="${padding.left}" y2="${padding.top + chartHeight}" stroke="#cfe0d0" stroke-width="1" />
                ${yTicks.map((tick) => `<text x="${padding.left - 8}" y="${tick.y + 4}" fill="#5c6f5d" font-size="10" text-anchor="end">${escapeXml(formatNumber(tick.value))}</text>`).join("")}
                <text x="${padding.left}" y="16" fill="#0D2D1E" font-size="12" font-weight="600">${options.label || "Lag correlation"}</text>
                <text x="${padding.left + chartWidth}" y="16" fill="#0D2D1E" font-size="12" text-anchor="end">+/-${formatNumber(maxAbs)}</text>
                <text x="${padding.left + chartWidth / 2}" y="${height - 8}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle">${escapeXml(options.xLabel || "Lag")}</text>
                <text x="16" y="${padding.top + chartHeight / 2}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle" transform="rotate(-90 16 ${padding.top + chartHeight / 2})">${escapeXml(options.yLabel || "Korelasi")}</text>
                ${bars}
            `
        );
    };

    const buildComparisonChart = (container, labels, actual, predicted) => {
        if (!container || !labels?.length || !actual?.length || !predicted?.length) return;

        const width = Math.max(container.clientWidth || 860, 420);
        const height = Math.max(container.clientHeight || 320, 240);
        const padding = { top: 24, right: 24, bottom: 56, left: 58 };
        const values = [...actual, ...predicted].map(Number).filter((value) => Number.isFinite(value));

        if (values.length === 0) return;

        const minValue = Math.min(...values);
        const maxValue = Math.max(...values);
        const range = maxValue - minValue || 1;
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const stepX = labels.length > 1 ? chartWidth / (labels.length - 1) : chartWidth;
        const yFor = (value) => padding.top + chartHeight - ((value - minValue) / range) * chartHeight;
        const xFor = (index) => padding.left + index * stepX;
        const buildPoints = (series) => series.map((value, index) => `${xFor(index)},${yFor(Number(value))}`).join(" ");
        const yTicks = Array.from({ length: 5 }, (_, index) => {
            const ratio = index / 4;
            const value = maxValue - ratio * range;
            const y = padding.top + chartHeight * ratio;

            return { value, y };
        });
        const xTickIndexes = [0, Math.floor((labels.length - 1) / 2), labels.length - 1]
            .filter((value, index, array) => array.indexOf(value) === index);

        container.innerHTML = createSvg(
            width,
            height,
            `
                <g stroke="#cfe0d0" stroke-width="1">
                    ${yTicks.map((tick) => `<line x1="${padding.left}" y1="${tick.y}" x2="${padding.left + chartWidth}" y2="${tick.y}" />`).join("")}
                </g>
                <line x1="${padding.left}" y1="${padding.top}" x2="${padding.left}" y2="${padding.top + chartHeight}" stroke="#8ea48f" stroke-width="1.5" />
                <line x1="${padding.left}" y1="${padding.top + chartHeight}" x2="${padding.left + chartWidth}" y2="${padding.top + chartHeight}" stroke="#8ea48f" stroke-width="1.5" />
                <polyline points="${buildPoints(actual)}" fill="none" stroke="#0D2D1E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="${buildPoints(predicted)}" fill="none" stroke="#6F916F" stroke-width="3" stroke-dasharray="8 6" stroke-linecap="round" stroke-linejoin="round" />
                ${yTicks.map((tick) => `<text x="${padding.left - 8}" y="${tick.y + 4}" fill="#5c6f5d" font-size="10" text-anchor="end">${escapeXml(formatNumber(tick.value))}</text>`).join("")}
                ${xTickIndexes.map((index) => `
                    <line x1="${xFor(index)}" y1="${padding.top + chartHeight}" x2="${xFor(index)}" y2="${padding.top + chartHeight + 6}" stroke="#8ea48f" stroke-width="1" />
                    <text x="${xFor(index)}" y="${height - 24}" fill="#5c6f5d" font-size="10" text-anchor="${index === 0 ? "start" : index === labels.length - 1 ? "end" : "middle"}">${escapeXml(labels[index] ?? "")}</text>
                `).join("")}
                <g transform="translate(${padding.left}, 14)">
                    <line x1="0" y1="0" x2="26" y2="0" stroke="#0D2D1E" stroke-width="3" />
                    <text x="34" y="4" fill="#0D2D1E" font-size="11">Aktual</text>
                    <line x1="110" y1="0" x2="136" y2="0" stroke="#6F916F" stroke-width="3" stroke-dasharray="8 6" />
                    <text x="144" y="4" fill="#0D2D1E" font-size="11">Prediksi</text>
                </g>
                <text x="${padding.left + chartWidth / 2}" y="${height - 6}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle">Waktu</text>
                <text x="16" y="${padding.top + chartHeight / 2}" fill="#0D2D1E" font-size="11" font-weight="600" text-anchor="middle" transform="rotate(-90 16 ${padding.top + chartHeight / 2})">NO2</text>
            `
        );
    };

    const renderCharts = () => {
        if (!visualizationData) return;

        buildLineChart(
            document.getElementById("preprocessRawChart"),
            visualizationData.preprocessing?.raw ?? [],
            { stroke: "#90A891", fillStart: "#B7C9B8", fillEnd: "#B7C9B8", xLabel: "Datetime", yLabel: "NO2" }
        );
        buildLineChart(
            document.getElementById("preprocessCleanChart"),
            visualizationData.preprocessing?.clean ?? [],
            { stroke: "#0D2D1E", fillStart: "#6F916F", fillEnd: "#6F916F", xLabel: "Datetime", yLabel: "NO2" }
        );
        buildLineChart(
            document.getElementById("differencingChart"),
            visualizationData.differencing?.series ?? [],
            { stroke: "#224C35", fillStart: "#6F916F", fillEnd: "#6F916F", xLabel: "Datetime", yLabel: "Differencing NO2" }
        );
        buildBarChart(
            document.getElementById("acfChart"),
            visualizationData.acf_pacf?.acf ?? [],
            { label: "ACF", barColor: "#49684A", xLabel: "Lag", yLabel: "Autokorelasi" }
        );
        buildBarChart(
            document.getElementById("pacfChart"),
            visualizationData.acf_pacf?.pacf ?? [],
            { label: "PACF", barColor: "#7D9680", xLabel: "Lag", yLabel: "Partial Autokorelasi" }
        );

        const comparison = visualizationData.forecasting?.comparison ?? {};
        buildComparisonChart(
            document.getElementById("forecastComparisonChart"),
            comparison.labels ?? [],
            comparison.actual ?? [],
            comparison.predicted ?? []
        );
    };

    if (openDatasetModalButton) {
        openDatasetModalButton.addEventListener("click", openDatasetModal);
    }

    if (closeDatasetModalButton) {
        closeDatasetModalButton.addEventListener("click", closeDatasetModal);
    }

    if (datasetModalBackdrop) {
        datasetModalBackdrop.addEventListener("click", closeDatasetModal);
    }

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && datasetModal && !datasetModal.classList.contains("hidden")) {
            closeDatasetModal();
        }
    });

    processTabs.forEach((tab) => {
        tab.addEventListener("click", () => {
            const targetPanel = tab.dataset.tab;

            processTabs.forEach((button) => {
                const isActive = button === tab;

                button.setAttribute("aria-pressed", String(isActive));
                button.classList.toggle("bg-[#0D2D1E]", isActive);
                button.classList.toggle("border-[#0D2D1E]", isActive);
                button.classList.toggle("text-white", isActive);
                button.classList.toggle("bg-neutral-100", !isActive);
                button.classList.toggle("border-neutral-200", !isActive);
                button.classList.toggle("text-neutral-600", !isActive);

                const stepLabel = button.querySelector("span");
                if (stepLabel) {
                    stepLabel.classList.toggle("text-white/70", isActive);
                    stepLabel.classList.toggle("text-neutral-400", !isActive);
                }
            });

            tabPanels.forEach((panel) => {
                panel.classList.toggle("hidden", panel.dataset.panel !== targetPanel);
            });

            requestAnimationFrame(renderCharts);
        });
    });

    if (visualizationData) {
        requestAnimationFrame(renderCharts);
        window.addEventListener("resize", renderCharts);
        if (preprocessingTab) {
            preprocessingTab.click();
        }
    }
});
