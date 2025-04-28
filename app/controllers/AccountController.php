<?php
class AccountController
{
    // Show Login form or handle POST
    public function login()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
            $stmt->execute(['username' => $_POST['username']]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($_POST['password'], $user['password_hash'])) {
                $error = 'Invalid username or password';
            } else {
                // Set session claims
                $_SESSION['user_id']    = $user['user_id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name']  = $user['last_name'];
                $_SESSION['role']       = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'Admin':
                        header('Location: index.php?route=admin/dashboard');
                        break;
                    case 'Teacher':
                        header('Location: index.php?route=teacher/dashboard');
                        break;
                    case 'Student':
                        header('Location: index.php?route=student/dashboard');
                        break;
                    default:
                        header('Location: index.php?route=account/login');
                }
                exit;
            }
        }

        // Render the view (which itself includes header/footer)
        require __DIR__ . '/../views/account/login.php';
    }

    // Show Sign-Up form or handle POST
    public function register()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = getPDO();
            // Check username
            $check = $pdo->prepare('SELECT user_id FROM users WHERE username = :username');
            $check->execute(['username' => $_POST['username']]);
            if ($check->fetch()) {
                $error = 'Username is already taken. Please choose another one.';
            } else {
                $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $ins = $pdo->prepare('
                    INSERT INTO users
                      (first_name, last_name, username, password_hash, role)
                    VALUES
                      (:first_name, :last_name, :username, :password_hash, :role)
                ');
                $ins->execute([
                    'first_name'    => $_POST['firstName'],
                    'last_name'     => $_POST['lastName'],
                    'username'      => $_POST['username'],
                    'password_hash' => $hashed,
                    'role'          => $_POST['role'],
                ]);
                // On success, go to Login
                header('Location: index.php?route=account/login');
                exit;
            }
        }

        require __DIR__ . '/../views/account/register.php';
    }

    // Logout action
    public function logout()
    {
        session_destroy();
        header('Location: index.php?route=account/login');
        exit;
    }
}
