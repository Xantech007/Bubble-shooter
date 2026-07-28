<?php
class Database {
    private $host = "sql212.infinityfree.com";
    private $dbname = "if0_42518178_db";
    private $username = "if0_42518178";
    private $password = "C40I79wQxIvm";

    public function connect(){
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo;
        } catch(PDOException $e){
            die("Database connection failed: " . $e->getMessage());
        }
    }
}
?>
