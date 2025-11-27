<?php
session_start();
require_once 'DBcon.php';

header('Content-Type: application/json');

// Check admin session
if (!isset($_SESSION['cbn_user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_farmer_withdrawals':
            getFarmerWithdrawals($conn);
            break;
        
        case 'approve':
            approveWithdrawal($conn);
            break;
        
        case 'reject':
            rejectWithdrawal($conn);
            break;
        
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getFarmerWithdrawals($conn) {
    $farmer_id = intval($_GET['farmer_id'] ?? 0);
    
    if ($farmer_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid farmer ID']);
        return;
    }
    
    $sql = "SELECT 
                withdrawal_id,
                amount,
                bank_name,
                account_number,
                account_name,
                status,
                DATE_FORMAT(request_date, '%Y-%m-%d %H:%i') as request_date
            FROM withdrawals
            WHERE user_id = ?
            ORDER BY request_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $farmer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $withdrawals = [];
    while ($row = $result->fetch_assoc()) {
        $withdrawals[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'data' => $withdrawals
    ]);
}

function approveWithdrawal($conn) {
    require_once 'paystack_config.php';
    require_once 'bank_codes.php';
    
    $withdrawal_id = intval($_POST['withdrawal_id'] ?? 0);
    $manual_process = isset($_POST['manual_process']) && $_POST['manual_process'] === 'true';
    
    if ($withdrawal_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid withdrawal ID']);
        return;
    }
    
    // Get withdrawal details
    $check_sql = "SELECT w.*, u.email, u.phone 
                  FROM withdrawals w
                  JOIN users u ON w.user_id = u.user_id
                  WHERE w.withdrawal_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $withdrawal_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Withdrawal not found']);
        $check_stmt->close();
        return;
    }
    
    $withdrawal = $check_result->fetch_assoc();
    $check_stmt->close();
    
    if ($withdrawal['status'] !== 'Pending') {
        echo json_encode(['success' => false, 'error' => 'Withdrawal has already been processed']);
        return;
    }
    
    $transferCode = null;
    $transferStatus = 'manual';
    $recipientCode = null;

    if (!$manual_process) {
        // Validate bank details
        $validation = validateBankDetails($withdrawal['bank_name'], $withdrawal['account_number']);
        
        if (!$validation['valid']) {
            echo json_encode([
                'success' => false, 
                'error' => 'Invalid bank details: ' . implode(', ', $validation['errors'])
            ]);
            return;
        }
        
        $bankCode = $validation['bank_code'];
        
        // Step 1: Create Transfer Recipient
        $recipientResponse = createTransferRecipient(
            $withdrawal['account_number'],
            $bankCode,
            $withdrawal['account_name']
        );
        
        if (!$recipientResponse['success']) {
            $errorMsg = $recipientResponse['data']['message'] ?? 'Failed to create transfer recipient';
            echo json_encode([
                'success' => false,
                'error' => 'Paystack Error: ' . $errorMsg,
                'can_manual_process' => true
            ]);
            return;
        }
        
        $recipientCode = $recipientResponse['data']['data']['recipient_code'] ?? null;
        
        if (!$recipientCode) {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to get recipient code from Paystack',
                'can_manual_process' => true
            ]);
            return;
        }
        
        // Step 2: Initiate Transfer
        $transferResponse = initiateTransfer(
            $recipientCode,
            $withdrawal['amount'],
            'Withdrawal payment - ID: ' . $withdrawal_id
        );
        
        if (!$transferResponse['success']) {
            $errorMsg = $transferResponse['data']['message'] ?? 'Failed to initiate transfer';
            echo json_encode([
                'success' => false,
                'error' => 'Paystack Transfer Error: ' . $errorMsg,
                'can_manual_process' => true
            ]);
            return;
        }
        
        $transferData = $transferResponse['data']['data'] ?? null;
        $transferCode = $transferData['transfer_code'] ?? null;
        $transferStatus = $transferData['status'] ?? 'unknown';
    }
    
    // Step 3: Update withdrawal status in database
    $sql = "UPDATE withdrawals 
            SET status = 'Approved', 
                processed_date = NOW(),
                transfer_code = ?,
                transfer_status = ?,
                recipient_code = ?
            WHERE withdrawal_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $transferCode, $transferStatus, $recipientCode, $withdrawal_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Return success with transfer details
        echo json_encode([
            'success' => true,
            'message' => $manual_process ? 'Withdrawal marked as paid manually.' : 'Withdrawal approved and payment initiated successfully!',
            'transfer_details' => [
                'transfer_code' => $transferCode,
                'status' => $transferStatus,
                'amount' => '₦' . number_format($withdrawal['amount'], 2),
                'recipient' => $withdrawal['account_name'],
                'bank' => $withdrawal['bank_name'],
                'account_number' => $withdrawal['account_number']
            ]
        ]);
    } else {
        $stmt->close();
        echo json_encode([
            'success' => false,
            'error' => 'Transfer initiated but failed to update database'
        ]);
    }
}

function rejectWithdrawal($conn) {
    $withdrawal_id = intval($_POST['withdrawal_id'] ?? 0);
    
    if ($withdrawal_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid withdrawal ID']);
        return;
    }
    
    // Check if withdrawal exists and is pending
    $check_sql = "SELECT status FROM withdrawals WHERE withdrawal_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('i', $withdrawal_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Withdrawal not found']);
        $check_stmt->close();
        return;
    }
    
    $current_status = $check_result->fetch_assoc()['status'];
    $check_stmt->close();
    
    if ($current_status !== 'Pending') {
        echo json_encode(['success' => false, 'error' => 'Withdrawal has already been processed']);
        return;
    }
    
    // Update withdrawal status to Rejected
    $sql = "UPDATE withdrawals 
            SET status = 'Rejected', 
                processed_date = NOW() 
            WHERE withdrawal_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $withdrawal_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Withdrawal rejected successfully']);
    } else {
        $stmt->close();
        echo json_encode(['success' => false, 'error' => 'Failed to reject withdrawal']);
    }
}
?>
