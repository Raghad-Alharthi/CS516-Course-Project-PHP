<?php
require_once __DIR__ . '/../config.php';

class User {
    public $user_id;
    public $first_name;
    public $last_name;
    public $username;
    public $password_hash;
    public $role;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function findById($id) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public static function findByUsername($username) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    // New: fetch all users with a specific role
    public static function allByRole($role) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE role = ?');
        $stmt->execute([$role]);
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function save() {
        $pdo = getPDO();
        if (isset($this->user_id)) {
            $stmt = $pdo->prepare(
                'UPDATE users SET first_name = ?, last_name = ?, username = ?, password_hash = ?, role = ? WHERE user_id = ?'
            );
            $stmt->execute([
                $this->first_name,
                $this->last_name,
                $this->username,
                $this->password_hash,
                $this->role,
                $this->user_id
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO users (first_name, last_name, username, password_hash, role) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $this->first_name,
                $this->last_name,
                $this->username,
                $this->password_hash,
                $this->role
            ]);
            $this->user_id = $pdo->lastInsertId();
        }
        return $this;
    }

    public function delete() {
        if (!isset($this->user_id)) return false;
        $pdo = getPDO();
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        return $stmt->execute([$this->user_id]);
    }
}
