<?php
// email_helper.php - Email notification helper for reports
require_once __DIR__ . '/../../load_env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email notification when a report is resolved
 * @param string $to_email Recipient email
 * @param string $to_name Recipient name
 * @param string $recipient_type 'farmer' or 'buyer'
 * @param array $report_data Report details
 * @return bool Success status
 */
function sendReportResolvedEmail($to_email, $to_name, $recipient_type, $report_data) {
    $mail = new PHPMailer(true);
    
    try {
        // ----- SMTP CONFIG -----
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // ----- VALIDATE CREDENTIALS -----
        if (empty($mail->Username) || empty($mail->Password)) {
            error_log('SMTP credentials are missing in .env');
            return false;
        }
        
        // ----- DEBUG (remove in production) -----
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer [$level]: $str");
        };
        
        // ----- EMAIL CONTENT -----
        $mail->setFrom($mail->Username, 'FarmerBuyerCon Support');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        
        // Different subject and content based on recipient type
        if ($recipient_type === 'farmer') {
            $mail->Subject = 'Report Against You Has Been Resolved - FarmerBuyerCon';
            $mail->Body = getFarmerEmailTemplate($to_name, $report_data);
        } else {
            $mail->Subject = 'Your Report Has Been Resolved - FarmerBuyerCon';
            $mail->Body = getBuyerEmailTemplate($to_name, $report_data);
        }
        
        $mail->send();
        error_log("Report resolution email sent to $to_email ($recipient_type)");
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo} | Exception: {$e->getMessage()}");
        return false;
    }
}

/**
 * Email template for buyer (who filed the report)
 */
function getBuyerEmailTemplate($buyer_name, $report_data) {
    $report_id = $report_data['report_id'];
    $farmer_name = $report_data['farmer_name'];
    $issue_type = $report_data['issue_type'];
    $resolution_action = $report_data['resolution_action'];
    $resolution_notes = $report_data['resolution_notes'];
    $order_number = $report_data['order_number'] ?? 'N/A';
    $produce_name = $report_data['produce_name'] ?? 'N/A';
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Report Has Been Resolved</title>
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
      font-weight: 600; min-width: 150px; color: #555;
    }
    .info-value {
      color: #333;
    }
    .resolution-box {
      background: #e8f5e9; border: 2px solid #38a169; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .resolution-box h3 {
      margin: 0 0 12px; color: #2f855a; font-size: 18px;
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
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Header -->
    <div class="header">
      <h1>✓ Report Resolved</h1>
      <p>Your issue has been addressed</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($buyer_name) . '</span>,</p>

      <p>We are writing to inform you that your report <strong>#REP' . htmlspecialchars($report_id) . '</strong> has been reviewed and resolved by our admin team.</p>

      <!-- Report Details -->
      <div class="info-box">
        <h3>📋 Report Details</h3>
        <div class="info-row">
          <span class="info-label">Report ID:</span>
          <span class="info-value">#REP' . htmlspecialchars($report_id) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Farmer Reported:</span>
          <span class="info-value">' . htmlspecialchars($farmer_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Order Number:</span>
          <span class="info-value">' . htmlspecialchars($order_number) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Produce:</span>
          <span class="info-value">' . htmlspecialchars($produce_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Issue Type:</span>
          <span class="info-value">' . htmlspecialchars($issue_type) . '</span>
        </div>
      </div>

      <!-- Resolution Details -->
      <div class="resolution-box">
        <h3>✅ Resolution</h3>
        <div class="info-row">
          <span class="info-label">Action Taken:</span>
          <span class="info-value"><strong>' . htmlspecialchars($resolution_action) . '</strong></span>
        </div>
        ' . (!empty($resolution_notes) ? '
        <div style="margin-top: 15px;">
          <span class="info-label">Admin Notes:</span>
          <p style="margin: 8px 0 0; padding: 12px; background: white; border-radius: 6px; font-size: 14px;">' 
            . nl2br(htmlspecialchars($resolution_notes)) . 
          '</p>
        </div>' : '') . '
      </div>

      <p><strong>What happens next?</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>The resolution has been documented in our system</li>
        <li>You can view the full details in your report history</li>
        <li>If you have any concerns, please contact our support team</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/reports.php" class="btn">View My Reports</a>
      </div>

      <p style="margin-top: 20px;">Thank you for helping us maintain a trustworthy marketplace. Your feedback is valuable to us.</p>

      <p style="font-size: 14px; color: #666; margin-top: 25px;">
        <em>If you have any questions or need further assistance, please don\'t hesitate to contact our support team.</em>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
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
 * Email template for farmer (who was reported)
 */
function getFarmerEmailTemplate($farmer_name, $report_data) {
    $report_id = $report_data['report_id'];
    $buyer_name = $report_data['buyer_name'];
    $issue_type = $report_data['issue_type'];
    $resolution_action = $report_data['resolution_action'];
    $resolution_notes = $report_data['resolution_notes'];
    $order_number = $report_data['order_number'] ?? 'N/A';
    $produce_name = $report_data['produce_name'] ?? 'N/A';
    
    return '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Report Resolution Notification</title>
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
    .warning-box {
      background: #fff7ed; border-left: 4px solid #f59e0b; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .warning-box h3 {
      margin: 0 0 12px; color: #d97706; font-size: 18px;
    }
    .info-row {
      display: flex; margin-bottom: 10px; font-size: 15px;
    }
    .info-label {
      font-weight: 600; min-width: 150px; color: #555;
    }
    .info-value {
      color: #333;
    }
    .resolution-box {
      background: #e8f5e9; border: 2px solid #38a169; padding: 20px;
      margin: 20px 0; border-radius: 8px;
    }
    .resolution-box h3 {
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
      <h1>⚠ Report Resolution Notice</h1>
      <p>Action taken on a report filed against you</p>
    </div>

    <!-- Main Content -->
    <div class="content">
      <p>Dear <span class="highlight">' . htmlspecialchars($farmer_name) . '</span>,</p>

      <p>This is to inform you that a report filed against you by a buyer has been reviewed and resolved by our admin team.</p>

      <!-- Report Details -->
      <div class="warning-box">
        <h3>📋 Report Information</h3>
        <div class="info-row">
          <span class="info-label">Report ID:</span>
          <span class="info-value">#REP' . htmlspecialchars($report_id) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Reported By:</span>
          <span class="info-value">' . htmlspecialchars($buyer_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Order Number:</span>
          <span class="info-value">' . htmlspecialchars($order_number) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Produce:</span>
          <span class="info-value">' . htmlspecialchars($produce_name) . '</span>
        </div>
        <div class="info-row">
          <span class="info-label">Issue Reported:</span>
          <span class="info-value">' . htmlspecialchars($issue_type) . '</span>
        </div>
      </div>

      <!-- Resolution Details -->
      <div class="resolution-box">
        <h3>✅ Admin Resolution</h3>
        <div class="info-row">
          <span class="info-label">Action Taken:</span>
          <span class="info-value"><strong>' . htmlspecialchars($resolution_action) . '</strong></span>
        </div>
        ' . (!empty($resolution_notes) ? '
        <div style="margin-top: 15px;">
          <span class="info-label">Admin Notes:</span>
          <p style="margin: 8px 0 0; padding: 12px; background: white; border-radius: 6px; font-size: 14px;">' 
            . nl2br(htmlspecialchars($resolution_notes)) . 
          '</p>
        </div>' : '') . '
      </div>

      <p><strong>Important Reminders:</strong></p>
      <ul style="margin: 16px 0; padding-left: 20px;">
        <li>Please ensure all future orders are fulfilled accurately and on time</li>
        <li>Maintain high quality standards for all produce</li>
        <li>Communicate promptly with buyers regarding any issues</li>
        <li>Multiple reports may affect your account standing</li>
      </ul>

      <div style="text-align: center;">
        <a href="http://localhost/farmerBuyerCon/resources/dashboard.php" class="btn">Go to Dashboard</a>
      </div>

      <p style="margin-top: 20px;">We appreciate your cooperation in maintaining the quality and trust of our marketplace. If you have any questions about this resolution, please contact our support team.</p>

      <p style="font-size: 14px; color: #666; margin-top: 25px;">
        <em>This is an automated notification. For inquiries, please contact support@farmerbuyercon.com</em>
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
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
