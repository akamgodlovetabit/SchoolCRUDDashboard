<?php
require_once 'includes/db_operations.php';

$conn = getDBConnection();

// Get countries with regions
$countriesWithRegions = getCountriesWithRegions();

// Handle country selection and region loading
$selectedCountry = $_POST['country'] ?? 'Ghana'; // Default to Ghana
$regions = getRegionsByCountry($selectedCountry);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'first_name' => mysqli_real_escape_string($conn, $_POST['first_name']),
        'middle_name' => mysqli_real_escape_string($conn, $_POST['middle_name']),
        'last_name' => mysqli_real_escape_string($conn, $_POST['last_name']),
        'date_of_birth' => $_POST['date_of_birth'],
        'gender' => $_POST['gender'],
        'place_of_birth' => mysqli_real_escape_string($conn, $_POST['place_of_birth']),
        'email' => mysqli_real_escape_string($conn, $_POST['email']),
        'phone' => mysqli_real_escape_string($conn, $_POST['phone']),
        'address' => mysqli_real_escape_string($conn, $_POST['address']),
        'country' => $_POST['country'],
        'region' => $_POST['region'],
        'father_name' => mysqli_real_escape_string($conn, $_POST['father_name']),
        'father_contact' => mysqli_real_escape_string($conn, $_POST['father_contact']),
        'mother_name' => mysqli_real_escape_string($conn, $_POST['mother_name']),
        'mother_contact' => mysqli_real_escape_string($conn, $_POST['mother_contact']),
        'guardian_name' => mysqli_real_escape_string($conn, $_POST['guardian_name']),
        'guardian_contact' => mysqli_real_escape_string($conn, $_POST['guardian_contact']),
        'guardian_relationship' => mysqli_real_escape_string($conn, $_POST['guardian_relationship']),
        'year_level' => $_POST['year_level'],
        'department' => $_POST['department'],
        'trade' => mysqli_real_escape_string($conn, $_POST['trade']),
        'academic_year' => $_POST['academic_year'],
        'admission_date' => $_POST['admission_date'],
        'certificate_name' => mysqli_real_escape_string($conn, $_POST['certificate_name']),
        'certificate_type' => $_POST['certificate_type'],
        'certificate_year' => $_POST['certificate_year'],
        'certificate_number' => mysqli_real_escape_string($conn, $_POST['certificate_number']),
        'certificate_grade' => mysqli_real_escape_string($conn, $_POST['certificate_grade']),
        'notes' => mysqli_real_escape_string($conn, $_POST['notes'])
    ];
    
    if (addStudent($conn, $data, $_FILES)) {
        $_SESSION['message'] = "Student registered successfully!";
        $_SESSION['message_type'] = 'success';
        redirect('students.php');
    } else {
        $error = "Error registering student: " . mysqli_error($conn);
    }
}

require_once 'includes/header.php';
?>

<div class="registration-container">
    <div class="registration-header">
        <h2><i class="fas fa-user-graduate"></i> Student Registration Form</h2>
        <p>Complete all sections to register a new student</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="registration-form" id="studentForm">
        
        <!-- Personal Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-user"></i>
                <h3>Personal Information</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group profile-upload">
                    <label class="upload-label">
                        <i class="fas fa-camera"></i>
                        <span>Profile Picture</span>
                        <input type="file" name="avatar" accept="image/*" class="file-input">
                    </label>
                    <div class="upload-preview" id="avatarPreview">
                        <i class="fas fa-user"></i>
                        <span>No image selected</span>
                    </div>
                    <small>Max 2MB - JPG, PNG, GIF</small>
                </div>

                <div class="form-group full-width">
                    <div class="name-grid">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" required class="form-control" placeholder="Enter first name" value="<?php echo $_POST['first_name'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" placeholder="Enter middle name" value="<?php echo $_POST['middle_name'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" required class="form-control" placeholder="Enter last name" value="<?php echo $_POST['last_name'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Date of Birth <span class="required">*</span></label>
                    <input type="date" name="date_of_birth" required class="form-control" value="<?php echo $_POST['date_of_birth'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Gender <span class="required">*</span></label>
                    <select name="gender" required class="form-control">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($_POST['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($_POST['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($_POST['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Place of Birth</label>
                    <input type="text" name="place_of_birth" class="form-control" placeholder="City, Country" value="<?php echo $_POST['place_of_birth'] ?? ''; ?>">
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-address-book"></i>
                <h3>Contact Information</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" required class="form-control" placeholder="student@example.com" value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Phone Number <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="phone" required class="form-control" placeholder="+233 XX XXX XXXX" value="<?php echo $_POST['phone'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Residential Address <span class="required">*</span></label>
                    <textarea name="address" rows="3" required class="form-control" placeholder="Enter complete address"><?php echo $_POST['address'] ?? ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Country <span class="required">*</span></label>
                    <select name="country" required class="form-control" onchange="this.form.submit()">
                        <option value="">Select Country</option>
                        <?php foreach (getCountries() as $country): ?>
                            <option value="<?php echo $country; ?>" 
                                <?php echo ($selectedCountry == $country) ? 'selected' : ''; ?>>
                                <?php echo $country; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text">Changing country will reload the regions</small>
                </div>
                
                <div class="form-group">
                    <label>Region <span class="required">*</span></label>
                    <select name="region" required class="form-control">
                        <option value="">Select Region</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?php echo $region; ?>" 
                                <?php echo ($_POST['region'] ?? '') == $region ? 'selected' : ''; ?>>
                                <?php echo $region; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Guardian Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-users"></i>
                <h3>Guardian Information</h3>
            </div>
            
            <div class="form-subsection">
                <h4>Father's Information</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Father's Name</label>
                        <input type="text" name="father_name" class="form-control" placeholder="Enter father's full name" value="<?php echo $_POST['father_name'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Father's Contact</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="father_contact" class="form-control" placeholder="Phone number" value="<?php echo $_POST['father_contact'] ?? ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-subsection">
                <h4>Mother's Information</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Mother's Name</label>
                        <input type="text" name="mother_name" class="form-control" placeholder="Enter mother's full name" value="<?php echo $_POST['mother_name'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Mother's Contact</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="mother_contact" class="form-control" placeholder="Phone number" value="<?php echo $_POST['mother_contact'] ?? ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-subsection">
                <h4>Guardian's Information (If different from parents)</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Guardian's Name</label>
                        <input type="text" name="guardian_name" class="form-control" placeholder="Enter guardian's full name" value="<?php echo $_POST['guardian_name'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Guardian's Contact</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="guardian_contact" class="form-control" placeholder="Phone number" value="<?php echo $_POST['guardian_contact'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Relationship</label>
                        <input type="text" name="guardian_relationship" class="form-control" placeholder="e.g., Uncle, Aunt" value="<?php echo $_POST['guardian_relationship'] ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-graduation-cap"></i>
                <h3>Academic Information</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Year Level <span class="required">*</span></label>
                    <select name="year_level" required class="form-control">
                        <option value="">Select Year Level</option>
                        <option value="First Year" <?php echo ($_POST['year_level'] ?? '') == 'First Year' ? 'selected' : ''; ?>>First Year</option>
                        <option value="Second Year" <?php echo ($_POST['year_level'] ?? '') == 'Second Year' ? 'selected' : ''; ?>>Second Year</option>
                        <option value="Third Year" <?php echo ($_POST['year_level'] ?? '') == 'Third Year' ? 'selected' : ''; ?>>Third Year</option>
                        <option value="Fourth Year" <?php echo ($_POST['year_level'] ?? '') == 'Fourth Year' ? 'selected' : ''; ?>>Fourth Year</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Department <span class="required">*</span></label>
                    <select name="department" id="department" required class="form-control" onchange="updateTrades()">
                        <option value="">Select Department</option>
                        <option value="Engineering" <?php echo ($_POST['department'] ?? '') == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                        <option value="Science" <?php echo ($_POST['department'] ?? '') == 'Science' ? 'selected' : ''; ?>>Science</option>
                        <option value="Arts" <?php echo ($_POST['department'] ?? '') == 'Arts' ? 'selected' : ''; ?>>Arts</option>
                        <option value="Business" <?php echo ($_POST['department'] ?? '') == 'Business' ? 'selected' : ''; ?>>Business</option>
                        <option value="Medicine" <?php echo ($_POST['department'] ?? '') == 'Medicine' ? 'selected' : ''; ?>>Medicine</option>
                        <option value="Law" <?php echo ($_POST['department'] ?? '') == 'Law' ? 'selected' : ''; ?>>Law</option>
                        <option value="Education" <?php echo ($_POST['department'] ?? '') == 'Education' ? 'selected' : ''; ?>>Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Trade/Program <span class="required">*</span></label>
                    <select name="trade" id="trade" required class="form-control">
                        <option value="">Select Department First</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Academic Year <span class="required">*</span></label>
                    <select name="academic_year" required class="form-control">
                        <option value="">Select Year</option>
                        <?php for ($year = date('Y'); $year >= 2000; $year--): ?>
                            <option value="<?php echo $year; ?>" 
                                <?php echo ($_POST['academic_year'] ?? date('Y')) == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Admission Date <span class="required">*</span></label>
                    <input type="date" name="admission_date" required class="form-control" value="<?php echo $_POST['admission_date'] ?? date('Y-m-d'); ?>">
                </div>
            </div>
        </div>

        <!-- Certificate Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-file-certificate"></i>
                <h3>Certificate Information</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Certificate Name <span class="required">*</span></label>
                    <input type="text" name="certificate_name" required class="form-control" placeholder="e.g., WASSCE, Diploma" value="<?php echo $_POST['certificate_name'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Certificate Type <span class="required">*</span></label>
                    <select name="certificate_type" required class="form-control">
                        <option value="">Select Type</option>
                        <option value="WASSCE" <?php echo ($_POST['certificate_type'] ?? '') == 'WASSCE' ? 'selected' : ''; ?>>WASSCE</option>
                        <option value="SSSCE" <?php echo ($_POST['certificate_type'] ?? '') == 'SSSCE' ? 'selected' : ''; ?>>SSSCE</option>
                        <option value="GCE" <?php echo ($_POST['certificate_type'] ?? '') == 'GCE' ? 'selected' : ''; ?>>GCE</option>
                        <option value="BAC" <?php echo ($_POST['certificate_type'] ?? '') == 'BAC' ? 'selected' : ''; ?>>BAC</option>
                        <option value="Diploma" <?php echo ($_POST['certificate_type'] ?? '') == 'Diploma' ? 'selected' : ''; ?>>Diploma</option>
                        <option value="Degree" <?php echo ($_POST['certificate_type'] ?? '') == 'Degree' ? 'selected' : ''; ?>>Degree</option>
                        <option value="Other" <?php echo ($_POST['certificate_type'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Certificate Year <span class="required">*</span></label>
                    <select name="certificate_year" required class="form-control">
                        <option value="">Select Year</option>
                        <?php for ($year = date('Y'); $year >= 1980; $year--): ?>
                            <option value="<?php echo $year; ?>" 
                                <?php echo ($_POST['certificate_year'] ?? '') == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Certificate Number <span class="required">*</span></label>
                    <input type="text" name="certificate_number" required class="form-control" placeholder="Enter certificate number" value="<?php echo $_POST['certificate_number'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Certificate Grade</label>
                    <input type="text" name="certificate_grade" class="form-control" placeholder="e.g., A1, B2, C4" value="<?php echo $_POST['certificate_grade'] ?? ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="upload-label">
                    <i class="fas fa-upload"></i>
                    <span>Upload Certificate Document</span>
                    <input type="file" name="certificate_document" accept=".pdf,.jpg,.jpeg,.png" class="file-input">
                </label>
                <small>Upload certificate copy (PDF, JPG, PNG - Max 2MB)</small>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="form-section">
            <div class="section-header">
                <i class="fas fa-sticky-note"></i>
                <h3>Additional Information</h3>
            </div>
            
            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" rows="4" class="form-control" placeholder="Any additional information, special requirements, or comments..."><?php echo $_POST['notes'] ?? ''; ?></textarea>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-success btn-large">
                <i class="fas fa-user-plus"></i> Register Student
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset Form
            </button>
            <a href="students.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>
    </form>
</div>

<script>
// JavaScript for form enhancements (kept for other functionality)
function updateTrades() {
    // Your existing trade update logic
    const department = document.getElementById('department').value;
    const tradeSelect = document.getElementById('trade');
    
    tradeSelect.innerHTML = '<option value="">Select Trade/Program</option>';
    
    if (department) {
        const trades = getTradesByDepartment(department);
        trades.forEach(trade => {
            const option = document.createElement('option');
            option.value = trade;
            option.textContent = trade;
            tradeSelect.appendChild(option);
        });
    }
}

function getTradesByDepartment(department) {
    const tradeMap = {
        'Engineering': ['Civil Engineering', 'Mechanical Engineering', 'Electrical Engineering', 'Computer Engineering'],
        'Science': ['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology'],
        'Arts': ['English Literature', 'History', 'Sociology', 'Psychology', 'Political Science'],
        'Business': ['Accounting', 'Finance', 'Marketing', 'Management', 'Economics'],
        'Medicine': ['Medicine', 'Nursing', 'Pharmacy', 'Dentistry', 'Public Health'],
        'Law': ['Law', 'Criminal Justice', 'International Law', 'Corporate Law'],
        'Education': ['Early Childhood Education', 'Primary Education', 'Secondary Education', 'Special Education']
    };
    
    return tradeMap[department] || [];
}
</script>

<?php require_once 'includes/footer.php'; ?>

<style>
/* Enhanced Registration Form Styles */
.registration-container {
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    overflow: hidden;
}

.registration-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.registration-header h2 {
    margin: 0 0 10px 0;
    font-size: 2.2em;
    font-weight: 300;
}

.registration-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1em;
}

.registration-form {
    padding: 0;
}

.form-section {
    padding: 30px;
    border-bottom: 1px solid #eef2f7;
}

.form-section:last-child {
    border-bottom: none;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.section-header i {
    font-size: 1.5em;
    color: #667eea;
    margin-right: 15px;
}

.section-header h3 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.form-subsection {
    margin-bottom: 25px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.form-subsection h4 {
    margin: 0 0 15px 0;
    color: #4a5568;
    font-weight: 600;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    align-items: start;
}

.full-width {
    grid-column: 1 / -1;
}

.name-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.form-group {
    margin-bottom: 0;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.95em;
}

.required {
    color: #e53e3e;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1em;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

.input-with-icon .form-control {
    padding-left: 45px;
}

/* Profile Upload Styles */
.profile-upload {
    text-align: center;
}

.upload-label {
    display: inline-block;
    padding: 15px 25px;
    background: #f7fafc;
    border: 2px dashed #cbd5e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 15px;
}

.upload-label:hover {
    border-color: #667eea;
    background: #edf2f7;
}

.upload-label i {
    font-size: 1.5em;
    color: #667eea;
    margin-right: 10px;
}

.file-input {
    display: none;
}

.upload-preview {
    width: 120px;
    height: 120px;
    margin: 0 auto 10px;
    border: 2px dashed #cbd5e0;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f7fafc;
    transition: all 0.3s ease;
    overflow: hidden;
}

.upload-preview.has-image {
    border-style: solid;
    border-color: #667eea;
}

.upload-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-preview i {
    font-size: 2em;
    color: #a0aec0;
    margin-bottom: 5px;
}

.upload-preview span {
    font-size: 0.8em;
    color: #718096;
    text-align: center;
}

/* Button Styles */
.form-actions {
    padding: 30px;
    background: #f8fafc;
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1.1em;
}

.btn-success {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(72, 187, 120, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #4299e1, #3182ce);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
}

.btn-secondary {
    background: #a0aec0;
    color: white;
}

.btn-secondary:hover {
    background: #718096;
    transform: translateY(-2px);
}

/* Alert Styles */
.alert {
    padding: 15px 20px;
    margin: 20px 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: #fed7d7;
    color: #c53030;
    border-left: 4px solid #e53e3e;
}

/* Responsive Design */
@media (max-width: 768px) {
    .registration-container {
        margin: 10px;
        border-radius: 10px;
    }
    
    .registration-header {
        padding: 20px;
    }
    
    .registration-header h2 {
        font-size: 1.8em;
    }
    
    .form-section {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .name-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        padding: 20px;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-section {
    animation: fadeIn 0.5s ease-out;
}

/* Hover Effects */
.form-section:hover .section-header {
    border-bottom-color: #667eea;
}

/* Focus States */
.form-control:invalid {
    border-color: #fc8181;
}

.form-control:valid {
    border-color: #68d391;
}
</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
closeDBConnection($conn);
require_once 'includes/footer.php';
?>