<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class ProductManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'product';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $product
     * @return int
     */
    public function insert(array $product): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`name`, `description`, `category_id`, `artist_id`, `size_id`, `quantity`, `price`, `is_activated`) 
        VALUES 
        (:name, :description, :category_id, :artist_id, :size_id, :quantity, :price, :is_activated) ");
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_STR);
        $statement->bindValue('artist_id', $product['artist_id'], \PDO::PARAM_STR);
        $statement->bindValue('size_id', $product['size_id'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $product['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_STR);
        $statement->bindValue('is_activated', $product['is_activated'], \PDO::PARAM_BOOL);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    /**
     * @param array $product
     * @return bool
     */
    public function update(array $product):bool
    {

        // prepared request
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . " SET 
            `name` = :name, 
            `description` = :description, 
            `category_id` = :category_id,
            `artist_id` = :artist_id,
            `size_id` = :size_id,
            `quantity` = :quantity,
            `price` = :price,
            `is_activated` = :is_activated 
            WHERE id = :id"
        );
        $statement->bindValue('id', $product['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_STR);
        $statement->bindValue('artist_id', $product['artist_id'], \PDO::PARAM_STR);
        $statement->bindValue('size_id', $product['size_id'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $product['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_STR);
        $statement->bindValue('is_activated', $product['is_activated'], \PDO::PARAM_BOOL);

        return $statement->execute();
    }

    public function selectAll(): array
    {
        $products = $this->pdo->query("SELECT 
            product.id,
            product.name,
            product.description,
            product.quantity,
            product.price,
            product.is_activated,
            category.name as category_name,
            product.category_id,
            artist.firstname as artist_firstname,
            artist.lastname as artist_lastname,
            product.artist_id,
            size.format as size,
            product.size_id     
            FROM product 
            INNER JOIN category ON category.id=product.category_id
            INNER JOIN size ON size.id=product.size_id
            INNER JOIN artist ON artist.id=product.artist_id ORDER BY name ASC")->fetchAll();

        $result = [];
        foreach ($products as $product) {
            $statementImg = $this->pdo->prepare('SELECT url FROM img WHERE product_id=:product_id');
            $statementImg->bindValue('product_id', $product['id'], \PDO::PARAM_INT);
            $statementImg->execute();
            $images = $statementImg->fetchAll();
            $product['images'] = $images;
            array_push($result, $product);
        }

        return $result;
    }

    public function selectOneById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT 
            product.id,
            product.name,
            product.description,
            product.quantity,
            product.price,
            product.is_activated,
            category.name as category_name,
            product.category_id,
            artist.firstname as artist_firstname,
            artist.lastname as artist_lastname,
            product.artist_id,
            size.format as size,
            product.size_id,
            img.url as url       
            FROM product 
            INNER JOIN category ON product.category_id=category.id
            INNER JOIN size ON product.size_id=size.id
            INNER JOIN artist ON product.artist_id=artist.id
            LEFT JOIN img ON img.product_id=product.id
            WHERE product.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectAllImgByProduct($id)
    {
        $statement = $this->pdo->prepare("SELECT 
            img.url 
            FROM img 
            WHERE img.product_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchFull(string $term): array
    {
        $statement = $this->pdo->prepare("SELECT
        product.id,
        product.name,
        product.description,
        product.quantity,
        product.price,
        product.is_activated,
        category.name as category_name,
        product.category_id,
        artist.firstname as artist_firstname,
        artist.lastname as artist_lastname,
        product.artist_id,
        size.format as size,
        product.size_id     
        FROM product 
        INNER JOIN category ON category.id=product.category_id
        INNER JOIN size ON size.id=product.size_id
        INNER JOIN artist ON artist.id=product.artist_id
        WHERE product.name LIKE :search
        OR product.description LIKE :search
        OR artist.firstname LIKE :search
        OR artist.lastname LIKE :search
        OR size.format LIKE :search
        OR category.name LIKE :search");
        $statement->bindValue('search', '%' . $term . '%', \PDO::PARAM_STR);
        $statement->execute();
        $products = $statement->fetchAll();

        $result = [];
        foreach ($products as $product) {
            $statementImg = $this->pdo->prepare('SELECT url FROM img WHERE product_id=:product_id');
            $statementImg->bindValue('product_id', $product['id'], \PDO::PARAM_INT);
            $statementImg->execute();
            $images = $statementImg->fetchAll();
            $product['images'] = $images;
            array_push($result, $product);
        }

        return $result;
    }
        

    public function searchByCategory(int $categoryId): array
    {
        $statement = $this->pdo->prepare("SELECT
            product.id,
            product.name,
            product.description,
            product.quantity,
            product.price,
            product.is_activated,
            category.name as category_name,
            product.category_id,
            artist.firstname as artist_firstname,
            artist.lastname as artist_lastname,
            product.artist_id,
            size.format as size,
            product.size_id     
            FROM product 
            INNER JOIN category ON category.id=product.category_id
            INNER JOIN size ON size.id=product.size_id
            INNER JOIN artist ON artist.id=product.artist_id
            WHERE category.id =:category_id");
        $statement->bindValue('category_id', $categoryId, \PDO::PARAM_INT);
        $statement->execute();
        $products = $statement->fetchAll();

        $result = [];
        foreach ($products as $product) {
            $statementImg = $this->pdo->prepare('SELECT url FROM img WHERE product_id=:product_id');
            $statementImg->bindValue('product_id', $product['id'], \PDO::PARAM_INT);
            $statementImg->execute();
            $images = $statementImg->fetchAll();
            $product['images'] = $images;
            array_push($result, $product);
        }

        return $result;
    }

    /**
     * @param array $product
     * @return bool
     */
    public function updateQty(array $product):bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE . " SET 
            `quantity` = :quantity
            WHERE id = :id"
        );
        $statement->bindValue('id', $product['id'], \PDO::PARAM_INT);
        $statement->bindValue('quantity', $product['quantity'], \PDO::PARAM_INT);

        return $statement->execute();
    }
}
