<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductOrderController extends AbstractController
{
    #[Route('/product/order', name: 'app_product_order_index')]
    public function index(): Response
    {
        return $this->render('product_order/index.html.twig', [
            'controller_name' => 'ProductOrderController',
        ]);
    }



    #[Route('/product/order/update', name: 'app_product_order_update')]
    public function update(): Response
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'order number processed successfully',
        ]);
    }
    
}
