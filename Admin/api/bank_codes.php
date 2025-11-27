<?php
/**
 * Nigerian Bank Codes for Paystack
 * Updated list as of 2024
 */

function getNigerianBankCodes() {
    return [
        'Access Bank' => '044',
        'Access Bank (Diamond)' => '063',
        'ALAT by WEMA' => '035A',
        'ASO Savings and Loans' => '401',
        'Bowen Microfinance Bank' => '50931',
        'CEMCS Microfinance Bank' => '50823',
        'Citibank Nigeria' => '023',
        'Ecobank Nigeria' => '050',
        'Ekondo Microfinance Bank' => '562',
        'Fidelity Bank' => '070',
        'First Bank of Nigeria' => '011',
        'First City Monument Bank' => '214',
        'Globus Bank' => '00103',
        'Guaranty Trust Bank' => '058',
        'Heritage Bank' => '030',
        'Jaiz Bank' => '301',
        'Keystone Bank' => '082',
        'Kuda Bank' => '50211',
        'One Finance' => '565',
        'Parallex Bank' => '526',
        'Polaris Bank' => '076',
        'Providus Bank' => '101',
        'Rubies MFB' => '125',
        'Sparkle Microfinance Bank' => '51310',
        'Stanbic IBTC Bank' => '221',
        'Standard Chartered Bank' => '068',
        'Sterling Bank' => '232',
        'Suntrust Bank' => '100',
        'TAJ Bank' => '302',
        'Titan Trust Bank' => '102',
        'Union Bank of Nigeria' => '032',
        'United Bank For Africa' => '033',
        'Unity Bank' => '215',
        'VFD Microfinance Bank' => '566',
        'Wema Bank' => '035',
        'Zenith Bank' => '057'
    ];
}

/**
 * Get bank code by bank name
 */
function getBankCode($bankName) {
    $banks = getNigerianBankCodes();
    
    // Try exact match first
    if (isset($banks[$bankName])) {
        return $banks[$bankName];
    }
    
    // Try case-insensitive partial match
    $bankNameLower = strtolower($bankName);
    foreach ($banks as $name => $code) {
        if (stripos($name, $bankName) !== false || stripos($bankName, $name) !== false) {
            return $code;
        }
    }
    
    return null;
}

/**
 * Get bank name by bank code
 */
function getBankName($bankCode) {
    $banks = getNigerianBankCodes();
    $bankCode = strval($bankCode);
    
    foreach ($banks as $name => $code) {
        if ($code === $bankCode) {
            return $name;
        }
    }
    
    return null;
}

/**
 * Validate bank details
 */
function validateBankDetails($bankName, $accountNumber) {
    $errors = [];
    
    if (empty($bankName)) {
        $errors[] = 'Bank name is required';
    }
    
    if (empty($accountNumber)) {
        $errors[] = 'Account number is required';
    } elseif (!preg_match('/^\d{10}$/', $accountNumber)) {
        $errors[] = 'Account number must be exactly 10 digits';
    }
    
    $bankCode = getBankCode($bankName);
    if ($bankCode === null) {
        $errors[] = 'Bank not supported or not found: ' . $bankName;
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'bank_code' => $bankCode
    ];
}

?>
