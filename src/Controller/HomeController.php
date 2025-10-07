<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
   #[Route(path: '/', name: 'app_home')]
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): Response {
        $categories = $categoryRepository->findAll();
        $products = [];
        $isAllProducts = false;

        // Get the filter from the query string
        $categoryId = $request->query->get('filter');

        if ($categoryId === 'all') {
            $products = $productRepository->findAll();
            $isAllProducts = true;

        } elseif ($categoryId) {
            $products = $productRepository->findBy(['category' => $categoryId]);
        } else {
            $defaultCategoryName = 'Pain';
            $defaultCategory = $categoryRepository->findOneBy(['name' => $defaultCategoryName]);

            if ($defaultCategory) {
                $categoryId = $defaultCategory->getId();
                $products = $productRepository->findBy(['category' => $categoryId]);
            }
        }



        $productPath = 'image.jpg';
        $categoryPath = 'image.jpg';

        $productForm = $this->createForm(ProductType::class, new Product());
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $em->persist($productForm->getData());
            $em->flush();
            $this->addFlash('success', 'Produit enregistré avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'categorys' => $categories,
            'products' => $products,
            'categoryId' => $categoryId,
            'isAllProducts' => $isAllProducts,
            'product_path' => $productPath,
            'category_path' => $categoryPath,
            'form' => $productForm->createView(),
        ]);
    }
}