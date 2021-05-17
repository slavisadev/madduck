<?php

namespace App\Models\Selling;

use App\Models\Product;

class Item
{
    /**
     * @var Product
     */
    protected $product;
    /**
     * @var int
     */
    protected $amount;
    /**
     * This field exists in Product model, but it should be repeated because of price changes
     *
     * @var mixed
     */
    protected $price;
    /**
     * @var string
     */
    protected $serialNumber;

    /**
     * Item constructor.
     *
     * @param Product $product
     * @param int $amount
     */
    public function __construct(Product $product, int $amount)
    {
        $this->product = $product;
        $this->amount = $amount;
        $this->price = $product->getPrice();

        if (property_exists(get_class($product), 'serialNumber')) {
            $this->serialNumber = $product->getSerialNumber();
        }
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
