# Dynamic Quiz Application

A PHP-based web application that allows users to take quizzes in various categories. The application validates answers server-side and provides a summary of results.

## Features

- **Category Selection**: Users can choose from categories like General Knowledge, Science, History, Sports, and Technology.
- **Difficulty Levels**: Support for Easy, Medium, and Hard difficulty settings.
- **Dynamic Questions**:  Integration with the Open Trivia Database API to fetch questions dynamically.
- **Secure Validation**: Server-side answer checking to prevent client-side manipulation.
- **Results Analysis**: Detailed breakdown of correct and incorrect answers.
- **Responsive Design**: Clean and accessible interface suitable for desktop and mobile devices.

## Requirements

- PHP 7.4 or higher
- A web server (Apache/Nginx) or PHP built-in server
- Internet connection (required for fetching questions from the API)

## Installation and Usage

### Option 1: Using XAMPP (Windows)

1.  Download and install XAMPP.
2.  Navigate to the XAMPP installation directory (usually `C:\xampp`).
3.  Open the `htdocs` folder.
4.  Create a new folder named `Dynamic Quiz Application`.
5.  Copy all project files into this new folder.
6.  Open the XAMPP Control Panel and start the Apache module.
7.  Open a web browser and navigate to:
    `http://localhost/Dynamic%20Quiz%20Application/`

### Option 2: Using PHP Built-in Server

1.  Open a terminal or command prompt.
2.  Navigate to the project directory.
3.  Run the following command:
    ```bash
    php -S localhost:8000
    ```
4.  Open a web browser and navigate to:
    `http://localhost:8000`

## File Structure

- `index.php`: Landing page for quiz configuration.
- `quiz.php`: Main quiz interface.
- `results.php`: Displays quiz results and performance.
- `php/`: Contains API handlers and configuration files.
    - `config.php`: General configuration and session management.
    - `get-questions.php`: Fetches questions from the external API.
    - `submit-quiz.php`: Processes submitted answers.
    - `save-results.php`: Saves results to JSON files.
- `css/`: Stylesheets.
- `data/`: Directory for storing saved results (JSON).

## API Reference

This application uses the Open Trivia Database (OpenTDB) for question data.
API Endpoint: `https://opentdb.com/api.php`

