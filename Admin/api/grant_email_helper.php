<?php
/**
 * Grant Application Email Notifications
 * Sends email notifications to farmers when their grant application status changes
 */

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send grant application approval email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $application_data Grant application details
 * @param string $admin_notes Optional admin notes
 * @return bool Success status
 */
function sendGrantApprovalEmail($to_email, $to_name, $application_data, $admin_notes = '') {
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
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Grant Services');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = '🎊 Grant Application Approved - FarmerBuyerCon';
        $mail->Body = getGrantApprovalTemplate($to_name, $application_data, $admin_notes);
        
        $mail->send();
        error_log("Grant approval email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Send grant application rejection email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $application_data Grant application details
 * @param string $rejection_reason Reason for rejection
 * @return bool Success status
 */
function sendGrantRejectionEmail($to_email, $to_name, $application_data, $rejection_reason) {
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
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Grant Services');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = 'Grant Application Update - FarmerBuyerCon';
        $mail->Body = getGrantRejectionTemplate($to_name, $application_data, $rejection_reason);
        
        $mail->send();
        error_log("Grant rejection email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Email template for grant approval
 */
function getGrantApprovalTemplate($farmer_name, $application_data, $admin_notes) {
    $application_id = $application_data['application_id'];
    $provider_name = $application_data['provider_name'];
    $grant_type = $application_data['grant_type'];
    $amount_requested = number_format($application_data['amount_requested'], 2);
    $purpose = $application_data['purpose'];
    $applied_date = date('M d, Y', strtotime($application_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Grant Application Approved</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #6d28d9, #7c3aed); color: white; padding: 30px 20px; text-align: center;
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
      color: #6d28d9; font-weight: 600;
    }
    .success-badge {
      display: inline-block; background: #7c3aed; color: white; padding: 8px 16px;
      border-radius: 20px; font-size: 14px; font-weight: 600; margin: 10px 0;
    }
    .info-box {
      background: #f8f9fa; border-left: 4px solid #7c3aed; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .info-box h3 {
      margin: 0 0 12px; color: #6d28d9; font-size: 18px;
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
      background: linear-gradient(135deg, #ede9fe, #ddd6fe); 
      border: 2px solid #7c3aed; padding: 25px;
      margin: 20px 0; border-radius: 12px; text-align: center;
    }
    .amount-box h2 {
      margin: 0 0 8px; color: #6d28d9; font-size: 36px;
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
      display: inline-block; background: #7c3aed; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #6d28d9; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(124, 58, 237, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #7c3aed; text-decoration: none; font-weight: 500;
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
      <h1>🎊 Congratulations!</h1>
      <p>Your Grant Application Has Been Approved</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>We are thrilled to inform you that your grant application has been <strong>approved</strong>! This is a remarkable achievement and a testament to your dedication to agricultural excellence. We are honored to support your farming project.</p>

      <div style="text-align: center;">
        <span class="success-badge">✓ GRANT APPROVED</span>
      </div>

      <!-- Grant Amount -->
      <div class="amount-box">
        <p style="margin-bottom: 5px; font-size: 14px; color: #666;">Approved Grant Amount</p>
        <h2>₦' . $amount_requested . '</h2>
        <p>Grant Type: ' . htmlspecialchars($grant_type) . '</p>
      </div>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Grant Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#GA' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Provider:</span>
          <span class="info-value">' . htmlspecialchars($provider_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Grant Type:</span>
          <span class="info-value"><strong>' . htmlspecialchars($grant_type) . '</strong></span>
        </div>
        <div class="info-row">
          <span class="info-label">Amount:</span>
          <span class="info-value"><strong>₦' . $amount_requested . '</strong></span>
        </div>
        <div class="info-row">
          <span class="info-label">Purpose:</span>
          <span class="info-value">' . htmlspecialchars(substr($purpose, 0, 100)) . '...</span>
        </div>
        <div class="info-row">
          <span class="info-label">Applied Date:</span>
          <span class="info-value">' . $applied_date . '</span>
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

      <!-- Disbursement Timeline -->
      <div class="info-box">
        <h3>⏱️ Disbursement Timeline</h3>
        <ol style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;"><strong>Verification (7-14 days):</strong> Provider will verify your details and documentation</li>
          <li style="margin-bottom: 10px;"><strong>Agreement Signing (3-5 days):</strong> You will receive and sign the grant agreement</li>
          <li style="margin-bottom: 10px;"><strong>Disbursement (21-30 days):</strong> Funds will be transferred to your account</li>
          <li style="margin-bottom: 10px;"><strong>Project Implementation (12 months):</strong> Execute your farming project</li>
        </ol>
      </div>

      <!-- Next Steps -->
      <div class="next-steps">
        <h3>📌 What Happens Next?</h3>
        <ol style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;">The grant provider will contact you within 5-7 business days</li>
          <li style="margin-bottom: 10px;">Prepare additional documentation if requested</li>
          <li style="margin-bottom: 10px;">Attend the mandatory grant orientation session</li>
          <li style="margin-bottom: 10px;">Sign the grant agreement and terms</li>
          <li style="margin-bottom: 10px;">Receive funds and begin project implementation</li>
          <li style="margin-bottom: 10px;">Submit quarterly progress reports</li>
        </ol>
      </div>

      <p><strong>Grant Conditions & Requirements:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Use funds strictly for the stated agricultural purpose</li>
        <li>Maintain detailed financial records of grant utilization</li>
        <li>Submit quarterly progress reports to the provider</li>
        <li>Complete the project within the specified timeline (12 months)</li>
        <li>Participate in monitoring and evaluation activities</li>
        <li>Acknowledge the grant provider in project communications</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/farmersDashboard.php" class="btn">Go to Dashboard</a>
      </div>

      <p style="margin-top: 25px;">This grant is an investment in your agricultural success and the future of farming in our community. We believe in your vision and are excited to see your project come to life!</p>

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
 * Email template for grant rejection
 */
function getGrantRejectionTemplate($farmer_name, $application_data, $rejection_reason) {
    $application_id = $application_data['application_id'];
    $provider_name = $application_data['provider_name'];
    $grant_type = $application_data['grant_type'];
    $amount_requested = number_format($application_data['amount_requested'], 2);
    $applied_date = date('M d, Y', strtotime($application_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Grant Application Update</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #0f766e, #14b8a6); color: white; padding: 30px 20px; text-align: center;
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
      color: #0f766e; font-weight: 600;
    }
    .info-box {
      background: #f8f9fa; border-left: 4px solid #14b8a6; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .info-box h3 {
      margin: 0 0 12px; color: #0f766e; font-size: 18px;
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
      background: #f0fdfa; border: 2px solid #14b8a6; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .reason-box h3 {
      margin: 0 0 12px; color: #0f766e; font-size: 18px;
    }
    .tips-box {
      background: #e8f5e9; border-left: 4px solid #38a169; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .tips-box h3 {
      margin: 0 0 12px; color: #2f855a; font-size: 18px;
    }
    .btn {
      display: inline-block; background: #14b8a6; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #0f766e; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(20, 184, 166, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #14b8a6; text-decoration: none; font-weight: 500;
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
      <h1>📋 Grant Application Update</h1>
      <p>Status notification regarding your application</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>Thank you for your interest in obtaining a grant through FarmerBuyerCon. After thorough evaluation of your application, we regret to inform you that we are unable to approve your grant request at this time.</p>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Application Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#GA' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Provider:</span>
          <span class="info-value">' . htmlspecialchars($provider_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Grant Type:</span>
          <span class="info-value">' . htmlspecialchars($grant_type) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Requested Amount:</span>
          <span class="info-value">₦' . $amount_requested . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Applied Date:</span>
          <span class="info-value">' . $applied_date . '</span>
        </div>
      </div>

      <!-- Rejection Reason -->
      <div class="reason-box">
        <h3>📝 Evaluation Feedback</h3>
        <p style="margin: 0; padding: 12px; background: white; border-radius: 6px; font-size: 15px; line-height: 1.6;">' 
          . nl2br(htmlspecialchars($rejection_reason)) . 
        '</p>
      </div>

      <!-- Tips for Future Applications -->
      <div class="tips-box">
        <h3>💡 Grant Writing Tips & Improvement Areas</h3>
        <ul style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;"><strong>Strengthen Your Business Plan:</strong> Provide detailed financial projections and clear implementation strategies</li>
          <li style="margin-bottom: 10px;"><strong>Demonstrate Impact:</strong> Show how your project will benefit the community and agricultural sector</li>
          <li style="margin-bottom: 10px;"><strong>Improve Documentation:</strong> Ensure all required documents are complete and up-to-date</li>
          <li style="margin-bottom: 10px;"><strong>Build Track Record:</strong> Complete more farming projects successfully to demonstrate capability</li>
          <li style="margin-bottom: 10px;"><strong>Seek Mentorship:</strong> Connect with successful grant recipients for guidance</li>
        </ul>
      </div>

      <p><strong>Alternative Funding Options:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Explore microfinance opportunities for smaller amounts</li>
        <li>Consider crowdfunding for community-supported projects</li>
        <li>Look into agricultural cooperatives for shared resources</li>
        <li>Apply for training programs to strengthen your skills</li>
      </ul>

      <p><strong>What You Can Do Next:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Review the feedback and work on strengthening weak areas</li>
        <li>You may reapply after 6 months with an improved proposal</li>
        <li>Attend grant writing workshops to improve your application skills</li>
        <li>Contact our support team for personalized guidance</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/apply_grant.php" class="btn">Apply Again</a>
      </div>

      <p style="margin-top: 25px;">While this decision may be disappointing, we encourage you to view it as an opportunity for growth. Many successful farmers faced initial rejections before securing grants. We believe in your potential and look forward to reviewing your future applications.</p>

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
