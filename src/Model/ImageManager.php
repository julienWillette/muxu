<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 *
 */

namespace App\Model;

/**
 *
 */
class ImageManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'img';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $image
     * @return int
     */
    public function insert(array $image): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`url`, `artist_id`, `product_id`) 
        VALUES 
        (:url, :artist_id, :product_id)");
        $statement->bindValue('url', $image['url'], \PDO::PARAM_STR);
        $statement->bindValue('artist_id', $image['artist_id'], \PDO::PARAM_INT);
        $statement->bindValue('product_id', $image['product_id'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM " . self::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * @param array $image
     * @return bool
     */
    public function update(array $image):bool
    {
        // prepared request
        $statement = $this->pdo->prepare(
            "UPDATE " . self::TABLE ." SET 
            `url`=:url, 
            `artist_id`=:artist_id, 
            `product_id`=:product_id
            WHERE id=:id"
        );
        $statement->bindValue('id', $image['id'], \PDO::PARAM_INT);
        $statement->bindValue('url', $image['url'], \PDO::PARAM_STR);
        $statement->bindValue('artist_id', $image['artist_id'], \PDO::PARAM_INT);
        $statement->bindValue('product_id', $image['product_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function selectAll(): array
    {
        return $this->pdo->query("SELECT 
            img.id,
            img.url,
            product.name as product_name,
            img.product_id,
            artist.firstname as artist_firstname,
            artist.lastname as artist_lastname,
            img.artist_id    
            FROM img 
            LEFT JOIN artist ON img.artist_id=artist.id
            LEFT JOIN product ON img.product_id=product.id ORDER BY id DESC")->fetchAll();
    }

    public function selectOneById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT 
            img.id,
            img.url,
            product.name as product_name,
            img.product_id,
            artist.firstname as artist_firstname,
            artist.lastname as artist_lastname,
            img.artist_id    
            FROM img 
            LEFT JOIN artist ON img.artist_id=artist.id
            LEFT JOIN product ON img.product_id=product.id
            WHERE img.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
}
