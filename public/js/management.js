document.addEventListener('DOMContentLoaded', () => {
    const initChart = () => {
        const ctx = document.getElementById('expenseChart');
        if (!ctx) return;

        const cData = window.chartData || { labels: [], data: [] };
        const hasData = cData.labels.length > 0;

        const baseColors = [
            '#10B981', '#F59E0B', '#3B82F6', '#8B5CF6',
            '#EF4444', '#06B6D4', '#F43F5E', '#84CC16',
        ];

        const getColors = (count) => {
            return Array.from({ length: count }, (_, i) => baseColors[i % baseColors.length]);
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: hasData ? cData.labels : ['Belum Ada Data'],
                datasets: [{
                    data: hasData ? cData.data : [1],
                    backgroundColor: hasData ? getColors(cData.labels.length) : ['#E5E7EB'],
                    borderWidth: 0,
                    hoverOffset: hasData ? 4 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: hasData,
                        backgroundColor: '#1F2937',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.raw / total) * 100).toFixed(1) : '0';
                                const formatted = new Intl.NumberFormat('id-ID').format(context.raw);
                                return ` ${context.label}: Rp ${formatted} (${pct}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 600,
                }
            }
        });
    };

    initChart();
});
