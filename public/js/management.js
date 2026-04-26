// public/js/management.js

document.addEventListener('DOMContentLoaded', () => {
    
    // Initialize Chart using dynamic data from Backend (window.chartData)
    const initChart = () => {
        const ctx = document.getElementById('expenseChart');
        if (!ctx) return;

        // Data from PHP Backend
        const cData = window.chartData || { labels: [], data: [] };
        const hasData = cData.labels.length > 0;

        // Generate soft colors based on label index
        const baseColors = [
            '#10B981', // Emerald
            '#F59E0B', // Amber
            '#3B82F6', // Blue
            '#8B5CF6', // Purple
            '#EF4444', // Red
            '#06B6D4', // Cyan
            '#F43F5E', // Rose
            '#84CC16', // Lime
        ];

        const generateColors = (count) => {
            let colors = [];
            for (let i = 0; i < count; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            return colors;
        };

        const chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: hasData ? cData.labels : ['Belum Ada Data'],
                datasets: [{
                    data: hasData ? cData.data : [1], // Show gray ring if 0
                    backgroundColor: hasData ? generateColors(cData.labels.length) : ['#E5E7EB'],
                    borderWidth: 0,
                    hoverOffset: hasData ? 4 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Poppins', sans-serif",
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        enabled: hasData,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    };

    initChart();
});
