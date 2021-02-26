<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="product_index")
     */
    public function index(ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
