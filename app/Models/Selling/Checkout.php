<?php

namespace App\Models\Selling;

trait Checkout
{
    /**
     * @param Bill $bill
     *
     * @return array
     * @throws \Exception
     */
    public function execute(Bill $bill)
    {
        if (empty($bill->getItems())) {
            throw new \Exception('Your bill is empty');
        }

        $billedItems = [];

        foreach ($bill->getItems() as $item) {

            /** @var Item $item */
            $oldQty = $item->getProduct()->getQty();

            if ($newQty = $item->getProduct()->setQty($item->getAmount())) {
                $billedItems[] = [
                    'item'   => $item,
                    'oldQty' => $oldQty,
                    'newQty' => $newQty,
                    'date'   => $bill->getDate(),
                ];
            }
        }

        return $billedItems;
    }
}
