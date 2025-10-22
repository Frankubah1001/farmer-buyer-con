<?php
// farm_tools.php
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
    header('Content-Disposition: attachment;filename="farm_tools_export_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Get data from database
    $sql = "SELECT * FROM farm_tools ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    // Start Excel content
    echo "Farm Tools Export\n\n";
    echo "Tool ID\tTool Name\tDescription\tStatus\tCreated Date\tUpdated Date\n";
    
    while($tool = $result->fetch_assoc()) {
        // Clean data for Excel format
        $tool_name = str_replace(["\t", "\n", "\r"], ' ', $tool['tool_name']);
        $description = str_replace(["\t", "\n", "\r"], ' ', $tool['description']);
        
        echo $tool['tool_id'] . "\t";
        echo $tool_name . "\t";
        echo $description . "\t";
        echo $tool['status'] . "\t";
        echo $tool['created_at'] . "\t";
        echo $tool['updated_at'] . "\n";
    }
    exit;
}

try {
    // Get all farm tools
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT * FROM farm_tools ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $tools = [];
        while($row = $result->fetch_assoc()) {
            $tools[] = $row;
        }
        
        sendResponse(true, 'Farm tools fetched successfully.', $tools);
    }

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Add new farm tool
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            // Validate required fields
            $required_fields = ['tool_name', 'description'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    sendResponse(false, "Field '$field' is required");
                }
            }
            
            $tool_name = $conn->real_escape_string($_POST['tool_name']);
            $description = $conn->real_escape_string($_POST['description']);
            
            $sql = "INSERT INTO farm_tools (tool_name, description) 
                    VALUES ('$tool_name', '$description')";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Farm tool added successfully');
            } else {
                throw new Exception('Error adding farm tool: ' . $conn->error);
            }
        }

        // Update farm tool
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            // Validate required fields
            if (empty($_POST['tool_id'])) {
                sendResponse(false, "Tool ID is required");
            }
            
            $tool_id = intval($_POST['tool_id']);
            $tool_name = $conn->real_escape_string($_POST['tool_name']);
            $description = $conn->real_escape_string($_POST['description']);
            
            $sql = "UPDATE farm_tools SET 
                    tool_name = '$tool_name', 
                    description = '$description',
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE tool_id = $tool_id";
            
            if ($conn->query($sql)) {
                if ($conn->affected_rows > 0) {
                    sendResponse(true, 'Farm tool updated successfully');
                } else {
                    sendResponse(false, 'No changes made or tool not found');
                }
            } else {
                throw new Exception('Error updating farm tool: ' . $conn->error);
            }
        }

        // Delete farm tool
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            if (empty($_POST['tool_id'])) {
                sendResponse(false, "Tool ID is required");
            }
            
            $tool_id = intval($_POST['tool_id']);
            
            $sql = "DELETE FROM farm_tools WHERE tool_id = $tool_id";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Farm tool deleted successfully');
            } else {
                throw new Exception('Error deleting farm tool: ' . $conn->error);
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