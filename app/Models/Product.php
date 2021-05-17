<?php

namespace App\Models;

class Product
{
    /**
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $price;
    /**
     * @var
     */
    protected $qty;
    /**
     * @var
     */
    protected $shop;

    /**
     * Product constructor.
     *
     * @param $name
     * @param $price
     * @param $qty
     */
    public function __construct($name, $price, $qty)
    {
        $this->name = $name;
        $this->price = $price;
        $this->qty = $qty;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param $amount
     *
     * @return false
     */
    public function setQty($amount)
    {
        if ($amount > $this->qty) {
            return false;
        }

        $this->qty = $this->qty - $amount;
        return $this->qty;
    }

    /**
     * @param Shop $shop
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @return mixed
     */
    public function getShop()
    {
        return $this->shop;
    }
}
