<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ?string $categoryId = null
    ): Response {
        // Fetch all categories
        $categories = $categoryRepository->findAll();
        $products = [];
        $isAllProducts = false;

        if ($categoryId === 'all') {
            // All products
            $products = $productRepository->findAll();
            $isAllProducts = true;

        } elseif ($categoryId) {
            // Specific category
            $products = $productRepository->findBy(['category' => $categoryId]);

        } else {
            // Default category fallback
            $defaultCategoryName = 'Pain';
            $defaultCategory = $categoryRepository->findOneBy(['name' => $defaultCategoryName]);

            if ($defaultCategory) {
                $categoryId = $defaultCategory->getId();
                $products = $productRepository->findBy(['category' => $categoryId]);
            }
        }

        // Example static image paths (to be adjusted to your real structure)
        $productPath = 'eclair.jpg';
        $categoryPath = 'pain.jpg';

        // Render Twig template
        return $this->render('home/index.html.twig', [
            'categorys' => $categories,
            'products' => $products,
            'categoryId' => $categoryId,
            'isAllProducts' => $isAllProducts,
            'product_path' => $productPath,
            'category_path' => $categoryPath,
        ]);
    }
}