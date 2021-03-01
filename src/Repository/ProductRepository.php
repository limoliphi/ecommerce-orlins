<?php

namespace App\Repository;

use App\Classe\SearchProduct;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requête qui permet de trouver un produit selon recherche de l'utilisateur
     * @return Product []
     */
    public function findBySearchProduct(SearchProduct $searchProduct)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->join('p.category', 'c');

        if (!empty($searchProduct->productCategories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $searchProduct->productCategories);
        }

        if (!empty($searchProduct->productName)) {
            $query = $query
                ->andWhere('p.name LIKE :productName')
                ->setParameter('productName', "%{$searchProduct->productName}%");
        }

        return $query->getQuery()->getResult();
    }
}
