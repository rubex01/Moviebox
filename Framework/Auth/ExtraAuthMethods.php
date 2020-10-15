<?php

namespace Framework\Auth;


trait ExtraAuthMethods
{

    public static function initialAuthCheck()
    {
        if (!isset($_COOKIE['token'])) return false;

        $deviceFingerPrint = self::$deviceFingerPrint;

        $getBrowserJson = json_encode($deviceFingerPrint['getBrowser']);

        $stmt = self::$database->prepare('SELECT * FROM user_sessions WHERE token = ? AND user_agent = ? AND get_browser_info = ? AND ip_address = ?');
        $stmt->bind_param("ssss", $_COOKIE['token'], $deviceFingerPrint['userAgent'], $getBrowserJson, $deviceFingerPrint['ipAddress']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $session = $result->fetch_assoc();
            if ($session['valid_until'] > date('Y-m-d H:i:s')) {

                $userData = self::getUserData('user_id', $session['user_id']);
                unset($userData['password']);

                self::$user = $userData;
                self::$id = $userData['user_id'];

                return true;
            }
        }
        self::logout();
        return false;
    }

    private static function getUserData(string $column, string $selectItem)
    {
        $stmt = self::$database->prepare("SELECT * FROM users WHERE $column = ?");
        $stmt->bind_param("s", $selectItem);
        $stmt->execute();
        $userData = $stmt->get_result()->fetch_assoc();
        self::$passwordHash = $userData['password'];
        return $userData;
    }

    private static function generateLogin(array $userData)
    {
        $deviceFingerPrint = self::$deviceFingerPrint;

        $token = bin2hex(random_bytes(64));
        $getBrowserJson = json_encode($deviceFingerPrint['getBrowser']);
        $timestamp = date('Y-m-d H:i:s', strtotime("+3 month", strtotime(date("Y/m/d"))));

        $stmt = self::$database->prepare('INSERT INTO user_sessions (user_id, token, valid_until, user_agent, get_browser_info, browser, operating_system, device_type, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param("sssssssss",
            $userData['user_id'],
            $token,
            $timestamp,
            $deviceFingerPrint['userAgent'],
            $getBrowserJson,
            $deviceFingerPrint['getBrowser']['browser'],
            $deviceFingerPrint['getBrowser']['platform'],
            $deviceFingerPrint['getBrowser']['device_type'],
            $deviceFingerPrint['ipAddress']
        );
        $stmt->execute();

        setcookie('token', $token, time() + (86400 * 99), '/', getenv('DOMAIN'), getenv('SECURE'), getenv('HTTP_ONLY'));

        return true;
    }

    /**
     * Get device fingerprint
     *
     * @return array
     */
    private function getDeviceFingerprint()
    {
        return [
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'getBrowser' => get_browser(null, true),
            'ipAddress' => $_SERVER['REMOTE_ADDR']
        ];
    }

}