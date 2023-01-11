<?php
class Post{
    private $conn;
    private $table = 'posts';

    public $id;
    public $category_id;
    public $category_name;
    public $title;
    public $body;
    public $author;
    public $created_at;

    public function __construct() {
        include_once __DIR__ . '\..\config\Database.php';
        
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function read() {
        $query = 'SELECT
            c.name as category_name,
            p.id,
            p.category_id,
            p.title,
            p.body,
            p.author,
            p.created_at
        FROM
            ' . $this->table . ' p
        LEFT JOIN
            categories c ON p.category_id = c.id
        ORDER BY
            p.id ASC';
        
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function read_single() {
        $query = 'SELECT
            c.name as category_name,
            p.id,
            p.category_id,
            p.title,
            p.body,
            p.author,
            p.created_at
        FROM
            ' . $this->table . ' p
        LEFT JOIN
            categories c ON p.category_id = c.id
        WHERE
            p.id = ?
        LIMIT 0,1';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        return $stmt;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . '
            SET
                title = :title,
                body = :body,
                author = :author,
                category_id = :category_id';

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->body = htmlspecialchars(strip_tags($this->body));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':body', $this->body);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':category_id', $this->category_id);

        if($stmt->execute()){
            return true;
        }
        
        printf("Error: $s.\n", $stmt->error);

        return false;

    }

    public function update() {

        $query = 'UPDATE ' . $this->table . '
            SET
                title = :title,
                body = :body,
                author = :author,
                category_id = :category_id
            WHERE
                id = :id';

        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->body = htmlspecialchars(strip_tags($this->body));
        $this->author = htmlspecialchars(strip_tags($this->author));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':body', $this->body);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':category_id', $this->category_id);

        if(!$this->checkIfPostExists()) return false;

        if($stmt->execute()){
            return true;
        }
        
        printf("Error: $s.\n", $stmt->error);

        return false;

    }

    public function delete() {

        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $query = 'DELETE FROM ' . $this->table . '
        WHERE
        id = :id';
        
        $stmt = $this->conn->prepare($query);
        
        
        $stmt->bindParam(':id', $this->id);
        
        if(!$this->checkIfPostExists()) return false;

        if($stmt->execute()){
            return true;
        }
        
        printf("Error: $s.\n", $stmt->error);

        return false;
    }

    private function checkIfPostExists() {

        $query = 'SELECT id FROM ' . $this->table . '
                    WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }

        return false;
    }


}