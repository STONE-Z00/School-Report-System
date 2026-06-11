<?php
/**
 * School Model
 */
require_once 'BaseModel.php';

class School extends BaseModel {
    protected $table = 'schools';

    public function create($data) {
        $rules = [
            'school_name' => 'required',
            'school_email' => 'required|email'
        ];
        
        $errors = $this->validate($data, $rules);
        if (!empty($errors)) return ['success' => false, 'errors' => $errors];

        try {
            $stmt = $this->pdo->prepare("INSERT INTO schools (school_name, school_address, school_phone, school_email) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['school_name'],
                $data['school_address'] ?? null,
                $data['school_phone'] ?? null,
                $data['school_email']
            ]);
            return ['success' => true, 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowed = ['school_name', 'school_address', 'school_phone', 'school_email', 'school_badge'];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) return ['success' => false, 'errors' => ["No valid fields to update"]];

        $values[] = $id;
        $sql = "UPDATE schools SET " . implode(', ', $fields) . " WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => [$e->getMessage()]];
        }
    }
}
