function updatePriorityChart(priorityData) {
    const ctx = document.getElementById('priority-chart').getContext('2d');
    if (charts.priority) charts.priority.destroy();
    
    charts.priority = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['High', 'Medium', 'Low'],
            datasets: [{
                data: [priorityData.high, priorityData.medium, priorityData.low],
                backgroundColor: ['#EF4444', '#F59E0B', '#10B981']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateHourlyChart(hourlyData) {
    const ctx = document.getElementById('hourly-chart').getContext('2d');
    if (charts.hourly) charts.hourly.destroy();

    const hours = Array.from({ length: 24 }, (_, i) => i);
    const counts = hours.map(hour => {
        const entry = hourlyData.find(d => d.hour === hour);
        return entry ? entry.count : 0;
    });

    charts.hourly = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hours.map(h => `${h}:00`),
            datasets: [{
                label: 'Tasks',
                data: counts,
                backgroundColor: '#3B82F6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function updateCompletionTrendChart(trendData) {
    const ctx = document.getElementById('completion-trend-chart').getContext('2d');
    if (charts.completionTrend) charts.completionTrend.destroy();

    charts.completionTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => moment(d.date).format('MMM D')),
            datasets: [{
                label: 'Completed Tasks',
                data: trendData.map(d => d.completed_count),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Task Completion'
                }
            }
        }
    });
}

function updateTimeTrendChart(trendData) {
    const ctx = document.getElementById('time-trend-chart').getContext('2d');
    if (charts.timeTrend) charts.timeTrend.destroy();

    charts.timeTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => moment(d.date).format('MMM D')),
            datasets: [{
                label: 'Hours Tracked',
                data: trendData.map(d => Math.round(d.total_duration)),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Time Tracking'
                }
            }
        }
    });
}

function updateCompletionTimeChart(trendData) {
    const ctx = document.getElementById('completion-time-chart').getContext('2d');
    if (charts.completionTime) charts.completionTime.destroy();

    charts.completionTime = new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => moment(d.date).format('MMM D')),
            datasets: [{
                label: 'Average Completion Time (hours)',
                data: trendData.map(d => Math.round(d.avg_completion_time)),
                borderColor: '#8B5CF6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Average Task Completion Time'
                }
            }
        }
    });
}

function updateTagCompletionChart(tagData) {
    const ctx = document.getElementById('tag-completion-chart').getContext('2d');
    if (charts.tagCompletion) charts.tagCompletion.destroy();

    // Sort tags by completion rate
    const sortedTags = [...tagData].sort((a, b) => b.completion_rate - a.completion_rate);

    charts.tagCompletion = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: sortedTags.map(t => t.name),
            datasets: [{
                label: 'Completion Rate (%)',
                data: sortedTags.map(t => t.completion_rate),
                backgroundColor: '#8B5CF6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
} 