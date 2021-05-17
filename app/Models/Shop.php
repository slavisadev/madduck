<?php

namespace App\Models;

use App\Exceptions\IncompatibleShopProductCombination;
use App\Models\Selling\Bill;
use App\Models\Selling\Checkout;
use App\Models\Selling\Report;
use Carbon\Carbon;

class Shop
{
    use Checkout;

    protected $name;
    protected $store;
    protected $products = [];
    protected $bills = [];
    protected $billedItems = [];
    protected $checkout;

    /**
     * Shop constructor.
     *
     * @param $name
     * @param Store $store
     */
    public function __construct($name, Store $store)
    {
        $this->name = $name;
        $this->store = $store;
    }

    /**
     * @param Product $product
     *
     * @return array
     * @throws \Exception
     */
    public function addProduct(Product $product)
    {
        if (
            (get_class($product) === 'App\\Models\\ProductTypes\\Medicine' && get_class($this) !== 'App\\Models\\ShopTypes\\Pharmacy')
            || (get_class($product) === 'App\\Models\\ProductTypes\\Cigarettes' && get_class($this) !== 'App\\Models\\ShopTypes\\CornerShop')
        ) {
            throw new IncompatibleShopProductCombination();
        }

        $product->setShop($this);
        $this->products[] = $product;
        return $this->products;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return get_class($this);
    }

    /**
     * @param CustomerData $customerData
     *
     * @return Bill
     */
    public function createBill(CustomerData $customerData)
    {
        $bill = new Bill(Carbon::now(), $customerData);
        $this->bills[] = $bill;
        return $bill;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function checkout()
    {
        if (empty($this->bills)) {
            throw new \Exception('You have no bills');
        }

        foreach ($this->bills as $bill) {
            $this->billedItems[] = $this->execute($bill);
        }

        return $this->billedItems;
    }

    /**
     * @param Carbon|null $date
     *
     * @return array
     *
     * @throws \Exception
     */
    public function generateReport(Carbon $date = null)
    {
        $reportItems = [];
        $items = $this->checkout();

        foreach ($items as $billedItems) {
            $report = new Report($billedItems, $date);
            $reportItems[] = $report->saveItems();
        }

        return $reportItems;
    }
}
