<?php

namespace App\Classe;


use App\Entity\Category;

class SearchProduct
{
    /**
     * @var string
     */
    //nom du produit recherché
    public $productName = '';

    /**
     * @var Category[]
     */
    //categorie du produit recherché
    public $productCategories = [];
}