<?php
// grant_providers.php
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
    header('Content-Disposition: attachment;filename="grant_providers_export_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Get data from database
    $sql = "SELECT * FROM grant_providers ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    // Start Excel content
    echo "Grant Providers Export\n\n";
    echo "Provider ID\tProvider Name\tGrant Amount\tTerms\tContact Details\tStatus\tCreated Date\tUpdated Date\n";
    
    while($provider = $result->fetch_assoc()) {
        // Clean data for Excel format
        $provider_name = str_replace(["\t", "\n", "\r"], ' ', $provider['provider_name']);
        $grant_amount = str_replace(["\t", "\n", "\r"], ' ', $provider['grant_amount']);
        $terms = str_replace(["\t", "\n", "\r"], ' ', $provider['terms']);
        $contact_details = str_replace(["\t", "\n", "\r"], ' ', $provider['contact_details']);
        
        echo $provider['provider_id'] . "\t";
        echo $provider_name . "\t";
        echo $grant_amount . "\t";
        echo $terms . "\t";
        echo $contact_details . "\t";
        echo $provider['status'] . "\t";
        echo $provider['created_at'] . "\t";
        echo $provider['updated_at'] . "\n";
    }
    exit;
}

try {
    // Get all grant providers
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM grant_providers ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $providers = [];
        while($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }
        
        sendResponse(true, 'Grant providers fetched successfully.', $providers);
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Add new grant provider
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            // Validate required fields
            $required_fields = ['provider_name', 'grant_amount', 'terms', 'contact_details'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    sendResponse(false, "Field '$field' is required");
                }
            }
            
            $provider_name = $conn->real_escape_string($_POST['provider_name']);
            $grant_amount = $conn->real_escape_string($_POST['grant_amount']);
            $terms = $conn->real_escape_string($_POST['terms']);
            $contact_details = $conn->real_escape_string($_POST['contact_details']);
            
            $sql = "INSERT INTO grant_providers (provider_name, grant_amount, terms, contact_details) 
                    VALUES ('$provider_name', '$grant_amount', '$terms', '$contact_details')";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Grant provider added successfully');
            } else {
                throw new Exception('Error adding grant provider: ' . $conn->error);
            }
        }

        // Update grant provider
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // Validate required fields
            if (empty($_POST['provider_id'])) {
                sendResponse(false, "Provider ID is required");
            }
            
            $provider_id = intval($_POST['provider_id']);
            $provider_name = $conn->real_escape_string($_POST['provider_name']);
            $grant_amount = $conn->real_escape_string($_POST['grant_amount']);
            $terms = $conn->real_escape_string($_POST['terms']);
            $contact_details = $conn->real_escape_string($_POST['contact_details']);
            
            $sql = "UPDATE grant_providers SET 
                    provider_name = '$provider_name', 
                    grant_amount = '$grant_amount', 
                    terms = '$terms', 
                    contact_details = '$contact_details',
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE provider_id = $provider_id";
            
            if ($conn->query($sql)) {
                if ($conn->affected_rows > 0) {
                    sendResponse(true, 'Grant provider updated successfully');
                } else {
                    sendResponse(false, 'No changes made or provider not found');
                }
            } else {
                throw new Exception('Error updating grant provider: ' . $conn->error);
            }
        }

        // Toggle grant provider status
        if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
            if (empty($_POST['provider_id']) || empty($_POST['current_status'])) {
                sendResponse(false, "Provider ID and current status are required");
            }
            
            $provider_id = intval($_POST['provider_id']);
            $current_status = $conn->real_escape_string($_POST['current_status']);
            $new_status = $current_status === 'Active' ? 'Inactive' : 'Active';
            
            $sql = "UPDATE grant_providers SET status = '$new_status' WHERE provider_id = $provider_id";
            
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