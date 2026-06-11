<?php
/**
 * Base Model Class
 */
abstract class BaseModel {
    protected $pdo;
    protected $table;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Find a record by ID
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all records
     */
    public function all($limit = 100, $offset = 0) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete a record
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Basic validation helper
     */
    protected function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') !== false && (!isset($data[$field]) || empty($data[$field]))) {
                $errors[] = ucfirst($field) . " is required.";
            }
            if (strpos($rule, 'email') !== false && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format for " . $field;
            }
        }
        return $errors;
    }
}
