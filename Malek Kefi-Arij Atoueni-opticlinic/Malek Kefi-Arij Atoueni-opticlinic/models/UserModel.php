<?php
class UserModel
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Find user by ID
     * @param PDO $pdo
     * @param int $id
     * @return array|null
     */
    public static function findById($pdo, $id)
    {
        // Retrieve user data by ID
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); // Select user by ID
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find user by email
     * @param PDO $pdo
     * @param string $email
     * @return array|null
     */
    public static function findByEmail($pdo, $email)
    {
        // Retrieve user data by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?"); // Select user by email
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}
