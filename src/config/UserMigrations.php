<?php

class UserMigrations {

    public function initiateDatabase($database) {
        this->createUsertable($database);
        // Create system admin
        this->createUser('admin', 'admin', 'admin@test.com', true);
        // Create system simple user
        this->createUser('user', 'user', 'user@test.com', false);
    }

    public function createUsertable($database) {
        $query = 'CREATE TABLE IF NOT EXISTS`users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `isAdmin` boolean NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            )';

        // Prepare statement
        $stmt = $database->prepare($query);

        // Execute query
        $stmt->execute();
    }

    public function createUser($name, $password, $email, $isAdmin) {
        $query = 'INSERT INTO `users`
                    SET
                        name = :name,
                        password = :password,
                        email = :email,
                        isAdmin = :isAdmin';
        
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