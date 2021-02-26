<?php

namespace App\Classe;


use App\Entity\Category;

class SearchProduct
{
    /**
     * @var string
     */
    //recherche texte de mes utilisateurs
    public $productName = '';

    /**
     * @var Category[]
     */
    //categorie du produit recherché
    public $productCategories = [];
}