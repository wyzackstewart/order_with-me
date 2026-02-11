<?php
class Product {
    private $conn;
    private $table = "products";

    public $product_id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $brand;
    public $stock_quantity;
    public $image_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add this method for search with pagination
    public function search($keywords = '', $category = '', $limit = null, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if(!empty($keywords)) {
            $query .= " AND (name LIKE :keywords OR description LIKE :keywords OR brand LIKE :keywords)";
        }
        
        if(!empty($category)) {
            $query .= " AND category = :category";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if(!empty($keywords)) {
            $keywords = "%{$keywords}%";
            $stmt->bindParam(":keywords", $keywords);
        }
        
        if(!empty($category)) {
            $stmt->bindParam(":category", $category);
        }
        
        if($limit !== null) {
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // Rest of your existing methods...
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                 (name, description, price, category, brand, stock_quantity, image_url) 
                 VALUES (:name, :description, :price, :category, :brand, :stock_quantity, :image_url)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":image_url", $this->image_url);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->product_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->category = $row['category'];
            $this->brand = $row['brand'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image_url = $row['image_url'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table . " 
                 SET name = :name, description = :description, price = :price, 
                     category = :category, brand = :brand, stock_quantity = :stock_quantity, 
                     image_url = :image_url 
                 WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":brand", $this->brand);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":product_id", $this->product_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>