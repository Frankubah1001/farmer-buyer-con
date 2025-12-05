<?php
/**
 * Farm Tools Application Email Notifications
 * Sends email notifications to farmers when their farm tools application status changes
 */

require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send farm tools application approval email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $application_data Farm tools application details
 * @param string $admin_notes Optional admin notes
 * @return bool Success status
 */
function sendFarmToolsApprovalEmail($to_email, $to_name, $application_data, $admin_notes = '') {
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
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Farm Tools Program');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = '🎉 Farm Tools Application Approved - FarmerBuyerCon';
        $mail->Body = getFarmToolsApprovalTemplate($to_name, $application_data, $admin_notes);
        
        $mail->send();
        error_log("Farm tools approval email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Send farm tools application rejection email
 * @param string $to_email Farmer's email
 * @param string $to_name Farmer's name
 * @param array $application_data Farm tools application details
 * @param string $rejection_reason Reason for rejection
 * @return bool Success status
 */
function sendFarmToolsRejectionEmail($to_email, $to_name, $application_data, $rejection_reason) {
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
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Farm Tools Program');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = 'Farm Tools Application Update - FarmerBuyerCon';
        $mail->Body = getFarmToolsRejectionTemplate($to_name, $application_data, $rejection_reason);
        
        $mail->send();
        error_log("Farm tools rejection email sent to $to_email");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Email template for farm tools approval
 */
function getFarmToolsApprovalTemplate($farmer_name, $application_data, $admin_notes) {
    $application_id = $application_data['application_id'];
    $provider_name = $application_data['provider_name'];
    $tools_requested = $application_data['tools_requested'];
    $quantity_needed = $application_data['quantity_needed'];
    $applied_date = date('M d, Y', strtotime($application_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Farm Tools Application Approved</title>
  <style>
    body {
      margin: 0; padding: 0; background: #f4f7f9; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #1e40af, #2563eb); color: white; padding: 30px 20px; text-align: center;
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
      color: #1e40af; font-weight: 600;
    }
    .success-badge {
      display: inline-block; background: #2563eb; color: white; padding: 8px 16px;
      border-radius: 20px; font-size: 14px; font-weight: 600; margin: 10px 0;
    }
    .info-box {
      background: #f8f9fa; border-left: 4px solid #2563eb; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .info-box h3 {
      margin: 0 0 12px; color: #1e40af; font-size: 18px;
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
    .tools-box {
      background: linear-gradient(135deg, #dbeafe, #bfdbfe); 
      border: 2px solid #2563eb; padding: 25px;
      margin: 20px 0; border-radius: 12px;
    }
    .tools-box h3 {
      margin: 0 0 12px; color: #1e40af; font-size: 20px;
    }
    .next-steps {
      background: #fff7ed; border-left: 4px solid #f59e0b; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .next-steps h3 {
      margin: 0 0 12px; color: #d97706; font-size: 18px;
    }
    .btn {
      display: inline-block; background: #2563eb; color: white !important; text-decoration: none;
      font-weight: 600; font-size: 17px; padding: 14px 32px; border-radius: 8px; margin: 20px 0;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
      transition: all 0.3s ease;
    }
    .btn:hover {
      background: #1e40af; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
    }
    .footer {
      background: #f8f9fa; padding: 25px; text-align: center; font-size: 13px; color: #6c757d;
      border-top: 1px solid #e9ecef;
    }
    .footer a {
      color: #2563eb; text-decoration: none; font-weight: 500;
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
      <h1>🎉 Congratulations!</h1>
      <p>Your Farm Tools Application Has Been Approved</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>We are delighted to inform you that your farm tools application has been <strong>approved</strong>! This is a significant step towards improving your farming productivity, and we are excited to support you on this journey.</p>

      <div style="text-align: center;">
        <span class="success-badge">✓ APPLICATION APPROVED</span>
      </div>

      <!-- Tools Details -->
      <div class="tools-box">
        <h3>🛠️ Approved Farm Tools</h3>
        <p style="margin: 0; padding: 12px; background: white; border-radius: 6px; font-size: 15px; line-height: 1.6;">
          <strong>Tools:</strong> ' . nl2br(htmlspecialchars($tools_requested)) . '<br>
          <strong>Quantity:</strong> ' . htmlspecialchars($quantity_needed) . '
        </p>
      </div>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Application Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#FT' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Provider:</span>
          <span class="info-value">' . htmlspecialchars($provider_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Tools Requested:</span>
          <span class="info-value"><strong>' . htmlspecialchars(substr($tools_requested, 0, 50)) . '...</strong></span>
        </div>
        <div class="info-row">
          <span class="info-label">Quantity:</span>
          <span class="info-value">' . htmlspecialchars($quantity_needed) . '</span>
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

      <!-- Next Steps -->
      <div class="next-steps">
        <h3>📌 What Happens Next?</h3>
        <ol style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;">The provider (' . htmlspecialchars($provider_name) . ') will contact you within 5-7 business days</li>
          <li style="margin-bottom: 10px;">You will receive details about tool collection or delivery arrangements</li>
          <li style="margin-bottom: 10px;">Attend the mandatory equipment training session</li>
          <li style="margin-bottom: 10px;">Sign the tool usage agreement and maintenance guidelines</li>
          <li style="margin-bottom: 10px;">Receive your approved farm tools</li>
        </ol>
      </div>

      <p><strong>Important Reminders:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Keep your phone number active for communication</li>
        <li>Attend all required training sessions</li>
        <li>Follow proper maintenance procedures for the tools</li>
        <li>Use the tools responsibly and for farming purposes only</li>
        <li>Report any issues or damages immediately</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/farmersDashboard.php" class="btn">Go to Dashboard</a>
      </div>

      <p style="margin-top: 25px;">These tools will significantly boost your farm productivity! We believe in your potential and are committed to supporting your agricultural endeavors.</p>

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
 * Email template for farm tools rejection
 */
function getFarmToolsRejectionTemplate($farmer_name, $application_data, $rejection_reason) {
    $application_id = $application_data['application_id'];
    $provider_name = $application_data['provider_name'];
    $tools_requested = $application_data['tools_requested'];
    $quantity_needed = $application_data['quantity_needed'];
    $applied_date = date('M d, Y', strtotime($application_data['created_at']));
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Farm Tools Application Update</title>
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
      <h1>📋 Farm Tools Application Update</h1>
      <p>Status notification regarding your application</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>Thank you for your interest in obtaining farm tools through FarmerBuyerCon. After careful review of your application, we regret to inform you that we are unable to approve your request at this time.</p>

      <!-- Application Details -->
      <div class="info-box">
        <h3>📋 Application Details</h3>
        <div class="info-row">
          <span class="info-label">Application ID:</span>
          <span class="info-value">#FT' . str_pad($application_id, 6, '0', STR_PAD_LEFT) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Provider:</span>
          <span class="info-value">' . htmlspecialchars($provider_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Tools Requested:</span>
          <span class="info-value">' . htmlspecialchars(substr($tools_requested, 0, 50)) . '...</span>
        </div>
        <div class="info-row">
          <span class="info-label">Quantity:</span>
          <span class="info-value">' . htmlspecialchars($quantity_needed) . '</span>
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
        <h3>💡 Alternative Options & Tips</h3>
        <ul style="margin: 12px 0 0; padding-left: 20px; font-size: 15px;">
          <li style="margin-bottom: 10px;">Consider renting tools from local agricultural cooperatives</li>
          <li style="margin-bottom: 10px;">Join farmer groups to share equipment costs</li>
          <li style="margin-bottom: 10px;">Build a stronger farming track record by completing more orders</li>
          <li style="margin-bottom: 10px;">Maintain accurate farm documentation and records</li>
          <li style="margin-bottom: 10px;">Start with smaller, essential tools and gradually expand</li>
        </ul>
      </div>

      <p><strong>What You Can Do Next:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Review the reason provided and work on addressing the concerns</li>
        <li>You may reapply after 3 months with improved documentation</li>
        <li>Contact our support team for guidance on strengthening your application</li>
        <li>Explore tool rental options available in your area</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/apply_farm_tools.php" class="btn">Apply Again</a>
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
