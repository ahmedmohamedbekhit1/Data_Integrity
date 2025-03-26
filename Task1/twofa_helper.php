<?php
require_once 'RobThree/Auth/TwoFactorAuth.php';
require_once 'RobThree/Auth/Algorithm.php';
require_once 'RobThree/Auth/TwoFactorAuthException.php';
require_once 'RobThree/Auth/Providers/Qr/IQRCodeProvider.php';
require_once 'RobThree/Auth/Providers/Qr/BaseHTTPQRCodeProvider.php';
require_once 'RobThree/Auth/Providers/Qr/GoogleChartsQrCodeProvider.php';
require_once 'RobThree/Auth/Providers/Qr/ImageChartsQRCodeProvider.php';
require_once 'RobThree/Auth/Providers/Rng/IRNGProvider.php';
require_once 'RobThree/Auth/Providers/Rng/CSRNGProvider.php';
require_once 'RobThree/Auth/Providers/Rng/RNGException.php';
require_once 'RobThree/Auth/Providers/Time/TimeException.php';
require_once 'RobThree/Auth/Providers/Time/ITimeProvider.php';
require_once 'RobThree/Auth/Providers/Time/LocalMachineTimeProvider.php';
require_once 'RobThree/Auth/Providers/Time/NTPTimeProvider.php';
require_once 'RobThree/Auth/Providers/Time/HttpTimeProvider.php';


class TwoFAHelper {
    private $tfa;
    public function __construct() {
        try {
            $qrProvider = new RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider();
            $this->tfa = new RobThree\Auth\TwoFactorAuth(
                $qrProvider,
                'YourAppName',
                6,     
                30,   
                RobThree\Auth\Algorithm::Sha1
            );
        } catch (Exception $e) {
            $qrProvider = new RobThree\Auth\Providers\Qr\GoogleChartsQrCodeProvider();
            $this->tfa = new RobThree\Auth\TwoFactorAuth(
                $qrProvider,
                'YourAppName',
                6,
                30,
                RobThree\Auth\Algorithm::Sha1
            );
        }
    }
    
    public function generateSecret(): string {
        return $this->tfa->createSecret();
    }
    
    public function getQRCode(string $username, string $secret): array {
        try {
            $qrCode = $this->tfa->getQRCodeImageAsDataUri($username, $secret, 200); // 200px size
            $manualEntry = $this->tfa->getQRText($username, $secret);
            
            return [
                'success' => true,
                'qr_code' => $qrCode,
                'manual_entry' => $manualEntry
            ];
        } catch (Exception $e) {
            error_log('QR generation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'manual_entry' => $this->tfa->getQRText($username, $secret),
                'message' => 'QR code unavailable. Please use manual entry.'
            ];
        }
    }
    
    public function verifyCode(string $secret, string $code): bool {
        return $this->tfa->verifyCode($secret, $code);
    }
}
?>