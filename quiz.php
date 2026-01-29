<?php
require_once 'php/config.php';

if (!isset($_POST['category']) || !isset($_POST['difficulty'])) {
    header('Location: index.php');
    exit;
}

$category = sanitize_input($_POST['category']);
$difficulty = sanitize_input($_POST['difficulty']);

$_SESSION['selected_category'] = $category;
$_SESSION['selected_difficulty'] = $difficulty;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Take the quiz and test your knowledge">
    <title>Quiz - Dynamic Quiz Application</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div class="card card-wide">
            <div class="quiz-header">
                <div class="question-counter" id="questionCounter">
                    Question <span id="currentQuestion">1</span> of <span id="totalQuestions">5</span>
                </div>
                <div class="timer" id="timer">15</div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>

            <div id="questionContainer">
                <h3 class="question-text" id="questionText">Loading question...</h3>

                <div class="options-container" id="optionsContainer">
                </div>
            </div>

            <div class="navigation-buttons">
                <button class="btn btn-secondary" id="prevBtn" style="display: none;">
                    Previous
                </button>
                <button class="btn btn-primary" id="nextBtn" disabled>
                    Next
                </button>
                <button class="btn btn-success" id="submitBtn" style="display: none;">
                    Submit Quiz
                </button>
            </div>
        </div>
    </div>

    <script>
        const quizConfig = {
            category: '<?php echo $category; ?>',
            difficulty: '<?php echo $difficulty; ?>'
        };

        let questions = [];
        let currentQuestionIndex = 0;
        let userAnswers = [];
        let timeTaken = [];
        let timerInterval = null;
        let questionStartTime = 0;

        const questionText = document.getElementById('questionText');
        const optionsContainer = document.getElementById('optionsContainer');
        const currentQuestionSpan = document.getElementById('currentQuestion');
        const totalQuestionsSpan = document.getElementById('totalQuestions');
        const timerDisplay = document.getElementById('timer');
        const progressFill = document.getElementById('progressFill');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        async function initQuiz() {
            try {
                const response = await fetch('php/get-questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(quizConfig)
                });

                const data = await response.json();

                if (data.success) {
                    questions = data.questions;
                    totalQuestionsSpan.textContent = data.totalQuestions;

                    userAnswers = new Array(questions.length).fill(null);
                    timeTaken = new Array(questions.length).fill(0);

                    loadQuestion(0);
                } else {
                    alert('Error loading questions: ' + data.error);
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load quiz. Please try again.');
                window.location.href = 'index.php';
            }
        }

        function loadQuestion(index) {
            if (index < 0 || index >= questions.length) return;

            currentQuestionIndex = index;
            const question = questions[index];

            currentQuestionSpan.textContent = index + 1;

            const progress = ((index + 1) / questions.length) * 100;
            progressFill.style.width = progress + '%';

            questionText.textContent = question.question;

            optionsContainer.innerHTML = '';
            question.options.forEach((option, optIndex) => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'option';
                optionDiv.textContent = option;
                optionDiv.dataset.index = optIndex;

                if (userAnswers[index] === optIndex) {
                    optionDiv.classList.add('selected');
                }

                optionDiv.addEventListener('click', () => selectOption(optIndex));
                optionsContainer.appendChild(optionDiv);
            });

            prevBtn.style.display = index > 0 ? 'block' : 'none';

            if (index === questions.length - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                submitBtn.style.display = 'none';
            }

            nextBtn.disabled = userAnswers[index] === null;
            submitBtn.disabled = userAnswers[index] === null;

            startTimer(question.timeLimit);
            questionStartTime = Date.now();
        }

        function selectOption(optIndex) {
            document.querySelectorAll('.option').forEach(opt => {
                opt.classList.remove('selected');
            });

            event.target.classList.add('selected');

            userAnswers[currentQuestionIndex] = optIndex;

            nextBtn.disabled = false;
            submitBtn.disabled = false;
        }

        function startTimer(timeLimit) {
            if (timerInterval) {
                clearInterval(timerInterval);
            }

            let timeRemaining = timeLimit;
            timerDisplay.textContent = timeRemaining;
            timerDisplay.className = 'timer';

            timerInterval = setInterval(() => {
                timeRemaining--;
                timerDisplay.textContent = timeRemaining;

                if (timeRemaining <= 5) {
                    timerDisplay.className = 'timer danger';
                } else if (timeRemaining <= 10) {
                    timerDisplay.className = 'timer warning';
                } else {
                    timerDisplay.className = 'timer';
                }

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    recordTime();

                    if (userAnswers[currentQuestionIndex] === null) {
                        userAnswers[currentQuestionIndex] = -1;
                    }

                    if (currentQuestionIndex < questions.length - 1) {
                        loadQuestion(currentQuestionIndex + 1);
                    } else {
                        submitQuiz();
                    }
                }
            }, 1000);
        }

        function recordTime() {
            const timeSpent = Math.floor((Date.now() - questionStartTime) / 1000);
            timeTaken[currentQuestionIndex] = timeSpent;
        }

        prevBtn.addEventListener('click', () => {
            recordTime();
            clearInterval(timerInterval);
            loadQuestion(currentQuestionIndex - 1);
        });

        nextBtn.addEventListener('click', () => {
            recordTime();
            clearInterval(timerInterval);
            loadQuestion(currentQuestionIndex + 1);
        });

        submitBtn.addEventListener('click', () => {
            recordTime();
            clearInterval(timerInterval);
            submitQuiz();
        });

        async function submitQuiz() {
            if (!confirm('Are you sure you want to submit the quiz?')) {
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const response = await fetch('php/submit-quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        answers: userAnswers,
                        timeTaken: timeTaken
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = 'results.php';
                } else {
                    alert('Error submitting quiz: ' + data.error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Quiz';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to submit quiz. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Quiz';
            }
        }

        window.addEventListener('beforeunload', (e) => {
            e.preventDefault();
            e.returnValue = '';
        });

        initQuiz();
    </script>
</body>

</html>