<?php
// src/Controller/ProductOrderController.php
namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductOrderController extends AbstractController
{
    #[Route('/product-order', name: 'app_product_order_index')]
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ): Response {
        // Retrieve all categories
        $categories = $categoryRepository->findAll();


        // Retrieve selected category ID from query parameters
        $categoryId = $request->query->get('category') ?? null;

        // If no category provided, select the first one
        $selectedCategory = null;
        if ($categoryId) {
            $selectedCategory = $categoryRepository->find($categoryId);
        }
        if (!$selectedCategory) {
            $selectedCategory = $categories[0];
        }

        // Retrieve products for this category, ordered by order_number
        $products = $productRepository->findBy(
            ['category' => $selectedCategory],
            ['orderNumber' => 'ASC']
        );

        // Render the template
        return $this->render('product_order/index.html.twig', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'products' => $products,
        ]);
    }





    #[Route('/product-order/update', name: 'app_product_order_update', methods: ['POST'])]
    public function update(
        Request $request,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        // Decode JSON body
        $data = json_decode($request->getContent(), true);

        // Check valid JSON array
        if (!is_array($data)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid format. Expected an array of [id, new_order].',
            ], 400);
        }

        $updatedCount = 0;

        // Loop through each [id, new_order] pair
        foreach ($data as $pair) {
            // Each pair must contain exactly two items: [id, new_order]
            if (!is_array($pair) || count($pair) < 2) {
                continue;
            }

            $id = (int) $pair[0];
            $newOrder = (int) $pair[1];

            // Find the product and update
            $product = $productRepository->find($id);
            if ($product) {
                $product->setOrderNumber($newOrder);
                $updatedCount++;
            }
        }

        // Commit DB changes
        if ($updatedCount > 0) {
            $em->flush();
        }

        return new JsonResponse([
            'success' => true,
            'message' => sprintf('Updated %d products successfully.', $updatedCount),
        ]);
    }
}