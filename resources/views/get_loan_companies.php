<?php
/**
 * Get Loan Companies API
 * Fetches active loan companies for the loan application form
 */

require_once '../DBcon.php';

header('Content-Type: application/json');

try {
    // Fetch only active loan companies
    $sql = "SELECT company_id, company_name, interest_rate, terms, contact_details 
            FROM loan_companies 
            WHERE status = 'Active' 
            ORDER BY company_name ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $companies = [];
    while ($row = $result->fetch_assoc()) {
        $companies[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $companies
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
