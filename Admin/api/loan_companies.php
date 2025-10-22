<?php
// loan_companies.php
include 'DBcon.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Export to Excel functionality
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="loan_companies_export_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Get data from database
    $sql = "SELECT * FROM loan_companies ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    // Start Excel content
    echo "Loan Companies Export\n\n";
    echo "Company ID\tCompany Name\tInterest Rate (%)\tTerms\tContact Details\tStatus\tCreated Date\tUpdated Date\n";
    
    while($company = $result->fetch_assoc()) {
        // Clean data for Excel format
        $company_name = str_replace(["\t", "\n", "\r"], ' ', $company['company_name']);
        $terms = str_replace(["\t", "\n", "\r"], ' ', $company['terms']);
        $contact_details = str_replace(["\t", "\n", "\r"], ' ', $company['contact_details']);
        
        echo $company['company_id'] . "\t";
        echo $company_name . "\t";
        echo $company['interest_rate'] . "\t";
        echo $terms . "\t";
        echo $contact_details . "\t";
        echo $company['status'] . "\t";
        echo $company['created_at'] . "\t";
        echo $company['updated_at'] . "\n";
    }
    exit;
}

try {
    // Get all loan companies
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM loan_companies ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $companies = [];
        while($row = $result->fetch_assoc()) {
            $companies[] = $row;
        }
        
        sendResponse(true, 'Loan companies fetched successfully.', $companies);
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Add new loan company
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            // Validate required fields
            $required_fields = ['company_name', 'interest_rate', 'terms', 'contact_details'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    sendResponse(false, "Field '$field' is required");
                }
            }
            
            $company_name = $conn->real_escape_string($_POST['company_name']);
            $interest_rate = floatval($_POST['interest_rate']);
            $terms = $conn->real_escape_string($_POST['terms']);
            $contact_details = $conn->real_escape_string($_POST['contact_details']);
            
            $sql = "INSERT INTO loan_companies (company_name, interest_rate, terms, contact_details) 
                    VALUES ('$company_name', '$interest_rate', '$terms', '$contact_details')";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Loan company added successfully');
            } else {
                throw new Exception('Error adding loan company: ' . $conn->error);
            }
        }

        // Update loan company
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // Validate required fields
            if (empty($_POST['company_id'])) {
                sendResponse(false, "Company ID is required");
            }
            
            $company_id = intval($_POST['company_id']);
            $company_name = $conn->real_escape_string($_POST['company_name']);
            $interest_rate = floatval($_POST['interest_rate']);
            $terms = $conn->real_escape_string($_POST['terms']);
            $contact_details = $conn->real_escape_string($_POST['contact_details']);
            
            $sql = "UPDATE loan_companies SET 
                    company_name = '$company_name', 
                    interest_rate = '$interest_rate', 
                    terms = '$terms', 
                    contact_details = '$contact_details',
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE company_id = $company_id";
            
            if ($conn->query($sql)) {
                if ($conn->affected_rows > 0) {
                    sendResponse(true, 'Loan company updated successfully');
                } else {
                    sendResponse(false, 'No changes made or company not found');
                }
            } else {
                throw new Exception('Error updating loan company: ' . $conn->error);
            }
        }

        // Toggle loan company status
        if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
            if (empty($_POST['company_id']) || empty($_POST['current_status'])) {
                sendResponse(false, "Company ID and current status are required");
            }
            
            $company_id = intval($_POST['company_id']);
            $current_status = $conn->real_escape_string($_POST['current_status']);
            $new_status = $current_status === 'Active' ? 'Inactive' : 'Active';
            
            $sql = "UPDATE loan_companies SET status = '$new_status' WHERE company_id = $company_id";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Status updated successfully', ['new_status' => $new_status]);
            } else {
                throw new Exception('Error updating status: ' . $conn->error);
            }
        }

        // If no valid action specified
        sendResponse(false, "Invalid action specified");
    }

} catch (Exception $e) {
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>