<?php

namespace App\Model;

/**
 *
 */
class InvPdtManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'inv_pdt';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $invPdt): int
    {
        // prepared request : changer les param INT
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . "
        (`quantity`, `price`, `product_id`, `invoice_id`) 
        VALUES 
        (:quantity, :price, :product_id, :invoice_id)");
        $statement->bindValue('quantity', $invPdt['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('price', $invPdt['price'], \PDO::PARAM_INT);
        $statement->bindValue('product_id', $invPdt['product_id'], \PDO::PARAM_INT);
        $statement->bindValue('invoice_id', $invPdt['invoice_id'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
    public function bestSeller()
    {
        $statement = $this->pdo->query("SELECT
            inv_pdt.product_id,
            SUM(inv_pdt.quantity) AS quantity,
            product.name
            FROM inv_pdt 
            INNER JOIN product ON product.id=inv_pdt.product_id
            GROUP BY inv_pdt.product_id
            ORDER BY quantity DESC
            LIMIT 3");
        return $statement->fetchAll();
    }
}
