<?php
class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $password;
    public $email;
    public $isAdmin;

    public function __construct()
    {
        include_once __DIR__ . '\..\config\Database.php';

        $database = new Database();
        $this->conn = $database->connect();
    }

    public function read()
    {
        $query = 'SELECT
                        name,
                        id,
                        email,
                        isAdmin
                    FROM
                        ' . $this->table . '
                    ORDER BY
                        id ASC';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function read_single()
    {
        $query = 'SELECT
                        name,
                        id,
                        email,
                        isAdmin
                    FROM
                        ' . $this->table . '
                    WHERE
                        id = :id
                    LIMIT 0,1';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                name = :name,
                password = :password,
                email = :email,
                isAdmin = :isAdmin';

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->isAdmin = htmlspecialchars(strip_tags($this->isAdmin));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':isAdmin', $this->isAdmin);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: $s.\n", $stmt->error);

        return false;

    }

    public function update()
    {

        $query = 'UPDATE ' . $this->table . '
            SET
                name = :name,
                password = :password,
                email = :email,
                isAdmin = :isAdmin
            WHERE
                id = :id';

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->isAdmin = htmlspecialchars(strip_tags($this->isAdmin));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':isAdmin', $this->isAdmin);

        if (!$this->checkIfUsertExists()) {
            return false;
        }

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: $s.\n", $stmt->error);

        return false;

    }

    public function delete()
    {

        $this->id = htmlspecialchars(strip_tags($this->id));

        $query = 'DELETE FROM ' . $this->table . '
        WHERE
        id = :id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        if (!$this->checkIfUsertExists()) {
            return false;
        }

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: $s.\n", $stmt->error);

        return false;
    }

    public function read_single_by_name_and_password($userName, $userPassword)
    {
        $query = 'SELECT
                    name,
                    id,
                    email,
                    isAdmin
                FROM
                    ' . $this->table . '
                WHERE
                    name = :name AND
                    password = :password
                LIMIT 0,1';

// Prepare statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $userName);
        $stmt->bindParam(':password', $userPassword);

// Execute query
        $stmt->execute();

        return $stmt;
    }

    private function checkIfUsertExists()
    {

        $query = 'SELECT id FROM ' . $this->table . '
                    WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

}
