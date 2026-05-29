import Chart from 'chart.js/auto';

const chartRegistry = new Map();

const chartTheme = () => {
	const isDark = document.documentElement.classList.contains('dark');

	return {
		text: isDark ? '#d4d4d4' : '#525252',
		muted: isDark ? '#a3a3a3' : '#737373',
		grid: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(23, 23, 23, 0.08)',
		brand: '#159868',
		brandSoft: 'rgba(21, 152, 104, 0.14)',
	};
};

const chartDatasets = (datasets) => datasets.map((dataset) => ({
	borderWidth: dataset.borderWidth ?? (dataset.type === 'line' ? 3 : 0),
	tension: dataset.tension ?? 0.35,
	pointRadius: dataset.pointRadius ?? 0,
	pointHoverRadius: dataset.pointHoverRadius ?? 5,
	fill: dataset.fill ?? false,
	...dataset,
}));

const chartOptions = (type, options = {}) => {
	const palette = chartTheme();
	const circular = type === 'doughnut' || type === 'pie';

	return {
		responsive: true,
		maintainAspectRatio: false,
		plugins: {
			legend: {
				position: circular ? 'bottom' : 'top',
				labels: {
					color: palette.text,
					usePointStyle: true,
					padding: 18,
					font: {
						family: 'Cairo',
						size: 12,
						weight: '700',
					},
				},
			},
			tooltip: {
				backgroundColor: palette.text,
				titleColor: '#ffffff',
				bodyColor: '#ffffff',
				padding: 12,
				cornerRadius: 14,
				displayColors: true,
				rtl: true,
			},
		},
		scales: circular ? {} : {
			x: {
				ticks: {
					color: palette.muted,
					font: {
						family: 'Cairo',
						size: 11,
						weight: '700',
					},
				},
				grid: {
					color: 'transparent',
					drawBorder: false,
				},
				border: {
					display: false,
				},
			},
			y: {
				beginAtZero: true,
				ticks: {
					color: palette.muted,
					precision: 0,
					font: {
						family: 'Cairo',
						size: 11,
						weight: '700',
					},
				},
				grid: {
					color: palette.grid,
					drawBorder: false,
				},
				border: {
					display: false,
				},
			},
		},
		...options,
	};
};

const cleanupCharts = () => {
	for (const [element, chart] of chartRegistry.entries()) {
		if (!document.body.contains(element)) {
			chart.destroy();
			chartRegistry.delete(element);
		}
	}
};

const initDashboardCharts = () => {
	cleanupCharts();

	document.querySelectorAll('[data-dashboard-chart]').forEach((element) => {
		const config = element.dataset.dashboardChart;

		if (!config) {
			return;
		}

		if (chartRegistry.has(element)) {
			chartRegistry.get(element)?.destroy();
			chartRegistry.delete(element);
		}

		const parsed = JSON.parse(config);
		const chart = new Chart(element, {
			type: parsed.type,
			data: {
				labels: parsed.labels,
				datasets: chartDatasets(parsed.datasets ?? []),
			},
			options: chartOptions(parsed.type, parsed.options),
		});

		chartRegistry.set(element, chart);
	});
};

window.HiremeeDashboard = {
	initCharts: initDashboardCharts,
};

document.addEventListener('DOMContentLoaded', initDashboardCharts);
document.addEventListener('livewire:navigated', initDashboardCharts);
document.addEventListener('theme-changed', initDashboardCharts);
