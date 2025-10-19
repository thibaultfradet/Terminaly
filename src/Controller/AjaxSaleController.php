<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\SaleProduct;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AjaxSaleController extends AbstractController
{
    #[Route('/ajax/sale', name: 'app_ajax_sale', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['cart']) || !isset($data['paymentType'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid data',
            ], 400);
        }

        $cart = $data['cart'];
        $paymentType = $data['paymentType'];

        // Create a new Sale
        $sale = new Sale();
        $sale->setCreatedAt(new \DateTimeImmutable());
        $sale->setPaymentType($paymentType);

        // Handle owing payments
        if ($paymentType === 'owing') {
            // If clientName is provided, store it
            $clientName = $data['clientName'] ?? null;
            if ($clientName) {
                $sale->setClientName($clientName);
            }

            // Set owingCompleted to false
            $sale->setOwingCompleted(false);
        }

        $em->persist($sale);

        // Loop through cart to create SaleProduct entries
        foreach ($cart as $productId => $item) {
            /** @var Product $product */
            $product = $em->getRepository(Product::class)->find($productId);
            if (!$product) {
                continue; // skip invalid products
            }

            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? $product->getPrice();

            $saleProduct = new SaleProduct();
            $saleProduct->setSale($sale);
            $saleProduct->setProduct($product);
            $saleProduct->setQuantity($quantity);
            $saleProduct->setPrice($price);

            $em->persist($saleProduct);
        }

        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Sale processed successfully',
            'saleId' => $sale->getId()
        ]);
    }




    #[Route('/facture/{sale}', name: 'app_facture')]
    public function facture(Sale $sale, PdfService $pdfService): Response
    {
        // Render the invoice HTML using Twig
        $html = $this->renderView('facture/index.html.twig', [
            'sale' => $sale,
        ]);

        // Use PdfService to generate and stream the PDF
        $pdfService->generatePdf($html, 'facture_' . $sale->getId() . '.pdf');

        // The PDF is streamed directly, so we can return an empty Response
        return new Response();
    }
}