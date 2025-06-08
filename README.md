# Expense Management System

A comprehensive web-based solution for tracking and managing personal or business expenses. Built with PHP, MySQL, and Bootstrap.

## Developer
- **Name:** M.Umar Farooq
- **Email:** m.umarfarooq0045@gmail.com

## Features
- User Authentication System
- Dashboard with Expense Overview
- Category Management
- Expense Tracking
- Detailed Reports & Analytics
- Responsive Design

## Requirements
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Apache Web Server
- XAMPP/WAMP/MAMP

## Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/Expense-Management-System.git
   ```

2. **Database Setup**
   - Create a new MySQL database named `expense_management`
   - Import the SQL file from `database/expense_management.sql`

3. **Configuration**
   - Navigate to `includes/dbconnection.php`
   - Update database credentials if needed:
     ```php
     $con = mysqli_connect("localhost", "root", "", "expense_management");
     ```

4. **Server Setup**
   - Place the project in your web server's root directory
     (e.g., `xampp/htdocs/` for XAMPP)
   - Start Apache and MySQL services

5. **Access the Application**
   - Open your web browser
   - Visit: `http://localhost/Expense/Expense-Management-System/`

## Project Structure
```
Expense-Management-System/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── includes/
│   ├── dbconnection.php
│   ├── header.php
│   └── sidebar.php
├── dashboard.php
├── add-expense.php
├── manage-expense.php
├── add-category.php
├── manage-category.php
└── README.md
```

## Usage
1. Register a new account
2. Login with your credentials
3. Add expense categories
4. Start managing your expenses
5. View reports and analytics

## Security Features
- Password Hashing
- SQL Injection Prevention
- Session Management
- Input Validation

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Support
For any queries or support, please contact:
- Email: m.umarfarooq0045@gmail.com