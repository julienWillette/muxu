<?php

namespace App\Model;

/**
 *
 */
class SizeManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'size';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $size
     * @return int
     */
    public function insert(array $size): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`format`) VALUES (:format)");
        $statement->bindValue('format', $size['format'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }


    /**
     * @param array $size
     * @return bool
     */
    public function update(array $size):bool
    {

        // prepared request
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `format` = :format WHERE id=:id");
        $statement->bindValue('id', $size['id'], \PDO::PARAM_INT);
        $statement->bindValue('format', $size['format'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
