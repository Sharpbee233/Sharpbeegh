<?php
require_once __DIR__ . '/../config/database.php';
require_once 'session.php';

class Auth {
    private $conn;
    private $session;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->session = new Session();
    }

    public function register($schoolData, $adminData) {
        try {
            $this->conn->beginTransaction();

            // Insert school data
            $schoolQuery = "INSERT INTO schools (school_name, address, contact_number, email) 
                          VALUES (:name, :address, :contact, :email)";
            $schoolStmt = $this->conn->prepare($schoolQuery);
            $schoolStmt->execute([
                ':name' => $schoolData['name'],
                ':address' => $schoolData['address'],
                ':contact' => $schoolData['contact'],
                ':email' => $schoolData['email']
            ]);
            
            $schoolId = $this->conn->lastInsertId();

            // Hash password for admin
            $hashedPassword = password_hash($adminData['password'], PASSWORD_DEFAULT);

            // Insert admin data
            $adminQuery = "INSERT INTO users (school_id, username, password, first_name, last_name, 
                          email, role) VALUES (:school_id, :username, :password, :first_name, 
                          :last_name, :email, 'admin')";
            $adminStmt = $this->conn->prepare($adminQuery);
            $adminStmt->execute([
                ':school_id' => $schoolId,
                ':username' => $adminData['username'],
                ':password' => $hashedPassword,
                ':first_name' => $adminData['first_name'],
                ':last_name' => $adminData['last_name'],
                ':email' => $adminData['email']
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Registration Error: " . $e->getMessage());
            return false;
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT u.*, s.school_name 
                     FROM users u 
                     JOIN schools s ON u.school_id = s.school_id 
                     WHERE u.username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            if($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $user['password'])) {
                    $this->session->set('user_id', $user['user_id']);
                    $this->session->set('school_id', $user['school_id']);
                    $this->session->set('school_name', $user['school_name']);
                    $this->session->set('role', $user['role']);
                    $this->session->set('username', $user['username']);
                    $this->session->set('full_name', $user['first_name'] . ' ' . $user['last_name']);
                    return true;
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        return $this->session->destroy();
    }

    public function isLoggedIn() {
        return $this->session->exists('user_id');
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $this->session->get('user_id'),
                'school_id' => $this->session->get('school_id'),
                'school_name' => $this->session->get('school_name'),
                'role' => $this->session->get('role'),
                'username' => $this->session->get('username'),
                'full_name' => $this->session->get('full_name')
            ];
        }
        return null;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /index.php');
            exit();
        }
    }

    public function requireRole($role) {
        $this->requireLogin();
        if ($this->session->get('role') !== $role) {
            header('Location: /index.php');
            exit();
        }
    }
}
?>
