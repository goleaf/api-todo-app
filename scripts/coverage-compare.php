<?php

/**
 * Test Coverage Comparison Tool
 * 
 * This script compares Vue.js and Livewire test coverage reports and generates a summary.
 * It helps teams track the progress of migration from Vue to Livewire.
 * 
 * Usage:
 * php scripts/coverage-compare.php [vue-coverage-path] [livewire-coverage-path]
 * 
 * Requirements:
 * - Vue.js coverage generated using Jest coverage reporter
 * - Livewire coverage generated using PHPUnit coverage reporter
 */

// Set default paths
$vueCoveragePath = $argv[1] ?? 'coverage-vue/coverage-summary.json';
$livewireCoveragePath = $argv[2] ?? 'coverage/coverage-summary.json';

// Output file
$outputFile = 'coverage-comparison.html';

// Parse Vue.js coverage
if (file_exists($vueCoveragePath)) {
    $vueCoverage = json_decode(file_get_contents($vueCoveragePath), true);
} else {
    echo "Vue.js coverage file not found at: $vueCoveragePath\n";
    echo "Using placeholder data instead.\n";
    
    // Use placeholder data for demonstration
    $vueCoverage = [
        'total' => [
            'lines' => ['pct' => 75],
            'statements' => ['pct' => 73],
            'functions' => ['pct' => 68],
            'branches' => ['pct' => 62],
        ]
    ];
}

// Parse Livewire coverage
if (file_exists($livewireCoveragePath)) {
    $livewireCoverage = json_decode(file_get_contents($livewireCoveragePath), true);
} else {
    echo "Livewire coverage file not found at: $livewireCoveragePath\n";
    echo "Using placeholder data instead.\n";
    
    // Use placeholder data for demonstration
    $livewireCoverage = [
        'total' => [
            'lines' => ['pct' => 82],
            'statements' => ['pct' => 80],
            'functions' => ['pct' => 75],
            'branches' => ['pct' => 70],
        ]
    ];
}

// Extract totals
$vueTotals = $vueCoverage['total'] ?? [];
$livewireTotals = $livewireCoverage['total'] ?? [];

// Component coverage data (would be extracted from detailed reports in a full implementation)
$componentCoverage = [
    'Authentication' => [
        'Vue' => 80,
        'Livewire' => 95,
        'Status' => 'Completed'
    ],
    'Task Management' => [
        'Vue' => 75,
        'Livewire' => 90,
        'Status' => 'Completed'
    ],
    'Dashboard' => [
        'Vue' => 70,
        'Livewire' => 85,
        'Status' => 'Completed'
    ],
    'Profile' => [
        'Vue' => 65,
        'Livewire' => 0,
        'Status' => 'Pending'
    ],
    'Calendar' => [
        'Vue' => 60,
        'Livewire' => 0,
        'Status' => 'Pending'
    ],
    'Statistics' => [
        'Vue' => 55,
        'Livewire' => 0,
        'Status' => 'Pending'
    ],
];

// Generate HTML report
$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Coverage Comparison: Vue.js vs Livewire</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .progress-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 4px;
            height: 20px;
        }
        .progress-bar {
            height: 20px;
            border-radius: 4px;
            text-align: center;
            color: white;
            font-weight: bold;
        }
        .vue-bar {
            background-color: #42b883;
        }
        .livewire-bar {
            background-color: #fb70a9;
        }
        .summary-box {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .chart-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 30px 0;
        }
        .chart {
            width: 45%;
            min-width: 300px;
            height: 300px;
            margin-bottom: 20px;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #dc3545;
            font-weight: bold;
        }
        .status-in-progress {
            color: #fd7e14;
            font-weight: bold;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Test Coverage Comparison: Vue.js vs Livewire</h1>
    <p>This report compares test coverage between the original Vue.js components and the migrated Livewire components.</p>
    
    <div class="summary-box">
        <h2>Summary</h2>
        <p>
            <strong>Vue.js Overall Coverage:</strong> ' . ($vueTotals['lines']['pct'] ?? 'N/A') . '%<br>
            <strong>Livewire Overall Coverage:</strong> ' . ($livewireTotals['lines']['pct'] ?? 'N/A') . '%<br>
            <strong>Coverage Improvement:</strong> ' . (($livewireTotals['lines']['pct'] ?? 0) - ($vueTotals['lines']['pct'] ?? 0)) . '%
        </p>
    </div>
    
    <h2>Coverage Details</h2>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th>Vue.js</th>
                <th>Livewire</th>
                <th>Difference</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Lines</td>
                <td>' . ($vueTotals['lines']['pct'] ?? 'N/A') . '%</td>
                <td>' . ($livewireTotals['lines']['pct'] ?? 'N/A') . '%</td>
                <td>' . (($livewireTotals['lines']['pct'] ?? 0) - ($vueTotals['lines']['pct'] ?? 0)) . '%</td>
            </tr>
            <tr>
                <td>Statements</td>
                <td>' . ($vueTotals['statements']['pct'] ?? 'N/A') . '%</td>
                <td>' . ($livewireTotals['statements']['pct'] ?? 'N/A') . '%</td>
                <td>' . (($livewireTotals['statements']['pct'] ?? 0) - ($vueTotals['statements']['pct'] ?? 0)) . '%</td>
            </tr>
            <tr>
                <td>Functions</td>
                <td>' . ($vueTotals['functions']['pct'] ?? 'N/A') . '%</td>
                <td>' . ($livewireTotals['functions']['pct'] ?? 'N/A') . '%</td>
                <td>' . (($livewireTotals['functions']['pct'] ?? 0) - ($vueTotals['functions']['pct'] ?? 0)) . '%</td>
            </tr>
            <tr>
                <td>Branches</td>
                <td>' . ($vueTotals['branches']['pct'] ?? 'N/A') . '%</td>
                <td>' . ($livewireTotals['branches']['pct'] ?? 'N/A') . '%</td>
                <td>' . (($livewireTotals['branches']['pct'] ?? 0) - ($vueTotals['branches']['pct'] ?? 0)) . '%</td>
            </tr>
        </tbody>
    </table>
    
    <h2>Component Coverage</h2>
    <table>
        <thead>
            <tr>
                <th>Component Group</th>
                <th>Vue.js</th>
                <th>Livewire</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($componentCoverage as $component => $data) {
    $statusClass = '';
    switch ($data['Status']) {
        case 'Completed':
            $statusClass = 'status-completed';
            break;
        case 'Pending':
            $statusClass = 'status-pending';
            break;
        case 'In Progress':
            $statusClass = 'status-in-progress';
            break;
    }
    
    $html .= '
            <tr>
                <td>' . htmlspecialchars($component) . '</td>
                <td>
                    <div class="progress-container">
                        <div class="progress-bar vue-bar" style="width: ' . $data['Vue'] . '%">' . $data['Vue'] . '%</div>
                    </div>
                </td>
                <td>
                    <div class="progress-container">
                        <div class="progress-bar livewire-bar" style="width: ' . $data['Livewire'] . '%">' . $data['Livewire'] . '%</div>
                    </div>
                </td>
                <td class="' . $statusClass . '">' . $data['Status'] . '</td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="chart-container">
        <div class="chart">
            <canvas id="overallCoverage"></canvas>
        </div>
        <div class="chart">
            <canvas id="componentCoverage"></canvas>
        </div>
    </div>
    
    <h2>Recommendations</h2>
    <ul>
        <li>Focus on implementing tests for components with 0% Livewire coverage</li>
        <li>Target achieving at least the same coverage percentage as Vue.js components</li>
        <li>Improve overall test coverage by adding edge case tests</li>
        <li>Consider using Livewire testing helpers to simplify test writing</li>
    </ul>
    
    <script>
        // Overall Coverage Chart
        const ctxOverall = document.getElementById("overallCoverage").getContext("2d");
        new Chart(ctxOverall, {
            type: "bar",
            data: {
                labels: ["Lines", "Statements", "Functions", "Branches"],
                datasets: [
                    {
                        label: "Vue.js",
                        data: [
                            ' . ($vueTotals['lines']['pct'] ?? 0) . ',
                            ' . ($vueTotals['statements']['pct'] ?? 0) . ',
                            ' . ($vueTotals['functions']['pct'] ?? 0) . ',
                            ' . ($vueTotals['branches']['pct'] ?? 0) . '
                        ],
                        backgroundColor: "#42b883",
                    },
                    {
                        label: "Livewire",
                        data: [
                            ' . ($livewireTotals['lines']['pct'] ?? 0) . ',
                            ' . ($livewireTotals['statements']['pct'] ?? 0) . ',
                            ' . ($livewireTotals['functions']['pct'] ?? 0) . ',
                            ' . ($livewireTotals['branches']['pct'] ?? 0) . '
                        ],
                        backgroundColor: "#fb70a9",
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: "Overall Coverage Comparison"
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
        
        // Component Coverage Chart
        const ctxComponent = document.getElementById("componentCoverage").getContext("2d");
        new Chart(ctxComponent, {
            type: "bar",
            data: {
                labels: [' . implode(', ', array_map(function($component) {
                    return '"' . addslashes($component) . '"';
                }, array_keys($componentCoverage))) . '],
                datasets: [
                    {
                        label: "Vue.js",
                        data: [' . implode(', ', array_column($componentCoverage, 'Vue')) . '],
                        backgroundColor: "#42b883",
                    },
                    {
                        label: "Livewire",
                        data: [' . implode(', ', array_column($componentCoverage, 'Livewire')) . '],
                        backgroundColor: "#fb70a9",
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: "Component Coverage Comparison"
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
    
    <footer>
        <p>Generated on ' . date('Y-m-d H:i:s') . '</p>
    </footer>
</body>
</html>';

// Save the HTML report
file_put_contents($outputFile, $html);

echo "Coverage comparison report generated: $outputFile\n";
echo "Open this file in a browser to view the report.\n"; 