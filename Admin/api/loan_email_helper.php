<?php
/**
 * Loan Application Email Notifications
 * Sends email notifications to farmers when their loan application status changes
 */

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send loan application approval email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $loan_data Loan application details
 * @param string $admin_notes Optional admin notes
 * @return bool Success status
 */
function sendLoanApprovalEmail($to_email, $to_name, $loan_data, $admin_notes = '') {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Validate credentials
        if (empty($mail->Username) || empty($mail->Password)) {
            error_log('SMTP credentials are missing in .env');
            return false;
        }
        
        // Debug (remove in production)
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer [$level]: $str");
        };
        
        // Email content
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Loan Services');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = '🎉 Loan Application Approved - FarmerBuyerCon';
        $mail->Body = getLoanApprovalTemplate($to_name, $loan_data, $admin_notes);
        
        $mail->send();
        error_log("Loan approval email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Send loan application rejection email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $loan_data Loan application details
 * @param string $rejection_reason Reason for rejection
 * @return bool Success status
 */
function sendLoanRejectionEmail($to_email, $to_name, $loan_data, $rejection_reason) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Validate credentials
        if (empty($mail->Username) || empty($mail->Password)) {
            error_log('SMTP credentials are missing in .env');
            return false;
        }
        
        // Debug (remove in production)
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer [$level]: $str");
        };
        
        // Email content
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Loan Services');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = 'Loan Application Update - FarmerBuyerCon';
        $mail->Body = getLoanRejectionTemplate($to_name, $loan_data, $rejection_reason);
        
        $mail->send();
        error_log("Loan rejection email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Email template for loan approval
 */
function getLoanApprovalTemplate($farmer_name, $loan_data, $admin_notes) {
    $application_id = $loan_data['application_id'];
    $loan_platform = $loan_data['loan_platform'];
    $loan_amount = number_format($loan_data['loan_amount'], 2);
    $repayment_period = $loan_data['repayment_period_months'];
    $bank_name = $loan_data['bank_name'];
    $account_number = $loan_data['account_number'];
    $account_name = $loan_data['account_name'];
    $applied_date = date('M d, Y', strtotime($loan_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Loan Application Approved</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #2f855a, #38a169); color: white; padding: 30px 20px; text-align: center;
    }
    .header h1 {
      margin: 0; font-size: 28px; font-weight: 600;
    }
    .header p {
      margin: 10px 0 0; font-size: 14px; opacity: 0.9;
    }
    .content {
      padding: 35px 40px; color: #333333; line-height: 1.7;
    }
    .content p {
      margin: 0 0 16px; font-size: 16px;
    }
    .highlight {
      color: #2f855a; font-weight: 600;
    }
    .success-badge {
      display: inline-block; background: #38a169; color: white; padding: 8px 16px;
      border-radius: 20px; font-size: 14px; font-weight: 600; margin: 10px 0;
    }
    .info-box {
      background: #f8f9fa; border-left: 4px solid #38a169; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .info-box h3 {
      margin: 0 0 12px; color: #2f855a; font-size: 18px;
    }
    .info-row {
      display: flex; margin-bottom: 10px; font-size: 15px;
    }
    .info-label {
      font-weight: 600; min-width: 180px; color: #555;
    }
    .info-value {
      color: #333;
    }
    .amount-box {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9); 
      border: 2px solid #38a169; padding: 25px;
      margin: 20px 0; border-radius: 12px; text-align: center;
    }
    .amount-box h2 {
      margin: 0 0 8px; color: #2f855a; font-size: 36px;
    }
    .amount-box p {
      margin: 0; color: #555; font-size: 14px;
    }
    .next-steps {
      background: #fff7ed; border-left: 4px solid #f59e0b; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .next-steps h3 {
      margin: 0 0 12px; color: #d97706; font-size: 18px;
    }
    .btn {
      display: inline-block; background: #38a169; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(56, 161, 105, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #2f855a; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(56, 161, 105, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #38a169; text-decoration: none; font-weight: 500;
    }
    .footer a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
      .content { padding: 25px 20px; }
      .header h1 { font-size: 24px; }
      .info-row { flex-direction: column; }
      .info-label { margin-bottom: 5px; }
      .amount-box h2 { font-size: 28px; }
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Header -->
    <div class="header">
      <h1>🎉 Congratulations!</h1>
      <p>Your Loan Application Has Been Approved</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>We are delighted to inform you that your loan application has been <strong>approved</strong>! This is a significant step towards growing your farming business, and we are excited to support you on this journey.</p>

      <div style="text-align: center;">
        <span class="success-badge">✓ APPLICATION APPROVED</span>
      </div>

      <!-- Loan Amount -->
      <div class="amount-box">
        <p style="margin-bottom: 5px; font-size: 14px; color: #666;">Approved Loan Amount</p>
        <h2>₦' . $loan_amount . '</h2>
        <p>Repayment Period: ' . $repayment_period . ' months</p>
      </div>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Application Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#LA' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Loan Platform:</span>
          <span class="info-value">' . htmlspecialchars($loan_platform) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Loan Amount:</span>
          <span class="info-value"><strong>₦' . $loan_amount . '</strong></span>
        </div>
        <div class="info-row">
          <span class="info-label">Repayment Period:</span>
          <span class="info-value">' . $repayment_period . ' months</span>
        </div>
        <div class="info-row">
          <span class="info-label">Applied Date:</span>
          <span class="info-value">' . $applied_date . '</span>
        </div>
      </div>

      <!-- Disbursement Details -->
      <div class="info-box">
        <h3>🏦 Disbursement Account</h3>
        <p style="margin-bottom: 12px; font-size: 14px; color: #666;">The loan will be disbursed to the following account:</p>
        <div class="info-row">
          <span class="info-label">Bank Name:</span>
          <span class="info-value">' . htmlspecialchars($bank_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Account Number:</span>
          <span class="info-value">' . htmlspecialchars($account_number) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Account Name:</span>
          <span class="info-value">' . htmlspecialchars($account_name) . '</span>
        </div>
      </div>

      ' . (!empty($admin_notes) ? '
      <!-- Admin Notes -->
      <div class="info-box">
        <h3>📝 Admin Notes</h3>
        <p style="margin: 0; padding: 12px; background: white; border-radius: 6px; font-size: 14px;">' 
          . nl2br(htmlspecialchars($admin_notes)) . 
        '</p>
      </div>' : '') . '

      <!-- Next Steps -->
      <div class="next-steps">
        <h3>📌 What Happens Next?</h3>
        <ol style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;">The loan platform (' . htmlspecialchars($loan_platform) . ') will contact you within 2-3 business days</li>
          <li style="margin-bottom: 10px;">You may be required to complete additional documentation</li>
          <li style="margin-bottom: 10px;">Once all requirements are met, the funds will be disbursed to your account</li>
          <li style="margin-bottom: 10px;">Keep track of your repayment schedule to maintain a good credit history</li>
        </ol>
      </div>

      <p><strong>Important Reminders:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Ensure your bank account details are correct</li>
        <li>Keep your phone number active for communication</li>
        <li>Use the loan for the stated purpose (farming activities)</li>
        <li>Plan your repayment to avoid penalties</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/farmersDashboard.php" class="btn">Go to Dashboard</a>
      </div>

      <p style="margin-top: 25px;">We believe in your potential and are committed to supporting your agricultural endeavors. This loan is an investment in your future success!</p>

      <p style="font-size: 14px; color: #666; margin-top: 25px;">
        <em>If you have any questions or need assistance, please contact our support team at support@farmerbuyercon.com or call our helpline.</em>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Empowering Farmers, Growing Communities</p>
      <p>
        Need help? <a href="mailto:support@farmerbuyercon.com">Contact Support</a> • 
        <a href="http://localhost/farmerBuyerCon/privacy.php">Privacy Policy</a>
      </p>
      <p>&copy; ' . date('Y') . ' FarmerBuyerCon. All rights reserved.</p>
    </div>

  </div>
</body>
</html>';
}

/**
 * Email template for loan rejection
 */
function getLoanRejectionTemplate($farmer_name, $loan_data, $rejection_reason) {
    $application_id = $loan_data['application_id'];
    $loan_platform = $loan_data['loan_platform'];
    $loan_amount = number_format($loan_data['loan_amount'], 2);
    $repayment_period = $loan_data['repayment_period_months'];
    $applied_date = date('M d, Y', strtotime($loan_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Loan Application Update</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #d97706, #f59e0b); color: white; padding: 30px 20px; text-align: center;
    }
    .header h1 {
      margin: 0; font-size: 28px; font-weight: 600;
    }
    .header p {
      margin: 10px 0 0; font-size: 14px; opacity: 0.9;
    }
    .content {
      padding: 35px 40px; color: #333333; line-height: 1.7;
    }
    .content p {
      margin: 0 0 16px; font-size: 16px;
    }
    .highlight {
      color: #d97706; font-weight: 600;
    }
    .info-box {
      background: #f8f9fa; border-left: 4px solid #f59e0b; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .info-box h3 {
      margin: 0 0 12px; color: #d97706; font-size: 18px;
    }
    .info-row {
      display: flex; margin-bottom: 10px; font-size: 15px;
    }
    .info-label {
      font-weight: 600; min-width: 180px; color: #555;
    }
    .info-value {
      color: #333;
    }
    .reason-box {
      background: #fff7ed; border: 2px solid #f59e0b; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .reason-box h3 {
      margin: 0 0 12px; color: #d97706; font-size: 18px;
    }
    .tips-box {
      background: #e8f5e9; border-left: 4px solid #38a169; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .tips-box h3 {
      margin: 0 0 12px; color: #2f855a; font-size: 18px;
    }
    .btn {
      display: inline-block; background: #f59e0b; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #d97706; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #f59e0b; text-decoration: none; font-weight: 500;
    }
    .footer a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
      .content { padding: 25px 20px; }
      .header h1 { font-size: 24px; }
      .info-row { flex-direction: column; }
      .info-label { margin-bottom: 5px; }
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Header -->
    <div class="header">
      <h1>📋 Loan Application Update</h1>
      <p>Status notification regarding your application</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>Thank you for your interest in obtaining a loan through FarmerBuyerCon. After careful review of your application, we regret to inform you that we are unable to approve your loan request at this time.</p>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Application Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#LA' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Loan Platform:</span>
          <span class="info-value">' . htmlspecialchars($loan_platform) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Requested Amount:</span>
          <span class="info-value">₦' . $loan_amount . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Repayment Period:</span>
          <span class="info-value">' . $repayment_period . ' months</span>
        </div>
        <div class="info-row">
          <span class="info-label">Applied Date:</span>
          <span class="info-value">' . $applied_date . '</span>
        </div>
      </div>

      <!-- Rejection Reason -->
      <div class="reason-box">
        <h3>📝 Reason for Decision</h3>
        <p style="margin: 0; padding: 12px; background: white; border-radius: 6px; font-size: 15px; line-height: 1.6;">' 
          . nl2br(htmlspecialchars($rejection_reason)) . 
        '</p>
      </div>

      <!-- Tips for Future Applications -->
      <div class="tips-box">
        <h3>💡 Tips for Future Applications</h3>
        <ul style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;">Build a strong farming track record by completing more orders successfully</li>
          <li style="margin-bottom: 10px;">Maintain accurate and up-to-date business documentation</li>
          <li style="margin-bottom: 10px;">Ensure all required documents are submitted with your application</li>
          <li style="margin-bottom: 10px;">Consider starting with a smaller loan amount to build credit history</li>
          <li style="margin-bottom: 10px;">Improve your farm\'s financial records and profitability metrics</li>
        </ul>
      </div>

      <p><strong>What You Can Do Next:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Review the reason provided and work on addressing the concerns</li>
        <li>You may reapply after 3 months with improved documentation</li>
        <li>Contact our support team for guidance on strengthening your application</li>
        <li>Explore alternative financing options available on our platform</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/apply_loan.php" class="btn">Apply Again</a>
      </div>

      <p style="margin-top: 25px;">We understand this may be disappointing, but we encourage you to continue building your farming business. We are here to support you in other ways, and we look forward to considering your future applications.</p>

      <p style="font-size: 14px; color: #666; margin-top: 25px;">
        <em>If you have questions about this decision or need assistance with future applications, please contact our support team at support@farmerbuyercon.com</em>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Empowering Farmers, Growing Communities</p>
      <p>
        Need help? <a href="mailto:support@farmerbuyercon.com">Contact Support</a> • 
        <a href="http://localhost/farmerBuyerCon/privacy.php">Privacy Policy</a>
      </p>
      <p>&copy; ' . date('Y') . ' FarmerBuyerCon. All rights reserved.</p>
    </div>

  </div>
</body>
</html>';
}
?>
