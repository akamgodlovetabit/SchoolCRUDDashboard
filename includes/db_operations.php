<?php
require_once 'config.php';

/**
 * Generates unique student ID based on year level and department
 * Format: [First letter of year][First 3 letters of department][4-digit sequence]
 * Sample: FENG0001 (First Year Engineering)
 *
 * @param mysqli $conn Database connection
 * @param string $year_level First Year or Second Year
 * @param string $department Department name
 * @return string Generated student ID
 */

function generateStudentID($conn, $year_level, $department)
{
    // Create prefix from year level and department
    $prefix = substr($year_level, 0, 1).substr($department, 0, 3);
    $prefix = strtoupper($prefix);

    // Find last student ID with same prefix
    $sql = "SELECT student_id FROM students WHERE student_id LIKE '$prefix%' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $last_id = mysqli_fetch_assoc($result)['student_id'];
        $last_num = intval(substr($last_id, -4));  // Extract last 4 digits
        $new_num = str_pad($last_num + 1, 4, '0', STR_PAD_LEFT);  // Increment
    } else {
        $new_num = '0001';  // BEgin from 0001 if no previous records
    }

    return $prefix.$new_num;
}

/**
 * Handles file upload for avatar and documents
 */
function uploadFile($file, $type = 'avatar')
{
    $upload_dir = '../uploads/';

    // Create uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error'];
    }

    // Check file size (max 2MB)
    if ($file['size'] > 2097152) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 2MB'];
    }

    // Validate file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif, pdf'];
    }

    // Generate unique filename
    $filename = $type.'_'.time().'_'.uniqid().'.'.$file_ext;
    $filepath = $upload_dir.$filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filepath' => $filepath, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file'];
    }
}

/**
 * Adds new student with all enhanced fields
 */
function addStudent($conn, $data, $files = [])
{
    // Generate unique student ID
    $student_id = generateStudentID($conn, $data['academic_year'], $data['department']);

    // Handle avatar upload
    $avatar_path = '';
    if (!empty($files['avatar']['name'])) {
        $upload_result = uploadFile($files['avatar'], 'avatar');
        if ($upload_result['success']) {
            $avatar_path = $upload_result['filepath'];
        }
    }

    $sql = 'INSERT INTO students (
        student_id, first_name, middle_name, last_name, avatar_path, date_of_birth, gender, 
        place_of_birth, email, phone, address, country, region, father_name, father_contact, 
        mother_name, mother_contact, guardian_name, guardian_contact, guardian_relationship,
        year_level, department, trade, academic_year, admission_date, certificate_name, 
        certificate_type, certificate_year, certificate_number, certificate_grade, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    mysqli_stmt_bind_param($stmt, 'sssssssssssssssssssssssssssssss',
        $student_id,
        $data['first_name'],
        $data['middle_name'],
        $data['last_name'],
        $avatar_path,
        $data['date_of_birth'],
        $data['gender'],
        $data['place_of_birth'],
        $data['email'],
        $data['phone'],
        $data['address'],
        $data['country'],
        $data['region'],
        $data['father_name'],
        $data['father_contact'],
        $data['mother_name'],
        $data['mother_contact'],
        $data['guardian_name'],
        $data['guardian_contact'],
        $data['guardian_relationship'],
        $data['year_level'],
        $data['department'],
        $data['trade'],
        $data['academic_year'],
        $data['admission_date'],
        $data['certificate_name'],
        $data['certificate_type'],
        $data['certificate_year'],
        $data['certificate_number'],
        $data['certificate_grade'],
        $data['notes']
    );

    $result = mysqli_stmt_execute($stmt);
    $student_insert_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Handle certificate document upload
    if ($result && !empty($files['certificate_document']['name'])) {
        $doc_upload = uploadFile($files['certificate_document'], 'certificate');
        if ($doc_upload['success']) {
            $doc_sql = "INSERT INTO student_documents (student_id, document_type, file_path) VALUES (?, 'Certificate', ?)";
            $doc_stmt = mysqli_prepare($conn, $doc_sql);
            mysqli_stmt_bind_param($doc_stmt, 'is', $student_insert_id, $doc_upload['filepath']);
            mysqli_stmt_execute($doc_stmt);
            mysqli_stmt_close($doc_stmt);
        }
    }

    return $result;
}

/**
 * Retrieves all students from database
 *
 * @param mysqli $conn Database connection
 * @return array Array of student records
 */
function getAllStudents($conn)
{
    $sql = 'SELECT * FROM students ORDER BY registration_date DESC';
    $result = mysqli_query($conn, $sql);

    $students = [];
    if ($result) {
        // Fetch all rows into array
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    return $students;
}

/**
 * Retrieves single student by ID
 *
 * @param mysqli $conn Database connection
 * @param int $id Student ID
 * @return array|null Student data or null if not found
 */
function getStudentById($conn, $id)
{
    $sql = 'SELECT * FROM students WHERE id = ?';
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        return null;
    }

    // Bind integer parameter
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    // Get result
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $student;
}

/**
 * Updates student information
 */
function updateStudent($conn, $id, $data, $files = [])
{
    // Handle avatar upload if new file provided
    $avatar_sql = '';
    $avatar_path = '';

    if (!empty($files['avatar']['name'])) {
        $upload_result = uploadFile($files['avatar'], 'avatar');
        if ($upload_result['success']) {
            $avatar_path = $upload_result['filepath'];
            $avatar_sql = ', avatar_path = ?';
        }
    }

    $sql = "UPDATE students SET 
        first_name = ?, middle_name = ?, last_name = ?, date_of_birth = ?, gender = ?, 
        place_of_birth = ?, email = ?, phone = ?, address = ?, country = ?, region = ?, 
        father_name = ?, father_contact = ?, mother_name = ?, mother_contact = ?, 
        guardian_name = ?, guardian_contact = ?, guardian_relationship = ?,
        year_level = ?, department = ?, trade = ?, academic_year = ?, admission_date = ?, 
        certificate_name = ?, certificate_type = ?, certificate_year = ?, 
        certificate_number = ?, certificate_grade = ?, notes = ?
        $avatar_sql
        WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false;
    }

    if (!empty($avatar_path)) {
        mysqli_stmt_bind_param($stmt, 'sssssssssssssssssssssssssssssssi',
            $data['first_name'], $data['middle_name'], $data['last_name'], $data['date_of_birth'],
            $data['gender'], $data['place_of_birth'], $data['email'], $data['phone'],
            $data['address'], $data['country'], $data['region'], $data['father_name'],
            $data['father_contact'], $data['mother_name'], $data['mother_contact'],
            $data['guardian_name'], $data['guardian_contact'], $data['guardian_relationship'],
            $data['year_level'], $data['department'], $data['trade'], $data['academic_year'],
            $data['admission_date'], $data['certificate_name'], $data['certificate_type'],
            $data['certificate_year'], $data['certificate_number'], $data['certificate_grade'],
            $data['notes'], $avatar_path, $id);
    } else {
        mysqli_stmt_bind_param($stmt, 'sssssssssssssssssssssssssssssi',
            $data['first_name'], $data['middle_name'], $data['last_name'], $data['date_of_birth'],
            $data['gender'], $data['place_of_birth'], $data['email'], $data['phone'],
            $data['address'], $data['country'], $data['region'], $data['father_name'],
            $data['father_contact'], $data['mother_name'], $data['mother_contact'],
            $data['guardian_name'], $data['guardian_contact'], $data['guardian_relationship'],
            $data['year_level'], $data['department'], $data['trade'], $data['academic_year'],
            $data['admission_date'], $data['certificate_name'], $data['certificate_type'],
            $data['certificate_year'], $data['certificate_number'], $data['certificate_grade'],
            $data['notes'], $id);
    }

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

/**
 * Deletes student from database
 *
 * @param mysqli $conn Database connection
 * @param int $id Student ID to delete
 * @return bool True on success, false on failure
 */
function deleteStudent($conn, $id)
{
    $sql = 'DELETE FROM students WHERE id = ?';
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

/**
 * Gets countries with their regions in a multidimensional array
 */
function getCountriesWithRegions() {
    return [
        'Ghana' => [
            'Ashanti Region', 'Greater Accra Region', 'Western Region', 'Eastern Region',
            'Central Region', 'Volta Region', 'Northern Region', 'Upper East Region',
            'Upper West Region', 'Bono Region', 'Ahafo Region', 'Bono East Region',
            'Oti Region', 'North East Region', 'Savannah Region', 'Western North Region'
        ],
        'Nigeria' => [
            'Abia State', 'Adamawa State', 'Akwa Ibom State', 'Anambra State', 'Bauchi State',
            'Bayelsa State', 'Benue State', 'Borno State', 'Cross River State', 'Delta State',
            'Ebonyi State', 'Edo State', 'Ekiti State', 'Enugu State', 'Gombe State',
            'Imo State', 'Jigawa State', 'Kaduna State', 'Kano State', 'Katsina State',
            'Kebbi State', 'Kogi State', 'Kwara State', 'Lagos State', 'Nasarawa State',
            'Niger State', 'Ogun State', 'Ondo State', 'Osun State', 'Oyo State',
            'Plateau State', 'Rivers State', 'Sokoto State', 'Taraba State', 'Yobe State',
            'Zamfara State', 'Federal Capital Territory'
        ],
        'Cameroon' => [
            'Adamawa', 'Centre', 'East', 'Far North', 'Littoral', 'North', 'Northwest',
            'South', 'Southwest', 'West'
        ],
        'Ivory Coast' => [
            'Abidjan', 'Bas-Sassandra', 'Comoé', 'Denguélé', 'Gôh-Djiboua', 'Lacs',
            'Lagunes', 'Montagnes', 'Sassandra-Marahoué', 'Savanes', 'Vallée du Bandama',
            'Woroba', 'Yamoussoukro', 'Zanzan'
        ],
        'Senegal' => [
            'Dakar', 'Diourbel', 'Fatick', 'Kaffrine', 'Kaolack', 'Kédougou', 'Kolda',
            'Louga', 'Matam', 'Saint-Louis', 'Sédhiou', 'Tambacounda', 'Thiès', 'Ziguinchor'
        ],
        'Togo' => [
            'Centrale', 'Kara', 'Maritime', 'Plateaux', 'Savanes'
        ],
        'Benin' => [
            'Alibori', 'Atakora', 'Atlantique', 'Borgou', 'Collines', 'Donga', 'Kouffo',
            'Littoral', 'Mono', 'Ouémé', 'Plateau', 'Zou'
        ],
        'Mali' => [
            'Bamako', 'Gao', 'Kayes', 'Kidal', 'Koulikoro', 'Ménaka', 'Mopti', 'Ségou',
            'Sikasso', 'Taoudénit', 'Tombouctou'
        ],
        'Burkina Faso' => [
            'Boucle du Mouhoun', 'Cascades', 'Centre', 'Centre-Est', 'Centre-Nord',
            'Centre-Ouest', 'Centre-Sud', 'Est', 'Hauts-Bassins', 'Nord', 'Plateau-Central',
            'Sahel', 'Sud-Ouest'
        ],
        'Niger' => [
            'Agadez', 'Diffa', 'Dosso', 'Maradi', 'Niamey', 'Tahoua', 'Tillabéri', 'Zinder'
        ],
        'Other' => [
            'Other Region'
        ]
    ];
}

/**
 * Gets regions for a specific country
 */
function getRegionsByCountry($country) {
    $countriesWithRegions = getCountriesWithRegions();
    return isset($countriesWithRegions[$country]) ? $countriesWithRegions[$country] : [];
}

/**
 * Gets all countries as a simple array
 */
function getCountries() {
    return array_keys(getCountriesWithRegions());
}

?>