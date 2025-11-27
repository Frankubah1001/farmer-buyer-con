<?php
/**
 * Paystack Configuration
 * 
 * Get your API keys from: https://dashboard.paystack.com/#/settings/developers
 */

// Paystack API Keys
define('PAYSTACK_SECRET_KEY', 'sk_test_0f8b9fa3f9c0b2825cf5148bba5e4426f2ec0d2f'); // Replace with your actual secret key
define('PAYSTACK_PUBLIC_KEY', 'pk_test_820ef67599b8a00fd2dbdde80e56e94fda5ed79f'); // Replace with your actual public key

// API Endpoints
define('PAYSTACK_BASE_URL', 'https://api.paystack.co');
define('PAYSTACK_TRANSFER_RECIPIENT_URL', PAYSTACK_BASE_URL . '/transferrecipient');
define('PAYSTACK_TRANSFER_URL', PAYSTACK_BASE_URL . '/transfer');
define('PAYSTACK_VERIFY_TRANSFER_URL', PAYSTACK_BASE_URL . '/transfer/verify/');
define('PAYSTACK_FINALIZE_TRANSFER_URL', PAYSTACK_BASE_URL . '/transfer/finalize_transfer');

// Transfer Settings
define('PAYSTACK_TRANSFER_REASON', 'Withdrawal payment from FarmerBuyerConnect');
define('PAYSTACK_CURRENCY', 'NGN'); // Nigerian Naira

/**
 * Make Paystack API Request
 */
function makePaystackRequest($url, $data = null, $method = 'POST') {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json',
        'Cache-Control: no-cache'
    ]);
    
    if ($method === 'POST' && $data !== null) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'success' => false,
            'error' => 'cURL Error: ' . $error
        ];
    }
    
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return [
        'success' => $http_code >= 200 && $http_code < 300,
        'http_code' => $http_code,
        'data' => $result
    ];
}

/**
 * Create Transfer Recipient
 * This creates a recipient that can receive transfers
 */
function createTransferRecipient($accountNumber, $bankCode, $accountName) {
    $data = [
        'type' => 'nuban', // Nigerian Uniform Bank Account Number
        'name' => $accountName,
        'account_number' => $accountNumber,
        'bank_code' => $bankCode,
        'currency' => PAYSTACK_CURRENCY
    ];
    
    return makePaystackRequest(PAYSTACK_TRANSFER_RECIPIENT_URL, $data, 'POST');
}

/**
 * Initiate Transfer
 * Sends money to a recipient
 */
function initiateTransfer($recipientCode, $amount, $reason = null) {
    // Convert amount to kobo (Paystack uses kobo, 1 Naira = 100 kobo)
    $amountInKobo = intval($amount * 100);
    
    $data = [
        'source' => 'balance',
        'amount' => $amountInKobo,
        'recipient' => $recipientCode,
        'reason' => $reason ?? PAYSTACK_TRANSFER_REASON,
        'currency' => PAYSTACK_CURRENCY
    ];
    
    return makePaystackRequest(PAYSTACK_TRANSFER_URL, $data, 'POST');
}

/**
 * Verify Transfer
 * Check the status of a transfer
 */
function verifyTransfer($transferCode) {
    $url = PAYSTACK_VERIFY_TRANSFER_URL . $transferCode;
    return makePaystackRequest($url, null, 'GET');
}

/**
 * Get Bank List
 * Fetch list of Nigerian banks supported by Paystack
 */
function getPaystackBanks() {
    $url = PAYSTACK_BASE_URL . '/bank?currency=NGN';
    return makePaystackRequest($url, null, 'GET');
}

/**
 * Resolve Account Number
 * Verify account number and get account name
 */
function resolveAccountNumber($accountNumber, $bankCode) {
    $url = PAYSTACK_BASE_URL . '/bank/resolve?account_number=' . $accountNumber . '&bank_code=' . $bankCode;
    return makePaystackRequest($url, null, 'GET');
}

?>
