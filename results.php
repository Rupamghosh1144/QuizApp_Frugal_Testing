<?php
require_once 'php/config.php';

if (!isset($_SESSION['quiz_results'])) {
    header('Location: index.php');
    exit;
}

$results = $_SESSION['quiz_results'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quiz Results and Performance Analysis">
    <title>Results - Dynamic Quiz Application</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="score-summary">
                <h1>Quiz Completed</h1>
                <div class="score-display">
                    <?php echo $results['percentage']; ?>%
                </div>
                <p class="score-label">Your Score</p>
                <p style="font-size: 1.1rem; color: var(--text-secondary);">
                    Category: <strong>
                        <?php echo ucwords(str_replace('-', ' ', $results['category'])); ?>
                    </strong> |
                    Difficulty: <strong>
                        <?php echo ucfirst($results['difficulty']); ?>
                    </strong>
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" style="color: var(--success-color);">
                        <?php echo $results['correctAnswers']; ?>
                    </div>
                    <div class="stat-label">Correct Answers</div>
                </div>

                <div class="stat-card">
                    <div class="stat-value" style="color: var(--danger-color);">
                        <?php echo $results['incorrectAnswers']; ?>
                    </div>
                    <div class="stat-label">Incorrect Answers</div>
                </div>

                <div class="stat-card">
                    <div class="stat-value" style="color: var(--primary-color);">
                        <?php echo $results['totalQuestions']; ?>
                    </div>
                    <div class="stat-label">Total Questions</div>
                </div>

                <div class="stat-card">
                    <div class="stat-value" style="color: var(--text-primary);">
                        <?php echo isset($results['totalTime']) ? $results['totalTime'] : 0; ?>s
                    </div>
                    <div class="stat-label">Total Time</div>
                </div>
            </div>

            <div class="chart-container" style="max-width: 400px; margin: 0 auto 2rem auto;">
                <h3 class="chart-title" style="text-align: center; margin-bottom: 1rem;">Performance Distribution</h3>
                <canvas id="pieChart"></canvas>
            </div>

            <div class="chart-container" style="margin-bottom: 2rem;">
                <h3 class="chart-title" style="text-align: center; margin-bottom: 1rem;">Time Spent Per Question</h3>
                <canvas id="barChart"></canvas>
            </div>

            <div class="question-review">
                <h2 class="text-center mb-3">Detailed Review</h2>
                <?php foreach ($results['detailedResults'] as $index => $result): ?>
                    <div class="review-item <?php echo $result['isCorrect'] ? 'correct' : 'incorrect'; ?>">
                        <div class="review-question">
                            <strong>Q
                                <?php echo $index + 1; ?>:
                            </strong>
                            <?php echo htmlspecialchars($result['question']); ?>
                        </div>
                        <div class="review-answer">
                            <strong>Your Answer:</strong>
                            <?php
                            if ($result['userAnswer'] >= 0 && $result['userAnswer'] < count($result['options'])) {
                                echo htmlspecialchars($result['options'][$result['userAnswer']]);
                            } else {
                                echo '<span style="color: var(--text-muted);">No answer selected</span>';
                            }
                            ?>
                            <?php echo $result['isCorrect'] ? '<span style="color: var(--success-color); font-weight: bold;">Correct</span>' : '<span style="color: var(--danger-color); font-weight: bold;">Incorrect</span>'; ?>
                        </div>
                        <?php if (!$result['isCorrect']): ?>
                            <div class="review-answer correct">
                                <strong>Correct Answer:</strong>
                                <?php echo htmlspecialchars($result['options'][$result['correctAnswer']]); ?>
                            </div>
                        <?php endif; ?>
                        <div class="review-answer"
                            style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.5rem;">
                            Time taken: <?php echo isset($result['timeTaken']) ? $result['timeTaken'] : 0; ?>s
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="navigation-buttons mt-4">
                <button class="btn btn-secondary" id="saveBtn" onclick="saveResults()">
                    Save Results
                </button>
                <button class="btn btn-primary" onclick="window.location.href='index.php'">
                    Take Another Quiz
                </button>
            </div>
        </div>
    </div>

    <script>
        const resultsData = <?php echo json_encode($results); ?>;

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Correct Answers', 'Incorrect Answers'],
                datasets: [{
                    data: [resultsData.correctAnswers, resultsData.incorrectAnswers],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444'
                    ],
                    borderColor: [
                        '#ffffff',
                        '#ffffff'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1f2937',
                            padding: 20,
                            font: {
                                family: 'Inter',
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        // Bar Chart - Time per question
        const barCtx = document.getElementById('barChart').getContext('2d');
        const questionLabels = resultsData.detailedResults.map((_, index) => `Q${index + 1}`);
        const timeTakenData = resultsData.detailedResults.map(result => result.timeTaken || 0);

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: questionLabels,
                datasets: [{
                    label: 'Time (seconds)',
                    data: timeTakenData,
                    backgroundColor: '#3b82f6',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                family: 'Inter'
                            }
                        },
                        grid: {
                            color: '#e5e7eb'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                family: 'Inter'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

        async function saveResults() {
            const saveBtn = event.target;
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            try {
                const response = await fetch('php/save-results.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Results saved successfully!\nResult ID: ' + data.resultId);
                    saveBtn.textContent = 'Saved';
                } else {
                    alert('Error saving results: ' + data.error);
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Results';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save results. Please try again.');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Results';
            }
        }
    </script>
</body>

</html>