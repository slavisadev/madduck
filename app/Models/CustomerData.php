<?php

namespace App\Models;

class CustomerData
{
    protected $firstName;
    protected $lastName;
    protected $telephone;

    /**
     * CustomerData constructor.
     *
     * @param $firstName
     * @param $lastName
     * @param $telephone
     */
    public function __construct($firstName, $lastName, $telephone)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->telephone = $telephone;
    }
}
