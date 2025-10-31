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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class AjaxSaleController extends AbstractController
{
    #[Route('/ajax/sale', name: 'app_ajax_sale', methods: ['POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PdfService $pdfService,
        MailerInterface $mailer
    ): JsonResponse {
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
            $clientName = $data['clientName'] ?? null;
            if ($clientName) {
                $sale->setClientName($clientName);
            }
            $sale->setOwingCompleted(false);
        }

        $em->persist($sale);

        // Create SaleProduct entities for each product in the cart
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

        // === Handle invoice generation and email sending if requested ===
        if (!empty($data['client_want_invoice']) && $data['client_want_invoice'] === true) {
            $clientName = $data['client_name'] ?? 'Client';
            $clientEmail = $data['client_mail'] ?? null;
            $clientAddress = $data['client_address'] ?? '';

            // Render invoice HTML using Twig
            $html = $this->renderView('facture/index.html.twig', [
                'sale' => $sale,
                'clientName' => $clientName,
                'clientAddress' => $clientAddress,
                // Seller info (you could refactor to a service later)
                'sellerName' => getenv('SELLER_NAME'),
                'sellerLegalForm' => getenv('SELLER_LEGAL_FORM'),
                'sellerActivity' => getenv('SELLER_ACTIVITY'),
                'sellerNafApe' => getenv('SELLER_NAF_APE'),
                'sellerSiren' => getenv('SELLER_SIREN'),
                'sellerSiret' => getenv('SELLER_SIRET'),
                'sellerVatNumber' => getenv('SELLER_VAT_NUMBER'),
                'sellerCreationDate' => getenv('SELLER_CREATION_DATE'),
                'sellerAddress' => getenv('SELLER_ADDRESS'),
                'sellerCity' => getenv('SELLER_CITY'),
                'sellerPostalCode' => getenv('SELLER_POSTAL_CODE'),
                'sellerCountry' => getenv('SELLER_COUNTRY'),
                'sellerRcs' => getenv('SELLER_RCS'),
                'sellerLatePaymentRate' => getenv('SELLER_LATE_PAYMENT_RATE'),
                'sellerTvaExemption' => getenv('SELLER_TVA_EXEMPTION'),
            ]);

            // Generate PDF and store it in /public/facture/
            $fileName = 'facture_' . $sale->getId() . '.pdf';
            $outputPath = $this->getParameter('kernel.project_dir') . '/public/facture/' . $fileName;

            // Use PdfService to save the file instead of streaming it
            $pdfService->generatePdf($html, $outputPath);

            // Send invoice via email if client email is provided
            if ($clientEmail) {
                $email = (new Email())
                    ->from(getenv('MAIL_FROM') ?: 'no-reply@yourcompany.com')
                    ->to($clientEmail)
                    ->subject('Votre facture #' . $sale->getId())
                    ->text('Bonjour ' . $clientName . ",\n\nVeuillez trouver ci-joint votre facture.\nMerci pour votre achat.")
                    ->attachFromPath($outputPath, $fileName, 'application/pdf');

                $mailer->send($email);
            }
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Sale processed successfully',
            'saleId' => $sale->getId(),
        ]);
    }


    // #[Route('/facture/{sale}', name: 'app_facture')]
    // public function facture(Sale $sale, PdfService $pdfService): Response
    // {
    //     // Render and stream invoice as before
    //     $html = $this->renderView('facture/index.html.twig', [
    //         'sale' => $sale,
    //     ]);

    //     $pdfService->generatePdf($html, 'facture_' . $sale->getId() . '.pdf');

    //     return new Response();
    // }
}