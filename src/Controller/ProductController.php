<?php

namespace App\Controller;

use App\Entity\Product;;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SaleRepository;
use App\Entity\SaleProduct;
use App\Repository\SaleProductRepository;
use App\Form\ProductType;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route('/admin/product/{id}/stats', name: 'admin_product_stats')]
    public function productStats(Product $product, Request $request, SaleProductRepository $saleProductRepository): Response
    {
        // Détermine le mois sélectionné ou par défaut le mois courant
        $currentMonth = $request->query->getInt('month', (int) date('n'));
        $currentYear = (int) date('Y');

        $startOfMonth = new \DateTimeImmutable("$currentYear-$currentMonth-01 00:00:00");
        $endOfMonth = $startOfMonth->modify('last day of this month')->setTime(23,59,59);

        // Récupère toutes les ventes du produit ce mois
        $sales = $saleProductRepository->createQueryBuilder('sp')
            ->join('sp.sale', 's')
            ->andWhere('sp.product = :product')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('product', $product)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();

        // Prépare les données journalières
        $daysInMonth = (int)$startOfMonth->format('t');
        $dailySales = [];
        $dailyRevenue = [];
        $saleIdsPerDay = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayKey = sprintf('%02d', $d);
            $dailySales[$dayKey] = 0;
            $dailyRevenue[$dayKey] = 0.0;
            $saleIdsPerDay[$dayKey] = [];
        }

        foreach ($sales as $saleProduct) {
            $sale = $saleProduct->getSale();
            $day = $sale->getCreatedAt()->format('d');

            // Compte uniquement les ventes uniques par jour
            if (!in_array($sale->getId(), $saleIdsPerDay[$day])) {
                $saleIdsPerDay[$day][] = $sale->getId();
                $dailySales[$day] += 1;
            }

            // Ajoute le chiffre généré pour ce produit
            $dailyRevenue[$day] += $saleProduct->getPrice() * $saleProduct->getQuantity();
        }

        $totalSalesCount = array_sum($dailySales);
        $totalRevenue = array_sum($dailyRevenue);

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return $this->render('admin/product_stats.html.twig', [
            'product' => $product,
            'dailySales' => $dailySales,
            'dailyRevenue' => $dailyRevenue,
            'dailySalesValues' => array_values($dailySales),
            'dailyRevenueValues' => array_values($dailyRevenue),
            'dailySalesLabels' => array_keys($dailySales),
            'months' => $months,
            'currentMonth' => $currentMonth,
            'totalSalesCount' => $totalSalesCount,
            'totalRevenue' => $totalRevenue,
        ]);
    }




    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageFile')->getData();
            // image file is require to create a new one   
            if ($imageFile) {

                $entityManager->persist($product);
                $entityManager->flush();


                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/products';

                $filename = $product->getId() . '_product.png';
                $imageFile->move($uploadDir, $filename);

                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Une image est obligatoire.');
            }
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/products';
                

                $filename = $product->getId() . '_product.png';
                $imagePath = $uploadDir . '/' . $filename;

                // Supprimer l'ancienne image si elle existe
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                $imageFile->move($uploadDir, $filename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
