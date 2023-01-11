<?php

class UserMigrations
{

    public function __construct() {
        include_once __DIR__ . Database.php;
        
        $database = new Database();
        $conn = $database->connect();
        initiateDatabase($conn);
    }

    public function initiateDatabase($database)
    {

        if (this->createUsertable($database)) {
            echo "Table users created successfully";
        }

        // Create system admin
        if (this->createUser('admin', 'admin', 'admin@test.com', true)) {
            echo "Admin user created successfully";
        }

        // Create system simple user
        if (this->createUser('user', 'user', 'user@test.com', false)) {
            echo "Simple user created successfully";
        }

    }

    private function createUsertable($database)
    {
        $query = 'CREATE TABLE IF NOT EXISTS users (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                password varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                isAdmin boolean NOT NULL,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id))';

        // Prepare statement
        $stmt = $database->prepare($query);

        // Execute query
        $stmt->execute();
    }

    private function createUser($name, $password, $email, $isAdmin)
    {
        $query = "INSERT INTO users (name, password, email, isAdmin) 
                    VALUES ($name, $password, $email, $isAdmin)"; 

        // Prepare statement
        $stmt = $database->prepare($query);

        // Bind the parametes
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':isAdmin', $isAdmin);

        // Execute query
        $stmt->execute();
    }
}
