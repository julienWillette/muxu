<?php

namespace App\Model;

/**
 *
 */
class ShippingManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'shipping';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }
}
