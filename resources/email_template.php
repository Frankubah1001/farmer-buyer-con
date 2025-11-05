<?php
// resources/email_template.php
function sendStyledEmail($to, $name, $subject, $bodyContent, $buttonText = null, $buttonLink = null) {
    $brandColor = '#2f855a';
    $buttonColor = '#38a169';

    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>' . htmlspecialchars($subject) . '</title>
      <style>
        body { margin:0; padding:0; background:#f4f7f9; font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif; }
        .container { max-width:600px; margin:30px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 8px 25px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg,' . $brandColor . ',' . $buttonColor . '); color:#fff; padding:30px 20px; text-align:center; }
        .header h1 { margin:0; font-size:28px; font-weight:600; }
        .content { padding:35px 40px; color:#333; line-height:1.7; }
        .content p { margin:0 0 16px; font-size:16px; }
        .highlight { color:' . $brandColor . '; font-weight:600; }
        .btn { display:inline-block; background:' . $buttonColor . '; color:#fff !important; text-decoration:none; font-weight:600; font-size:17px; padding:14px 32px; border-radius:8px; margin:20px 0; box-shadow:0 4px 12px rgba(56,161,105,0.3); transition:all .3s; }
        .btn:hover { background:' . $brandColor . '; transform:translateY(-2px); box-shadow:0 6px 16px rgba(56,161,105,0.4); }
        .footer { background:#f8f9fa; padding:25px; text-align:center; font-size:13px; color:#6c757d; border-top:1px solid #e9ecef; }
        .footer a { color:' . $buttonColor . '; text-decoration:none; font-weight:500; }
        .footer a:hover { text-decoration:underline; }
        @media (max-width:600px) { .content { padding:25px 20px; } .header h1 { font-size:24px; } }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="header"><h1>FarmerBuyerCon</h1></div>
        <div class="content">
          ' . $bodyContent . '
        </div>
        <div class="footer">
          <p><strong>FarmerBuyerCon</strong> – Fresh from Farm to You</p>
          <p><a href="mailto:support@farmerbuyercon.com">Contact Support</a> • <a href="http://localhost/farmerBuyerCon/privacy.php">Privacy</a></p>
          <p>&copy; ' . date('Y') . ' FarmerBuyerCon. All rights reserved.</p>
        </div>
      </div>
    </body>
    </html>';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($_ENV['SMTP_USER'], 'FarmerBuyerCon');
        $mail->addAddress($to, $name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>