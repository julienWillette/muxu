<?php

namespace App\Model;

/**
 *
 */
class HeaderManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'header';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $header
     * @return int
     */
    public function insert(array $header): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
         (`title`, `description`, `img_id`) VALUES (:title, :description, :img_id)");
        $statement->bindValue('title', $header['title'], \PDO::PARAM_STR);
        $statement->bindValue('description', $header['description'], \PDO::PARAM_STR);
        $statement->bindValue('img_id', $header['img_id'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    /**
     * @param array $header
     * @return bool
     */
    public function update(array $header):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
         " SET `title` = :title, `img_id`= :img_id,`description`= :description  WHERE id=:id");
        $statement->bindValue('id', $header['id'], \PDO::PARAM_INT);
        $statement->bindValue('title', $header['title'], \PDO::PARAM_STR);
        $statement->bindValue('description', $header['description'], \PDO::PARAM_STR);
        $statement->bindValue('img_id', $header['img_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function selectAll(): array
    {
        return $this->pdo->query("SELECT 
            header.id,
            header.title,
            header.description,
            header.img_id,
            img.url as url    
            FROM header 
            LEFT JOIN img ON header.img_id=img.id ORDER BY title ASC")->fetchAll();
    }

    public function selectOneById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT 
            header.id,
            header.title,
            header.description,
            header.img_id,
            img.url as url    
            FROM header 
            LEFT JOIN img ON header.img_id=img.id
            WHERE header.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectOneByIdImage(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM header WHERE img_id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
}
