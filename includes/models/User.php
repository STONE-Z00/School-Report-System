<?php
/**
 * User Model
 */
require_once 'BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    /**
     * Create a new user
     */
    public function create($data) {
        $rules = [
            'username' => 'required',
            'password' => 'required',
            'full_name' => 'required',
            'role' => 'required'
        ];
        
        $errors = $this->validate($data, $rules);
        if (!empty($errors)) return ['success' => false, 'errors' => $errors];

        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (school_id, username, password, full_name, email, phone, role) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $data['school_id'] ?? null,
                $data['username'],
                $password,
                $data['full_name'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['role']
            ]);
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    /**
     * Update an existing user
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $fields[] = "password = ?";
                $values[] = password_hash($value, PASSWORD_DEFAULT);
            } else if (in_array($key, ['full_name', 'email', 'phone', 'role', 'status'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) return ['success' => false, 'errors' => ["No valid fields to update"]];

        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    /**
     * Get users by school
     */
    public function getBySchool($school_id) {
        $stmt = $this->pdo->prepare("SELECT id, username, full_name, role, status FROM users WHERE school_id = ?");
        $stmt->execute([$school_id]);
        return $stmt->fetchAll();
    }
}
