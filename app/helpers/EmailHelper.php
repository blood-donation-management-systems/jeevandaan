<?php
require APP_ROOT . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailHelper {
    
    public static function sendOTP($toEmail, $toName, $otp) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            
            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'JeevanDaan - Password Reset OTP';
            $mail->Body = self::getOTPTemplate($toName, $otp);
            $mail->AltBody = "Your OTP for password reset is: $otp. Valid for 10 minutes.";
            
            $mail->send();
            return ['success' => true, 'message' => 'OTP sent successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to send email: ' . $mail->ErrorInfo];
        }
    }
    
    private static function getOTPTemplate($name, $otp) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { padding: 40px 30px; }
                .otp-box { background: #fff5f5; border: 2px dashed #dc3545; border-radius: 10px; padding: 25px; text-align: center; margin: 25px 0; }
                .otp-code { font-size: 42px; font-weight: bold; color: #dc3545; letter-spacing: 10px; margin: 10px 0; }
                .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🩸 JeevanDaan</h1>
                    <p style="margin: 5px 0 0 0;">Gift of Life</p>
                </div>
                <div class="content">
                    <h2>Hello, ' . htmlspecialchars($name) . '!</h2>
                    <p>You requested to reset your password. Use the OTP below to verify your identity:</p>
                    
                    <div class="otp-box">
                        <p style="margin: 0; color: #666;">Your OTP Code</p>
                        <div class="otp-code">' . $otp . '</div>
                        <p style="margin: 0; color: #999; font-size: 14px;">Valid for 10 minutes</p>
                    </div>
                    
                    <div class="warning">
                        <strong>⚠️ Security Notice:</strong>
                        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                            <li>Never share this OTP with anyone</li>
                            <li>JeevanDaan team will never ask for your OTP</li>
                            <li>If you did not request this, please ignore this email</li>
                        </ul>
                    </div>
                    
                    <p>Need help? Contact us at <a href="mailto:support@jeevandaan.org.np" style="color: #dc3545;">support@jeevandaan.org.np</a></p>
                </div>
                <div class="footer">
                    <p>© ' . date('Y') . ' JeevanDaan - Blood Donation Nepal</p>
                    <p>Saving lives, one drop at a time. ❤️</p>
                </div>
            </div>
        </body>
        </html>';
    }
}
