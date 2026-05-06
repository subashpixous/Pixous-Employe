<?php
class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRF()) {
                $error = 'Invalid security token. Please try again.';
            } else {
                $username = sanitize($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                // Brute force protection
                $attempts = $_SESSION['login_attempts'] ?? 0;
                $lockout  = $_SESSION['login_lockout'] ?? 0;

                if ($lockout > time()) {
                    $wait  = $lockout - time();
                    $error = "Too many attempts. Please wait {$wait} seconds.";
                } elseif (empty($username) || empty($password)) {
                    $error = 'Username and password are required.';
                } else {
                    $user = $this->userModel->findByUsername($username);

                    if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                        // Success — reset & set session
                        unset($_SESSION['login_attempts'], $_SESSION['login_lockout']);
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user']    = [
                            'id'        => $user['id'],
                            'username'  => $user['username'],
                            'full_name' => $user['full_name'],
                            'email'     => $user['email'],
                            'role'      => $user['role'],
                        ];

                        $this->userModel->updateLastLogin($user['id']);
                        logActivity(Database::getInstance()->getConnection(), 'Login', 'Auth', "User {$user['username']} logged in");
                        setFlash('success', 'Welcome Back!', 'You have successfully logged in.');
                        redirect('dashboard');
                    } else {
                        $attempts++;
                        $_SESSION['login_attempts'] = $attempts;
                        if ($attempts >= 5) {
                            $_SESSION['login_lockout'] = time() + 60;
                        }
                        $error = 'Invalid username or password.';
                    }
                }
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout(): void
    {
        logActivity(Database::getInstance()->getConnection(), 'Logout', 'Auth', 'User logged out');
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('auth/login');
    }
}
