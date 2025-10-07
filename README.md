I'm#Explanation of the Student Registration System

/ (root)
├── index.php
├── config.php
├── students.sql
├── add_student.php
├── students.php
├── edit_student.php
├── view_student.php
├── delete_student.php
└── includes/
    ├── header.php
    ├── footer.php
    └── db_operations.php

## 1. Database Structure & Configuration

### config.php - Database Configuration
**Explanation:**
- **`define()`**: Creates constants for database configuration that can't be changed during runtime
- **`getDBConnection()`**: 
  - Establishes connection to MySQL using mysqli
  - Returns connection object
  - Stops script execution if connection fails
- **`closeDBConnection()`**: Properly closes database connection
- **`redirect()`**: Sends HTTP header to redirect browser to different page

**How to call:**
```php
$conn = getDBConnection(); // TO Get connection
// Use $conn for database operations
closeDBConnection($conn); // Close connection when done
```

## 2. Database Operations (Core Functions)

### includes/db_operations.php

**Key Function Explanations:**

### generateStudentID()
- **Purpose**: Creates unique student IDs automatically
- **Logic**: 
  - Takes first letter of year level + first 3 letters of department
  - Finds last used number for that combination
  - Increments and pads to 4 digits
- **Example**: First Year Engineering → "F" + "ENG" + "0001" = "FENG0001"

### addStudent()
- **Purpose**: Inserts new student record
- **Security**: Uses prepared statements to prevent SQL injection
- **Process**: 
  1. Generates student ID
  2. Prepares SQL statement with placeholders
  3. Binds parameters
  4. Executes statement

### getAllStudents()
- **Purpose**: Fetches all students ordered by registration date
- **Return**: Array of associative arrays (each student is an array)

### getStudentById()
- **Purpose**: Fetches single student by primary key
- **Security**: Uses prepared statement for safety

### updateStudent()
- **Purpose**: Updates existing student record
- **Note**: Student ID cannot be changed (it's auto-generated)

### deleteStudent()
- **Purpose**: Removes student record from database

**How to call these functions:**
```php
$conn = getDBConnection();

// Add student
$student_data = [
    'first_name' => 'Akam',
    'last_name' => 'Godlove',
    // ... other data fields
];
addStudent($conn, $student_data);

// Get all students from the database
$students = getAllStudents($conn);

// Get single student with a givien id from the database
$student = getStudentById($conn, 1);

// Update student student with a given id (1) and the data to be updated ($update_data)
updateStudent($conn, 1, $updated_data);

// Delete student with a given id (1)
deleteStudent($conn, 1);

closeDBConnection($conn);
```

## 3. Session Management & Templates

### includes/header.php


**Explanation:**
- **`session_start()`**: Must be called before any output to enable sessions
- **`displayMessage()`**: 
  - Checks for stored messages in session
  - Returns formatted HTML
  - Clears message after displaying (so it doesn't show again)

## 4. Page Flow & Processing

**Form Processing Flow:**
1. **POST Request**: Form submitted → Process data
2. **Validation/Sanitization**: Clean input data
3. **Database Operation**: Call appropriate function
4. **Success**: Store message in session, redirect to list page
5. **Failure**: Show error on same form

## 5. Security Features

### SQL Injection Prevention
```php
// UNSAFE - vulnerable to SQL injection
$sql = "SELECT * FROM students WHERE id = $id"; 

// SAFE - uses prepared statements
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
```

### XSS Prevention
```php
// UNSAFE - vulnerable to XSS
echo $user_input;

// SAFE - escapes HTML characters
echo htmlspecialchars($user_input);
```

### Data Sanitization
```php
// Escape strings for database
$safe_input = mysqli_real_escape_string($conn, $_POST['input']);

// Validate email format
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Valid email
}
```

## 6. Complete Workflow Example

### Adding a New Student:
1. User visits `add_student.php`
2. Fills out form and submits
3. `add_student.php` processes POST data:
   - Sanitizes inputs
   - Calls `addStudent()`
   - Generates student ID automatically
4. On success:
   - Stores success message in session
   - Redirects to `students.php`
5. `students.php`:
   - Displays success message from session
   - Shows updated student list

### Editing a Student:
1. User clicks "Edit" on `students.php`
2. Goes to `edit_student.php?id=1`
3. Form pre-filled with existing data
4. On submit:
   - Calls `updateStudent()`
   - Redirects back to list with success message

## 7. Function Call Examples

### Basic Usage Pattern:
```php
// 1. Get database connection
$conn = getDBConnection();

// 2. Perform operations
$students = getAllStudents($conn);

// 3. Close connection
closeDBConnection($conn);
```

### With Error Handling:
```php
$conn = getDBConnection();

if (addStudent($conn, $data)) {
    $_SESSION['message'] = "Success!";
    redirect('students.php');
} else {
    $error = "Error: " . mysqli_error($conn);
}

closeDBConnection($conn);
```


## 8. Key PHP Concepts Used

### Prepared Statements:
- **Purpose**: Prevent SQL injection
- **Process**: 
  1. Prepare SQL with `?` placeholders
  2. Bind actual values to placeholders
  3. Execute statement

### Sessions:
- **Purpose**: Maintain state across page requests
- **Usage**: Storing messages, user login status, etc.

### HTTP Redirects:
- **Purpose**: Prevent form resubmission
- **Pattern**: POST → Process → Redirect → GET

### mysqli Functions:
- `mysqli_connect()` - Connect to database
- `mysqli_prepare()` - Create prepared statement
- `mysqli_stmt_bind_param()` - Bind parameters
- `mysqli_real_escape_string()` - Escape strings
- `mysqli_error()` - Get last error


CREATE DATABASE university_registration;
USE university_registration;

CREATE TABLE students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    
    -- Personal Information
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    avatar_path VARCHAR(255), -- Path to profile picture
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    place_of_birth VARCHAR(100),
    
    -- Contact Information
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    country VARCHAR(50) DEFAULT 'Ghana',
    region VARCHAR(50),
    
    -- Guardian Information
    father_name VARCHAR(100),
    father_contact VARCHAR(20),
    mother_name VARCHAR(100),
    mother_contact VARCHAR(20),
    guardian_name VARCHAR(100),
    guardian_contact VARCHAR(20),
    guardian_relationship VARCHAR(50),
    
    -- Academic Information
    year_level ENUM('First Year', 'Second Year', 'Third Year', 'Fourth Year') NOT NULL,
    department ENUM('Engineering', 'Science', 'Arts', 'Business', 'Medicine', 'Law', 'Education') NOT NULL,
    trade VARCHAR(100) NOT NULL,
    academic_year YEAR NOT NULL,
    admission_date DATE NOT NULL,
    
    -- Certification Information
    certificate_name VARCHAR(100) NOT NULL,
    certificate_type ENUM('WASSCE', 'SSSCE', 'GCE', 'BAC', 'Diploma', 'Degree', 'Other') NOT NULL,
    certificate_year YEAR NOT NULL,
    certificate_number VARCHAR(50) NOT NULL,
    certificate_grade VARCHAR(10),
    
    -- System Information
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive', 'Graduated', 'Suspended') DEFAULT 'Active',
    notes TEXT
);

-- Insert sample departments
INSERT INTO departments (department_name, description) VALUES
('Engineering', 'Engineering and Technology programs'),
('Science', 'Science and Mathematics programs'),
('Arts', 'Arts and Humanities programs'),
('Business', 'Business and Management programs'),
('Medicine', 'Medical and Health Science programs'),
('Law', 'Legal Studies programs'),
('Education', 'Education and Teaching programs');

-- Create table for file uploads tracking
CREATE TABLE student_documents (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    document_type ENUM('Certificate', 'Photo', 'Transcript', 'Birth_Certificate', 'Other'),
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);