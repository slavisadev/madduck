<?php

namespace App\Models\ProductTypes;

use App\Models\Product;

class ParkingTicket extends Product
{
    protected $serialNumber;

    public function __construct($name, $price, $qty, $serialNumber)
    {
        parent::__construct($name, $price, $qty);
        $this->serialNumber = $serialNumber;
    }

    public function getSerialNumber()
    {
        return $this->serialNumber;
    }
}
