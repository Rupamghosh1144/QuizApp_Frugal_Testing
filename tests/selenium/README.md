# Dynamic Quiz Application - Selenium Automation Tests

## Overview
This directory contains automated Selenium WebDriver tests for the Dynamic Quiz Application. The tests verify the complete quiz flow from landing page to results display.

## Prerequisites

### Required Software
1. **Java Development Kit (JDK)** - Version 11 or higher
   - Download: https://www.oracle.com/java/technologies/downloads/
   - Verify installation: `java -version`

2. **Maven** (if using Maven project)
   - Download: https://maven.apache.org/download.cgi
   - Verify installation: `mvn -version`

3. **IDE** - Eclipse or IntelliJ IDEA
   - Eclipse: https://www.eclipse.org/downloads/
   - IntelliJ IDEA: https://www.jetbrains.com/idea/download/

4. **ChromeDriver** (or other WebDriver)
   - Download: https://chromedriver.chromium.org/downloads
   - Match version with your Chrome browser
   - Add to system PATH or specify path in code

5. **Local Web Server** (XAMPP, WAMP, or PHP built-in server)
   - The quiz application must be running on localhost

## Project Structure
```
tests/selenium/
├── QuizAutomationTest.java    # Main test class
├── pom.xml                     # Maven dependencies
├── testng.xml                  # TestNG configuration
└── README.md                   # This file
```

## Setup Instructions

### Option 1: Using Eclipse

1. **Import Project**
   - Open Eclipse
   - File → Import → Existing Maven Projects
   - Browse to `tests/selenium` directory
   - Click Finish

2. **Install TestNG Plugin**
   - Help → Eclipse Marketplace
   - Search for "TestNG"
   - Install TestNG for Eclipse

3. **Update Maven Dependencies**
   - Right-click on project → Maven → Update Project
   - Check "Force Update of Snapshots/Releases"
   - Click OK

4. **Configure ChromeDriver**
   - Option A: Add chromedriver.exe to system PATH
   - Option B: Update code with path:
     ```java
     System.setProperty("webdriver.chrome.driver", "C:/path/to/chromedriver.exe");
     ```

### Option 2: Using IntelliJ IDEA

1. **Open Project**
   - File → Open
   - Select `tests/selenium` directory
   - Click OK

2. **Enable TestNG**
   - IntelliJ usually detects TestNG automatically
   - If not: File → Settings → Plugins → Search "TestNG" → Install

3. **Sync Maven**
   - Right-click on pom.xml
   - Maven → Reload Project

4. **Configure ChromeDriver** (same as Eclipse)

## Running Tests

### Before Running Tests
1. **Start Local Web Server**
   - XAMPP: Start Apache
   - Or use PHP built-in server:
     ```bash
     cd "d:/Dynamic Quiz Application"
     php -S localhost:80
     ```

2. **Verify Application URL**
   - Open browser and navigate to: `http://localhost/Dynamic%20Quiz%20Application/index.php`
   - Ensure the landing page loads correctly

3. **Update Test Configuration** (if needed)
   - Open `QuizAutomationTest.java`
   - Modify `BASE_URL` if your URL is different
   - Adjust `ANSWERS` array based on your question bank

### Running in Eclipse
1. Right-click on `QuizAutomationTest.java`
2. Run As → TestNG Test
3. View results in TestNG Results panel

### Running in IntelliJ IDEA
1. Right-click on `QuizAutomationTest.java`
2. Run 'QuizAutomationTest'
3. View results in Run panel

### Running with Maven (Command Line)
```bash
cd "d:/Dynamic Quiz Application/tests/selenium"
mvn clean test
```

## Test Scenarios

The automation suite includes the following test cases:

### Test 1: Verify Landing Page
- Opens the quiz URL
- Verifies page title and URL
- Prints URL and title to console
- Checks for required elements (category dropdown, difficulty dropdown, start button)

### Test 2: Start Quiz
- Selects quiz category (General Knowledge)
- Selects difficulty level (Easy)
- Clicks "Start Quiz" button
- Verifies first question is displayed

### Test 3: Question Navigation & Answer Selection
- For each question:
  - Verifies question text is displayed
  - Verifies answer options are present
  - Selects predefined answer
  - Clicks "Next" button (or "Submit" for last question)
- Handles quiz submission confirmation

### Test 4: Score Calculation & Results
- Verifies results page loads
- Extracts and displays score
- Verifies correct/incorrect answer counts
- Checks for presence of charts
- Displays detailed statistics

## Expected Console Output

```
============================================================
DYNAMIC QUIZ APPLICATION - AUTOMATION TEST
============================================================

[TEST 1] Verifying Landing Page...
✓ Page URL: http://localhost/Dynamic%20Quiz%20Application/index.php
✓ Page Title: Dynamic Quiz Application - Home
✓ All required elements found on landing page
[TEST 1] PASSED

[TEST 2] Starting Quiz...
✓ Selected Category: general-knowledge
✓ Selected Difficulty: easy
✓ Clicked 'Start Quiz' button
✓ First question loaded: What is the capital of France?
[TEST 2] PASSED

[TEST 3] Answering Questions...

--- Question 1 ---
Question: What is the capital of France?
Answer Options:
  1. London
  2. Berlin
  3. Paris
  4. Madrid
✓ Selected Answer: Option 3 - Paris
✓ Clicked 'Next' button

[... continues for all questions ...]

[TEST 4] Verifying Results Page...
✓ Results page loaded
✓ Score: 80%

--- Quiz Statistics ---
Correct Answers: 4
Incorrect Answers: 1
Total Questions: 5
Total Time: 45s

--- Detailed Review ---
Total review items: 5
Correct answers: 4
Incorrect answers: 1
✓ Charts rendered: 2 charts found

[TEST 4] PASSED

============================================================
ALL TESTS COMPLETED SUCCESSFULLY!
============================================================
```

## Customization

### Changing Test Data
Edit the constants in `QuizAutomationTest.java`:
```java
private static final String CATEGORY = "science";        // Change category
private static final String DIFFICULTY = "medium";       // Change difficulty
private static final int[] ANSWERS = {0, 1, 2, 3, 4};   // Change answers
```

### Testing Different Browsers
Replace ChromeDriver with other drivers:
```java
// Firefox
driver = new FirefoxDriver();

// Edge
driver = new EdgeDriver();
```

### Adjusting Timeouts
Modify wait duration:
```java
wait = new WebDriverWait(driver, Duration.ofSeconds(30)); // Increase to 30 seconds
```

## Troubleshooting

### Common Issues

1. **ChromeDriver version mismatch**
   - Error: "This version of ChromeDriver only supports Chrome version X"
   - Solution: Download matching ChromeDriver version

2. **Element not found**
   - Error: "NoSuchElementException"
   - Solution: Increase wait time or check element selectors

3. **Connection refused**
   - Error: "Failed to connect to localhost"
   - Solution: Ensure web server is running

4. **TestNG not found**
   - Error: "Cannot resolve symbol TestNG"
   - Solution: Install TestNG plugin and update Maven dependencies

## Best Practices

1. **Always start with a clean session** - Clear browser cache if tests behave unexpectedly
2. **Use explicit waits** - Already implemented in the test code
3. **Run tests in headless mode** for CI/CD (optional):
   ```java
   ChromeOptions options = new ChromeOptions();
   options.addArguments("--headless");
   driver = new ChromeDriver(options);
   ```

## Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Verify PHP error logs
3. Review Selenium WebDriver documentation: https://www.selenium.dev/documentation/

---

**Author**: Frugal Testing Software Engineer  
**Version**: 1.0.0  
**Last Updated**: 2026-01-29
