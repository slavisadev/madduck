<?php

namespace App\Models\Selling;

class Report
{
    /**
     * @var array
     */
    protected $items = [];
    /**
     * @var array
     */
    protected $reportItems = [];
    /**
     * @var
     */
    protected $date;

    /**
     * Report constructor.
     *
     * @param $items
     * @param $date
     */
    public function __construct($items, $date)
    {
        $this->items = $items;
        $this->date = $date;
    }

    public function saveItems()
    {
        foreach ($this->items as $item) {
            if ($item['date']->diffInDays($this->date) === 0) {
                $this->reportItems = $this->addItem($item);
            }
        }

        return $this->reportItems;
    }

    /**
     * @param $billedItem
     *
     * @return array|false
     */
    public function addItem($billedItem)
    {
        $item = $billedItem['item'];
        $shop = $item->getProduct()->getShop();

        if(!is_object($shop)) {
            return false;
        }

        return [
            'store_type'      => get_class($shop),
            'product_type'    => get_class($billedItem['item']->getProduct()),
            'product_price'   => $billedItem['item']->getPrice(),
            'amount_before'   => $billedItem['oldQty'],
            'amount_after'    => $billedItem['newQty'],
            'bill_created_at' => $billedItem['date']
        ];
    }
}
