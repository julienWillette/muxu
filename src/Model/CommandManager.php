<?php

namespace App\Model;

/**
 *
 */
class CommandManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'command';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $command): int
    {
        // prepared request : changer les param INT
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`total`, `created_at`, `user_id`,`invoice_id`, `shipping_id`) 
        VALUES 
        (:total, :created_at, :user_id, :invoice_id, :shipping_id)");
        $statement->bindValue('total', $command['total'], \PDO::PARAM_INT);
        $statement->bindValue('created_at', $command['created_at']);
        $statement->bindValue('user_id', $command['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('invoice_id', $command['invoice_id'], \PDO::PARAM_INT);
        $statement->bindValue('shipping_id', $command['shipping_id'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function selectAll(): array
    {
        $commands = $this->pdo->query("SELECT 
            command.id,
            command.total,
            command.created_at,
            command.user_id,
            command.invoice_id,
            user.email,
            user.firstname,
            user.lastname,
            user.address
            FROM command 
            INNER JOIN user ON user.id=command.user_id ORDER BY command.id DESC")->fetchAll();

        return $commands;
    }

    public function selectOneById(int $id)
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT 
            command.id,
            command.total,
            command.created_at,
            command.invoice_id,
            command.user_id,
            user.email,
            user.firstname,
            user.lastname,
            user.address,
            inv_pdt.quantity,
            inv_pdt.product_id
            FROM command 
            INNER JOIN user ON user.id=command.user_id
            INNER JOIN inv_pdt ON command.invoice_id=inv_pdt.invoice_id
            WHERE command.id=:id");

        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectAllProductsByCommand()
    {
        $invProducts = $this->pdo->query("SELECT 
            inv_pdt.product_id,
            inv_pdt.quantity,
            inv_pdt.price,
            command.invoice_id,
            command.user_id,
            product.name 
            FROM inv_pdt 
            INNER JOIN command ON inv_pdt.invoice_id=command.invoice_id
            INNER JOIN product ON product.id=inv_pdt.product_id
            WHERE inv_pdt.invoice_id=command.invoice_id")->fetchAll();

        return $invProducts;
    }
}
