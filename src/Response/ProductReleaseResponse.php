<?php

namespace App\Response;

class ProductReleaseResponse
{
    public ?array $releasedProducts;
    public ?array $not_found;

    public function __construct(?array $releasedProducts, ?array $not_found)
    {
        $this->releasedProducts = $releasedProducts;
        $this->not_found = $not_found;
    }
}