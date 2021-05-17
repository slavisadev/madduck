<?php

namespace Tests\Unit;

use App\Exceptions\IncompatibleShopProductCombination;
use App\Models\CustomerData;
use App\Models\ProductTypes\Cigarettes;
use App\Models\ProductTypes\Food;
use App\Models\ProductTypes\Medicine;
use App\Models\ProductTypes\ParkingTicket;
use App\Models\Selling\Item;
use App\Models\ShopTypes\CornerShop;
use App\Models\ShopTypes\Pharmacy;
use App\Models\ShopTypes\SuperMarket;
use App\Models\Store;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_adding_medicine_to_not_pharmacy()
    {
        $store = new Store();
        $cornerShop = new CornerShop('InTheNeighbourhood', $store);
        $productMedicine = new Medicine('Panthenol', 20, 100, uniqid());
        $this->expectException(IncompatibleShopProductCombination::class);
        $cornerShop->addProduct($productMedicine);
    }

    public function test_adding_medicine_to_pharmacy()
    {
        $store = new Store();
        $pharmacy = new Pharmacy('Hemopharm', $store);
        $productMedicine = new Medicine('Panthenol', 20, 100, uniqid());
        $addedProducts = $pharmacy->addProduct($productMedicine);
        $this->assertTrue(count($addedProducts) > 0);
    }

    public function test_adding_cigarettes_to_not_corner_shop()
    {
        $store = new Store();
        $pharmacy = new Pharmacy('Hemopharm', $store);
        $cigarettes = new Cigarettes('Dunhill', 20, 100);
        $this->expectException(IncompatibleShopProductCombination::class);
        $pharmacy->addProduct($cigarettes);
    }

    public function test_adding_cigarettes_to_corner_shop()
    {
        $store = new Store();
        $cornerShop = new CornerShop('InTheNeighbourhood', $store);
        $cigarettes = new Cigarettes('Dunhill', 20, 100);
        $addedProducts = $cornerShop->addProduct($cigarettes);
        $this->assertTrue(count($addedProducts) > 0);
    }

    public function test_ultimate_check()
    {
        $customerData = new CustomerData('Slavisa', 'Perisic', '+38111555333');
        $store = new Store();

        /**
         * CORNER SHOP
         */
        $cornerShop = new CornerShop('InTheNeighbourhood', $store);
        $productFood = new Food('Donuts', 15, 10);
        $productCig = new Cigarettes('Dunhill', 50, 10);
        $cornerShop->addProduct($productFood);
        $cornerShop->addProduct($productCig);

        /**
         * PHARMACY
         */
        $pharmacy = new Pharmacy('ICN Galenika', $store);
        $productMedicine1 = new Medicine('Panthenol', 25, 100, uniqid());
        $productMedicine2 = new Medicine('Zoloft', 20, 100, uniqid());
        $pharmacy->addProduct($productMedicine1);
        $pharmacy->addProduct($productMedicine2);

        /**
         * SUPER MARKET
         */
        $superMarket = new SuperMarket('IDEA', $store);
        $productParkingTicket1 = new ParkingTicket('Zone1', 1000, 5, uniqid());
        $productParkingTicket2 = new ParkingTicket('Zone2', 1000, 5, uniqid());
        $superMarket->addProduct($productParkingTicket1);
        $superMarket->addProduct($productParkingTicket2);

        /**
         * CREATE BILLS WITH TWO PRODUCTS FOR EACH STORE
         */
        $item1 = new Item($productFood, 5);
        $item2 = new Item($productCig, 5);
        $billCornerShop1 = $cornerShop->createBill($customerData);
        $billCornerShop1->addItem($item1);
        $billCornerShop1->addItem($item2);
        $this->assertTrue(count($billCornerShop1->getItems()) === 2);

        $item1 = new Item($productMedicine1, 5);
        $item2 = new Item($productMedicine2, 5);
        $billPharmacy1 = $pharmacy->createBill($customerData);
        $billPharmacy1->addItem($item1);
        $billPharmacy1->addItem($item2);
        $this->assertTrue(count($billPharmacy1->getItems()) === 2);

        $item1 = new Item($productParkingTicket1, 5);
        $item2 = new Item($productParkingTicket2, 5);
        $billSuperMarket1 = $superMarket->createBill($customerData);
        $billSuperMarket1->addItem($item1);
        $billSuperMarket1->addItem($item2);
        $this->assertTrue(count($billSuperMarket1->getItems()) === 2);

        /**
         * CREATE BILLS WITH TWO PRODUCTS FOR EACH STORE, WITH AMOUNT BIGGER THAN AVAILABLE QUANTITY
         */
        $item1 = new Item($productFood, 500);
        $item2 = new Item($productCig, 500);
        $billCornerShop2 = $cornerShop->createBill($customerData);
        $billCornerShop2->addItem($item1);
        $billCornerShop2->addItem($item2);
        $this->assertTrue(count($billCornerShop2->getItems()) === 2);

        $item1 = new Item($productMedicine1, 500);
        $item2 = new Item($productMedicine2, 500);
        $billPharmacy2 = $pharmacy->createBill($customerData);
        $billPharmacy2->addItem($item1);
        $billPharmacy2->addItem($item2);
        $this->assertTrue(count($billPharmacy2->getItems()) === 2);

        $item1 = new Item($productParkingTicket1, 500);
        $item2 = new Item($productParkingTicket2, 500);
        $billSuperMarket2 = $superMarket->createBill($customerData);
        $billSuperMarket2->addItem($item1);
        $billSuperMarket2->addItem($item2);
        $this->assertTrue(count($billSuperMarket2->getItems()) === 2);

        /**
         * CREATE BILLS FOR PRODUCTS WITHOUT SERIAL NUMBER AND MADE IN A PHARMACY
         */
        $this->expectException(\ArgumentCountError::class);
        $productMedicine3 = new Medicine('Panadol', 25, 100);
        $this->expectException(\ArgumentCountError::class);
        $productMedicine4 = new Medicine('Andol', 20, 100);
        $pharmacy->addProduct($productMedicine1);
        $pharmacy->addProduct($productMedicine2);
    }

    public function test_report_generation()
    {
        $customerData = new CustomerData('Slavisa', 'Perisic', '+38111555333');
        $store = new Store();

        /**
         * CORNER SHOP
         */
        $cornerShop = new CornerShop('InTheNeighbourhood', $store);
        $productFood = new Food('Donuts', 15, 10);
        $productCig = new Cigarettes('Dunhill', 50, 10);
        $cornerShop->addProduct($productFood);
        $cornerShop->addProduct($productCig);

        /**
         * PHARMACY
         */
        $pharmacy = new Pharmacy('ICN Galenika', $store);
        $productMedicine1 = new Medicine('Panthenol', 25, 100, uniqid());
        $productMedicine2 = new Medicine('Zoloft', 20, 100, uniqid());
        $pharmacy->addProduct($productMedicine1);
        $pharmacy->addProduct($productMedicine2);

        /**
         * SUPER MARKET
         */
        $superMarket = new SuperMarket('IDEA', $store);
        $productParkingTicket1 = new ParkingTicket('Zone1', 1000, 5, uniqid());
        $productParkingTicket2 = new ParkingTicket('Zone2', 1000, 5, uniqid());
        $superMarket->addProduct($productParkingTicket1);
        $superMarket->addProduct($productParkingTicket2);

        /**
         * CREATE BILLS WITH TWO PRODUCTS FOR EACH STORE
         */
        $item1 = new Item($productFood, 5);
        $item2 = new Item($productCig, 5);
        $billCornerShop1 = $cornerShop->createBill($customerData);
        $billCornerShop1->addItem($item1);
        $billCornerShop1->addItem($item2);

        $item1 = new Item($productMedicine1, 5);
        $item2 = new Item($productMedicine2, 5);
        $billPharmacy1 = $pharmacy->createBill($customerData);
        $billPharmacy1->addItem($item1);
        $billPharmacy1->addItem($item2);

        $item1 = new Item($productParkingTicket1, 5);
        $item2 = new Item($productParkingTicket2, 5);
        $billSuperMarket1 = $superMarket->createBill($customerData);
        $billSuperMarket1->addItem($item1);
        $billSuperMarket1->addItem($item2);

        /**
         * CREATE BILLS WITH TWO PRODUCTS FOR EACH STORE, WITH AMOUNT BIGGER THAN AVAILABLE QUANTITY
         */
        $item1 = new Item($productFood, 500);
        $item2 = new Item($productCig, 500);
        $billCornerShop2 = $cornerShop->createBill($customerData);
        $billCornerShop2->addItem($item1);
        $billCornerShop2->addItem($item2);

        $item1 = new Item($productMedicine1, 500);
        $item2 = new Item($productMedicine2, 500);
        $billPharmacy2 = $pharmacy->createBill($customerData);
        $billPharmacy2->addItem($item1);
        $billPharmacy2->addItem($item2);

        $item1 = new Item($productParkingTicket1, 500);
        $item2 = new Item($productParkingTicket2, 500);
        $billSuperMarket2 = $superMarket->createBill($customerData);
        $billSuperMarket2->addItem($item1);
        $billSuperMarket2->addItem($item2);

        /**
         * GENERATE REPORTS
         */
        $report = $cornerShop->generateReport(Carbon::now());
        $this->assertTrue(count($report) > 0);
    }
}
