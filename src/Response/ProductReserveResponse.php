<?php

namespace App\Response;

class ProductReserveResponse
{
    public ?array $reservedProducts;
    public ?array $not_found;
    public ?array $out_of_stock;

    public function __construct(?array $reservedProducts, ?array $not_found, ?array $out_of_stock)
    {
        $this->reservedProducts = $reservedProducts;
        $this->not_found = $not_found;
        $this->out_of_stock = $out_of_stock;
    }
}