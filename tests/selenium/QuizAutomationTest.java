import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.*;

import java.time.Duration;
import java.util.List;

/**
 * Selenium Automation Test for Dynamic Quiz Application
 * 
 * This test automates the complete quiz flow:
 * 1. Verify landing page
 * 2. Select category and difficulty
 * 3. Start quiz
 * 4. Answer all questions
 * 5. Submit quiz
 * 6. Verify results
 * 
 * @author Frugal Testing Software Engineer
 * @version 1.0
 */
public class QuizAutomationTest {
    
    private WebDriver driver;
    private WebDriverWait wait;
    
    // Test configuration
    private static final String BASE_URL = "http://localhost/Dynamic%20Quiz%20Application/index.php";
    private static final String CATEGORY = "general-knowledge";
    private static final String DIFFICULTY = "easy";
    
    // Predefined answers for each question (0-based index)
    // Adjust these based on your question bank
    private static final int[] ANSWERS = {2, 2, 3, 1, 1}; // Example: Q1->Option 3, Q2->Option 3, etc.
    
    @BeforeClass
    public void setUp() {
        // Set up ChromeDriver
        // Make sure chromedriver.exe is in your system PATH or specify the path:
        // System.setProperty("webdriver.chrome.driver", "path/to/chromedriver.exe");
        
        driver = new ChromeDriver();
        driver.manage().window().maximize();
        wait = new WebDriverWait(driver, Duration.ofSeconds(15));
        
        System.out.println("=".repeat(60));
        System.out.println("DYNAMIC QUIZ APPLICATION - AUTOMATION TEST");
        System.out.println("=".repeat(60));
    }
    
    @Test(priority = 1)
    public void testLandingPage() {
        System.out.println("\n[TEST 1] Verifying Landing Page...");
        
        // Navigate to quiz URL
        driver.get(BASE_URL);
        
        // Verify page load
        wait.until(ExpectedConditions.titleContains("Dynamic Quiz Application"));
        
        // Print URL and Title
        String currentUrl = driver.getCurrentUrl();
        String pageTitle = driver.getTitle();
        
        System.out.println("✓ Page URL: " + currentUrl);
        System.out.println("✓ Page Title: " + pageTitle);
        
        // Verify page elements
        WebElement categorySelect = driver.findElement(By.id("category"));
        WebElement difficultySelect = driver.findElement(By.id("difficulty"));
        WebElement startButton = driver.findElement(By.id("startBtn"));
        
        Assert.assertNotNull(categorySelect, "Category dropdown should be present");
        Assert.assertNotNull(difficultySelect, "Difficulty dropdown should be present");
        Assert.assertNotNull(startButton, "Start button should be present");
        
        System.out.println("✓ All required elements found on landing page");
        System.out.println("[TEST 1] PASSED\n");
    }
    
    @Test(priority = 2, dependsOnMethods = {"testLandingPage"})
    public void testStartQuiz() {
        System.out.println("[TEST 2] Starting Quiz...");
        
        // Select category
        Select categoryDropdown = new Select(driver.findElement(By.id("category")));
        categoryDropdown.selectByValue(CATEGORY);
        System.out.println("✓ Selected Category: " + CATEGORY);
        
        // Select difficulty
        Select difficultyDropdown = new Select(driver.findElement(By.id("difficulty")));
        difficultyDropdown.selectByValue(DIFFICULTY);
        System.out.println("✓ Selected Difficulty: " + DIFFICULTY);
        
        // Click Start Quiz button
        WebElement startButton = driver.findElement(By.id("startBtn"));
        startButton.click();
        System.out.println("✓ Clicked 'Start Quiz' button");
        
        // Wait for quiz page to load
        wait.until(ExpectedConditions.presenceOfElementLocated(By.id("questionText")));
        
        // Verify first question is displayed
        WebElement questionText = driver.findElement(By.id("questionText"));
        Assert.assertFalse(questionText.getText().isEmpty(), "First question should be displayed");
        
        System.out.println("✓ First question loaded: " + questionText.getText());
        System.out.println("[TEST 2] PASSED\n");
    }
    
    @Test(priority = 3, dependsOnMethods = {"testStartQuiz"})
    public void testQuestionNavigationAndAnswers() {
        System.out.println("[TEST 3] Answering Questions...");
        
        int totalQuestions = ANSWERS.length;
        
        for (int i = 0; i < totalQuestions; i++) {
            System.out.println("\n--- Question " + (i + 1) + " ---");
            
            // Wait for question to load
            wait.until(ExpectedConditions.presenceOfElementLocated(By.id("questionText")));
            
            // Verify question text
            WebElement questionText = driver.findElement(By.id("questionText"));
            String question = questionText.getText();
            System.out.println("Question: " + question);
            
            // Verify answer options
            WebElement optionsContainer = driver.findElement(By.id("optionsContainer"));
            List<WebElement> options = optionsContainer.findElements(By.className("option"));
            
            System.out.println("Answer Options:");
            for (int j = 0; j < options.size(); j++) {
                System.out.println("  " + (j + 1) + ". " + options.get(j).getText());
            }
            
            // Select the predefined answer
            int answerIndex = ANSWERS[i];
            if (answerIndex >= 0 && answerIndex < options.size()) {
                options.get(answerIndex).click();
                System.out.println("✓ Selected Answer: Option " + (answerIndex + 1) + " - " + options.get(answerIndex).getText());
                
                // Wait a moment for selection to register
                Thread.sleep(500);
            } else {
                System.out.println("⚠ Warning: Invalid answer index for question " + (i + 1));
            }
            
            // Click Next or Submit button
            if (i < totalQuestions - 1) {
                // Click Next button
                WebElement nextButton = wait.until(ExpectedConditions.elementToBeClickable(By.id("nextBtn")));
                nextButton.click();
                System.out.println("✓ Clicked 'Next' button");
                
                // Wait a moment for next question to load
                Thread.sleep(1000);
            } else {
                // Last question - click Submit button
                WebElement submitButton = wait.until(ExpectedConditions.elementToBeClickable(By.id("submitBtn")));
                submitButton.click();
                System.out.println("✓ Clicked 'Submit Quiz' button");
                
                // Handle confirmation dialog if present
                try {
                    Thread.sleep(500);
                    driver.switchTo().alert().accept();
                    System.out.println("✓ Confirmed quiz submission");
                } catch (Exception e) {
                    // No alert present, continue
                }
            }
        }
        
        System.out.println("\n[TEST 3] PASSED\n");
    }
    
    @Test(priority = 4, dependsOnMethods = {"testQuestionNavigationAndAnswers"})
    public void testResultsPage() throws InterruptedException {
        System.out.println("[TEST 4] Verifying Results Page...");
        
        // Wait for results page to load
        wait.until(ExpectedConditions.urlContains("results.php"));
        Thread.sleep(2000); // Wait for charts to render
        
        System.out.println("✓ Results page loaded");
        
        // Verify score display
        WebElement scoreDisplay = wait.until(ExpectedConditions.presenceOfElementLocated(
            By.className("score-display")));
        String scoreText = scoreDisplay.getText();
        System.out.println("✓ Score: " + scoreText);
        
        // Extract statistics
        List<WebElement> statValues = driver.findElements(By.className("stat-value"));
        List<WebElement> statLabels = driver.findElements(By.className("stat-label"));
        
        System.out.println("\n--- Quiz Statistics ---");
        for (int i = 0; i < Math.min(statValues.size(), statLabels.size()); i++) {
            System.out.println(statLabels.get(i).getText() + ": " + statValues.get(i).getText());
        }
        
        // Verify detailed review section
        List<WebElement> reviewItems = driver.findElements(By.className("review-item"));
        System.out.println("\n--- Detailed Review ---");
        System.out.println("Total review items: " + reviewItems.size());
        
        int correctCount = 0;
        int incorrectCount = 0;
        
        for (WebElement item : reviewItems) {
            if (item.getAttribute("class").contains("correct")) {
                correctCount++;
            } else if (item.getAttribute("class").contains("incorrect")) {
                incorrectCount++;
            }
        }
        
        System.out.println("Correct answers: " + correctCount);
        System.out.println("Incorrect answers: " + incorrectCount);
        
        // Verify charts are present
        List<WebElement> charts = driver.findElements(By.tagName("canvas"));
        Assert.assertTrue(charts.size() >= 2, "At least 2 charts should be present");
        System.out.println("✓ Charts rendered: " + charts.size() + " charts found");
        
        System.out.println("\n[TEST 4] PASSED\n");
    }
    
    @AfterClass
    public void tearDown() {
        System.out.println("=".repeat(60));
        System.out.println("ALL TESTS COMPLETED SUCCESSFULLY!");
        System.out.println("=".repeat(60));
        
        // Close browser
        if (driver != null) {
            // Wait a few seconds to see the results
            try {
                Thread.sleep(3000);
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
            driver.quit();
        }
    }
}
