<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Dynamic Quiz Application - Test your knowledge across multiple categories and difficulty levels">
    <title>Dynamic Quiz Application - Home</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <h1 class="text-center">Dynamic Quiz Application</h1>
            <p class="text-center">Test your knowledge across multiple categories!</p>

            <form id="quizConfigForm" method="POST" action="quiz.php">
                <div class="form-group">
                    <label for="category">Select Category</label>
                    <select id="category" name="category" required>
                        <option value="">-- Choose a Category --</option>
                        <option value="general-knowledge">General Knowledge</option>
                        <option value="science">Science</option>
                        <option value="history">History</option>
                        <option value="sports">Sports</option>
                        <option value="technology">Technology</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Select Difficulty Level</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="">-- Choose Difficulty --</option>
                        <option value="easy">Easy (15 seconds per question)</option>
                        <option value="medium">Medium (20 seconds per question)</option>
                        <option value="hard">Hard (25 seconds per question)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="startBtn">
                    Start Quiz
                </button>
            </form>

            <div class="text-center mt-3">
                <p style="font-size: 0.9rem; color: var(--text-secondary);">
                    Each quiz contains 5 questions with countdown timers
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('quizConfigForm').addEventListener('submit', function (e) {
            const category = document.getElementById('category').value;
            const difficulty = document.getElementById('difficulty').value;

            if (!category || !difficulty) {
                e.preventDefault();
                alert('Please select both category and difficulty level!');
                return false;
            }

            const startBtn = document.getElementById('startBtn');
            startBtn.disabled = true;
            startBtn.textContent = 'Loading Quiz...';
        });

        const selects = document.querySelectorAll('select');
        selects.forEach(select => {
            select.addEventListener('change', function () {
                this.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    </script>
</body>

</html>