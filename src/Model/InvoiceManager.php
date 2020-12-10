<?php

namespace App\Model;

/**
 *
 */
class InvoiceManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'invoice';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $invoice): int
    {
        // prepared request : changer les param INT
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`created_at`, `user_id`) 
        VALUES 
        (:created_at, :user_id)");
        $statement->bindValue('created_at', $invoice['created_at']);
        $statement->bindValue('user_id', $invoice['user_id'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
}
