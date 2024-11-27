<?php
 /**
     * Tung Duong.
     * tham khảo ngay đối với tôi
     */
class csrf_token
{
    private $token = '';
    private $secureName = 'csrf_secure'; // Tên session lưu token
    private $durationExpire = 3600; // Token hết hạn sau 1 giờ

    /**
     * CSRF constructor.
     */
    public function __construct()
    {
        // Kiểm tra HTTPS
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            die('HTTPS is required');
        }

        // Kiểm tra session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) {
            die('Session must be started');
        }

        if (empty($_SESSION[$this->secureName])) {
            $this->generateToken();
        } else {
            $this->token = $_SESSION[$this->secureName]['token'];
        }
    }

    /**
     * Tạo token mới và lưu vào SESSION
     */
    public function generateToken()
    {
        $this->token = bin2hex(random_bytes(32));
        $_SESSION[$this->secureName] = [
            'token' => $this->token,
            'expiry' => time() + $this->durationExpire
        ];
    }

    /**
     * Lấy token hiện tại
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Trả về thẻ input chứa token (dùng trong form HTML)
     *
     * @return string
     */
    public function getInput()
    {
        return '<input type="hidden" name="_token" value="' . $this->token . '">';
    }

    /**
     * Xác thực token từ yêu cầu POST
     *
     * @param string|null $token Token cần xác thực
     * @return bool
     */
    public function validate(?string $token = null)
    {
        if (empty($token) || empty($_SESSION[$this->secureName])) {
            return false;
        }

        $storedToken = $_SESSION[$this->secureName];

        return hash_equals($storedToken['token'], $token) && $storedToken['expiry'] > time();
    }
}