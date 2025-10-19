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
            // Order products by 'orderNumber' ascending
            $products = $productRepository->findBy([], ['orderNumber' => 'ASC']);
            $isAllProducts = true;

        } elseif ($categoryId) {
            // Order products by 'orderNumber' ascending within a specific category
            $products = $productRepository->findBy(['category' => $categoryId], ['orderNumber' => 'ASC']);

        } else {
            $defaultCategoryName = 'Pain';
            $defaultCategory = $categoryRepository->findOneBy(['name' => $defaultCategoryName]);

            if ($defaultCategory) {
                $categoryId = $defaultCategory->getId();
                // Order products by 'orderNumber' ascending for the default category
                $products = $productRepository->findBy(['category' => $categoryId], ['orderNumber' => 'ASC']);
            }
        }



        return $this->render('home/index.html.twig', [
            'categorys' => $categories,
            'products' => $products,
            'categoryId' => $categoryId,
            'isAllProducts' => $isAllProducts,
        ]);
    }
}