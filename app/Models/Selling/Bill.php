<?php

namespace App\Models\Selling;

use App\Models\CustomerData;

class Bill
{
    protected $date;
    protected $customerData;
    protected $itemList = [];

    /**
     * Bill constructor.
     *
     * @param $date
     * @param CustomerData $customerData
     */
    public function __construct($date, CustomerData $customerData)
    {
        $this->date = $date;
        $this->customerData = $customerData;
    }

    /**
     * @param Item $item
     *
     * @return array
     */
    public function addItem(Item $item)
    {
        $this->itemList[] = $item;
        return $this->itemList;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->itemList;
    }

    /**
     * @return array
     */
    public function getDate()
    {
        return $this->date;
    }
}
