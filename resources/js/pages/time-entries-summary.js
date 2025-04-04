import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    // Handle date range selection
    const dateRangeSelect = document.getElementById('date_range');
    const customDateRange = document.getElementById('custom-date-range');
    
    if (dateRangeSelect) {
        dateRangeSelect.addEventListener('change', function() {
            if (customDateRange) {
                customDateRange.classList.toggle('hidden', this.value !== 'custom');
            }
        });
        // Initial state
        if (customDateRange) {
            customDateRange.classList.toggle('hidden', dateRangeSelect.value !== 'custom');
        }
    }
    
    // Setup chart
    const chartElement = document.getElementById('timeDistributionChart');
    if (chartElement) { 
        const chartDataString = chartElement.dataset.chartData;
        if (chartDataString) {
            try {
                const chartData = JSON.parse(chartDataString);
                const ctx = chartElement.getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Hours',
                            data: chartData.values,
                            backgroundColor: 'rgba(79, 70, 229, 0.6)', // Indigo-600
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            borderRadius: 4, // Add some rounding
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + 'h';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false // Hide x-axis grid lines
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + ' hours';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (e) {
                console.error('Error parsing chart data:', e);
            }
        } else {
            console.error('Chart data attribute (data-chart-data) not found or empty.');
        }
    }
}); 