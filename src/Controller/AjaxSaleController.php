<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\SaleProduct;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class AjaxSaleController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'SELLER_NAME')]
        private string $sellerName,
        #[Autowire(env: 'SELLER_LEGAL_FORM')]
        private string $sellerLegalForm,
        #[Autowire(env: 'SELLER_ACTIVITY')]
        private string $sellerActivity,
        #[Autowire(env: 'SELLER_NAF_APE')]
        private string $sellerNafApe,
        #[Autowire(env: 'SELLER_SIREN')]
        private string $sellerSiren,
        #[Autowire(env: 'SELLER_SIRET')]
        private string $sellerSiret,
        #[Autowire(env: 'SELLER_VAT_NUMBER')]
        private string $sellerVatNumber,
        #[Autowire(env: 'SELLER_CREATION_DATE')]
        private string $sellerCreationDate,
        #[Autowire(env: 'SELLER_ADDRESS')]
        private string $sellerAddress,
        #[Autowire(env: 'SELLER_CITY')]
        private string $sellerCity,
        #[Autowire(env: 'SELLER_POSTAL_CODE')]
        private string $sellerPostalCode,
        #[Autowire(env: 'SELLER_COUNTRY')]
        private string $sellerCountry,
        #[Autowire(env: 'SELLER_RCS')]
        private string $sellerRcs,
        #[Autowire(env: 'SELLER_LATE_PAYMENT_RATE')]
        private string $sellerLatePaymentRate,
        #[Autowire(env: 'SELLER_TVA_EXEMPTION')]
        private string $sellerTvaExemption,
    ) {
    }

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

        // Create new sale
        $sale = new Sale();
        $sale->setCreatedAt(new \DateTimeImmutable());
        $sale->setPaymentType($paymentType);

        // Handle owing sales
        if ($paymentType === 'owing') {
            $sale->setClientName($data['clientName'] ?? null);
            $sale->setOwingCompleted(false);
        }

        $em->persist($sale);

        // Add each product to sale
        foreach ($cart as $productId => $item) {
            $product = $em->getRepository(Product::class)->find($productId);
            if (!$product)
                continue;

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

        // === Generate and send invoice if requested ===
        if (!empty($data['client_want_invoice'])) {
            $clientName = $data['client_name'] ?? 'Client';
            $clientEmail = $data['client_mail'] ?? null;
            $clientMail = $data['client_mail'] ?? '';

            $translatedPaymentType = '';
            $type = strtolower($sale->getPaymentType());
            switch ($type) {
                case 'card':
                    $translatedPaymentType = 'Carte bancaire';
                    break;
                case 'cash':
                    $translatedPaymentType = 'Espèce';
                    break;
                case 'check':
                    $translatedPaymentType = 'Chèque';
                    break;
                case 'owing':
                    $translatedPaymentType = 'Dû';
                    break;
            }


            // Render invoice HTML with seller info
            $html = $this->renderView('reçu/index.html.twig', [
                'sale' => $sale,
                'translatedPaymentType' => $translatedPaymentType,
                'sellerName' => $this->sellerName,
                'sellerSiret' => $this->sellerSiret,
                'sellerVatNumber' => $this->sellerVatNumber,
                'sellerAddress' => $this->sellerAddress,
                'sellerCity' => $this->sellerCity,
                'sellerPostalCode' => $this->sellerPostalCode,
            ]);

            $fileName = 'reçu_' . $sale->getId() . '.pdf';
            $outputPath = $this->getParameter('kernel.project_dir') . '/public/reçu/' . $fileName;

            // Generate PDF file
            $pdfService->generatePdf($html, $outputPath);

            // Send invoice by email if client email is provided
            if ($clientEmail) {
                $email = (new Email())
                    ->from('no-reply@yourcompany.com')
                    ->to($clientEmail)
                    ->subject('Votre reçu #' . $sale->getId())
                    ->text("Bonjour $clientName,\n\nVeuillez trouver ci-joint votre reçu.")
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

    #[Route('/recu/{sale}', name: 'app_reçu')]
    public function reçu(Sale $sale, PdfService $pdfService): Response
    {
        $translatedPaymentType = '';
        $type = strtolower($sale->getPaymentType());
        switch ($type) {
            case 'card':
                $translatedPaymentType = 'Carte bancaire';
                break;
            case 'cash':
                $translatedPaymentType = 'Espèce';
                break;
            case 'check':
                $translatedPaymentType = 'Chèque';
                break;
            case 'owing':
                $translatedPaymentType = 'Dû';
                break;
        }

        // Render invoice view with seller info
        $html = $this->renderView('recu/index.html.twig', [
            'sale' => $sale,
            'translatedPaymentType' => $translatedPaymentType,
            'sellerName' => $this->sellerName,
            'sellerSiret' => $this->sellerSiret,
            'sellerVatNumber' => $this->sellerVatNumber,
            'sellerAddress' => $this->sellerAddress,
            'sellerCity' => $this->sellerCity,
            'sellerPostalCode' => $this->sellerPostalCode,
        ]);

        // Stream PDF directly to browser
        $pdfService->generatePdf($html, 'reçu_' . $sale->getId() . '.pdf');

        return new Response();
    }
}