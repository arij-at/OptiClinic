<?php
class Patient
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new patient record
     * @param PDO $pdo
     * @param string $name
     * @param string $dateOfBirth
     * @param string $email
     * @param string $phone
     * @param string $token
     * @return int
     */
    public static function create($pdo, $name, $dateOfBirth, $email, $phone, $token)
    {
        // Insert patient data into database
        $stmt = $pdo->prepare("INSERT INTO patients (name, date_of_birth, email, phone, token) VALUES (?, ?, ?, ?, ?)"); // Insert patient record
        $stmt->execute([$name, $dateOfBirth, $email, $phone, $token]);
        return $pdo->lastInsertId();
    }

    /**
     * Find patient by ID
     * @param PDO $pdo
     * @param int $id
     * @return array|null
     */
    public static function findById($pdo, $id)
    {
        // Retrieve patient data by ID
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?"); // Select patient by ID
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
