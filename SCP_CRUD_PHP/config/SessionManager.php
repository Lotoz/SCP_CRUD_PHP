<?php

/**
 * SessionManager
 *
 * Lightweight session utility that centralizes secure session handling for the
 * application. Responsibilities include:
 * - starting the session with hardened cookie parameters
 * - enforcing inactivity timeout and periodic session ID regeneration
 * - generating and verifying a CSRF token stored in the session
 * - performing a secure logout that removes session data and cookies
 *
 * The comments below explain the rationale for each step as if describing the
 * code to a teammate who will maintain or audit session behavior.
 */
class SessionManager
{

    // Start a secure session and set safe cookie parameters before calling session_start().
    // This ensures cookies have attributes that mitigate XSS and CSRF risks.
    public static function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            // Configure cookie security attributes. Adjust 'domain' and 'secure'
            // according to the deployment environment (secure=true for HTTPS).
            session_set_cookie_params([
                'lifetime' => 7200,             // Two hour maximum lifetime for the cookie
                'path' => '/',
                'domain' => 'localhost',        // Change to your production domain when deployed
                'secure' => false,              // Should be true in production with HTTPS
                'httponly' => true,             // Prevent JavaScript access to the cookie (mitigates XSS)
                'samesite' => 'Strict'          // Tight same-site policy to reduce CSRF risk
            ]);
            session_start();
        }
    }

    // Enforce inactivity logout and periodically regenerate session IDs.
    // This reduces the risk of session fixation and limits exposure from stolen session IDs.
    public static function checkActivity()
    {
        // If we recorded a last activity time and the user has been idle longer
        // than the configured timeout (30 minutes), perform a logout and redirect.
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // More than 30 minutes of inactivity: clear session and force re-login
            self::logout();
            header("Location: login.php?msg=timeout");
            exit();
        }

        // Regenerate the session ID every 30 minutes to reduce the window for
        // session hijacking attacks. We track when the session was created and
        // call session_regenerate_id(true) when the age exceeds the threshold.
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }

        // Update last activity timestamp on each request that reaches this check.
        $_SESSION['last_activity'] = time();
    }

    // Generate or return the existing CSRF token for the session.
    // The token is cryptographically secure and should be embedded in forms.
    public static function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            // Use random_bytes for secure randomness and encode as hex for transport
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify a provided CSRF token against the one stored in session.
    // Returns true only for exact matches, using hash_equals to avoid timing attacks.
    public static function verifyCSRFToken($token)
    {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    // Perform a secure logout:
    // - clear session array, - delete the session cookie, - unset and destroy session data.
    // This helps ensure no residual session data remains on the server or client.
    public static function logout()
    {
        // Clear session array to remove all stored values
        $_SESSION = [];

        // Remove the session cookie from the client if cookies are used
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_unset();
        // Destroy the session data on the server
        session_destroy();
    }
}
