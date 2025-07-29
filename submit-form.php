<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection function
function getDbConnection() {
        // Database configuration
    $host = 'localhost';
    $dbname = 'eduloome_eduloom_enquiries';
    $username = 'eduloome_eduloom_user';
    $password = 'Akash@1395#$Bobby';
    $port = '3306';
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        throw new Exception("Connection failed: " . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate a unique token for this form submission
    $formToken = md5(serialize($input) . time());
    
    // Check if this is a duplicate submission
    if (isset($_SESSION['last_form_submission']) && 
        (time() - $_SESSION['last_form_submission']) < 30) { // 30 seconds cooldown
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Please wait before submitting again.']);
        exit();
    }
    
    // Update the last submission time
    $_SESSION['last_form_submission'] = time();
    
    // Validate input
    if (!isset($input['name']) || !isset($input['email']) || !isset($input['phone']) || !isset($input['program'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    // Sanitize input
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($input['phone'], FILTER_SANITIZE_STRING);
    $program = filter_var($input['program'], FILTER_SANITIZE_STRING);
    $message = isset($input['message']) ? filter_var($input['message'], FILTER_SANITIZE_STRING) : '';
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit();
    }
    
    try {
        $pdo = getDbConnection();
        
        // Create enquiries table if it doesn't exist (MySQL syntax)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS enquiries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                program VARCHAR(100) NOT NULL,
                message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_enquiry (email, created_at),
                INDEX idx_email (email),
                INDEX idx_phone (phone),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Check for any existing submission with the same email or phone within the last 24 hours
        $checkStmt = $pdo->prepare("
            SELECT id, name, email, phone, created_at 
            FROM enquiries 
            WHERE (email = :email OR phone = :phone)
            AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        
        $checkStmt->execute([
            ':email' => $email,
            ':phone' => $phone
        ]);
        $existingEnquiry = $checkStmt->fetch();
        
        if ($existingEnquiry) {
            // If a submission exists within the last 24 hours
            $existingTime = new DateTime($existingEnquiry['created_at']);
            $currentTime = new DateTime();
            $hoursDiff = ($currentTime->getTimestamp() - $existingTime->getTimestamp()) / 3600;
            $hoursRemaining = ceil(24 - $hoursDiff);
            
            $duplicateField = '';
            $duplicateValue = '';
            
            if ($existingEnquiry['email'] === $email) {
                $duplicateField = 'email';
                $duplicateValue = $email;
                $message = "This email address has already been used for an enquiry. Please wait $hoursRemaining hours before submitting again.";
            } else {
                $duplicateField = 'phone';
                $duplicateValue = $phone;
                $message = "This phone number has already been used for an enquiry. Please wait $hoursRemaining hours before submitting again.";
            }
            
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'isDuplicate' => true,
                'duplicateField' => $duplicateField,
                'duplicateValue' => $duplicateValue,
                'message' => $message
            ]);
            exit();
        }
        
        // Insert enquiry with error handling for duplicates
        try {
            $stmt = $pdo->prepare("
                INSERT INTO enquiries (name, email, phone, program, message, created_at)
                VALUES (:name, :email, :phone, :program, :message, NOW())
            
            ");
            
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':program' => $program,
                ':message' => $message
            ]);
        } catch (PDOException $e) {
            // If it's a duplicate key error, just return success
            if ($e->getCode() == '23000') {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Your enquiry has been received.']);
                exit();
            }
            throw $e; // Re-throw if it's a different error
        }
        
        // Send email notification (optional)
        $to = 'your-email@example.com';
        $subject = 'New Enquiry from ' . $name;
        $emailMessage = "
            <h2>New Enquiry Received</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Program:</strong> {$program}</p>
            <p><strong>Message:</strong> {$message}</p>
        
        ";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $email . "\r\n";
        
        // Uncomment to enable email notifications
        // mail($to, $subject, $emailMessage, $headers);
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your enquiry! We will contact you soon.'
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
