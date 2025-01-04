# Web Novel Management System

This project is a web-based application for managing and sharing novels. It allows users to register, login, upload, and manage their novels. The application supports both short and long novels, with options for premium content.

## Features

- User registration and login
- Dashboard for managing novels
- Upload and manage short and long novels
- Admin control panel for managing users
- CSRF protection
- Email notifications using PHPMailer
- Secure password handling

## Project Structure
 ├── .gitignore 
 ├── controlpanel.php 
 ├── dashboard.php 
 ├── db/ 
 │ ├── conn.php 
 │ ├── crud.php 
 │ ├── snh_webproj_novels.sql 
 │ ├── token.php 
 │ └── user.php 
 ├── delete_novel.php 
 ├── incl/ 
 │ ├── auth_check.php 
 │ ├── bootstrap.php 
 │ ├── download_pdf.php 
 │ ├── https_check.php 
 │ ├── logout.php 
 │ ├── reCaptcha.json 
 │ ├── security_headers.php 
 │ ├── session.php 
 │ ├── update_premium.php 
 │ ├── utils.js 
 │ └── utils.php 
 ├── index.php 
 ├── insert_lgnovel.php 
 ├── insert_shnovel.php 
 ├── libs/ 
 │ └── PHPMailer-master/ 
 ├── logs/ 
 │ └── events.log 
 ├── psw_reset.php 
 ├── register.php 
 ├── request_psw_reset.php 
 └── uploads/ 
 └── long_novels/


## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/GabrieleGalli/SNH_WebProj_ReadNovels.git
    ```

2. Navigate to the project directory:
    ```sh
    cd SNH_WebProj_ReadNovels
    ```

3. Install dependencies:
    ```sh
    composer install
    ```

4. Set up the database:
    - Import the SQL file located at  into your MySQL database.

5. Configure the environment:
    - Copy `incl/.env.example` to `incl/.env` and update the database credentials and the other parameters to the correct ones. 

6. Start the server:
    ```sh
    php -S localhost:8000
    ```

## Usage

- Open your browser and navigate to `http://localhost:8000`.
- Register a new user or login with an existing account.
- Use the dashboard to upload and manage your novels.
- Admin users can access the control panel to manage other users.