<?php

namespace App\Controller;

use App\Repository\SaleRepository;
use App\Service\DailyStatisticsService;
use App\Service\MonthlyStatisticsService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{


    #[Route('/daily-statistiques', name: 'admin_daily_statistiques')]
    public function dailyStatistiques(
        Request $request,
        DailyStatisticsService $statisticsService
    ): Response {
        // Get the selected date (default = today)
        $dateString = $request->query->get('date', (new \DateTime())->format('Y-m-d'));
        $selectedDate = new DateTimeImmutable($dateString);

        // Get all sales for this date
        $sales = $statisticsService->getSalesByDay($selectedDate);

        // Compute statistics
        $totalProductSold = $statisticsService->getTotalProductSold($sales);
        $totalRevenue = $statisticsService->getTotalRevenue($sales);
        $clientCount = $statisticsService->getClientCount($sales);
        $revenueByPaymentType = $statisticsService->getRevenueByPaymentType($sales);
        $detailedSales = $statisticsService->getDetailedSalesByCategory($sales);


        return $this->render('dashboard/daily_statistiques.html.twig', [
            'date' => $selectedDate->format('Y-m-d'),
            'totalProductSold' => $totalProductSold,
            'totalRevenue' => $totalRevenue,
            'clientCount' => $clientCount,
            'revenueByPaymentType' => $revenueByPaymentType,
            'detailedSales' => $detailedSales,
        ]);
    }



    #[Route('/monthly-statistiques', name: 'admin_monthly_statistiques')]
    public function monthlyStatistiques(
        Request $request,
        MonthlyStatisticsService $statisticsService
    ): Response {
        $now = new \DateTime();

        $selectedMonth = $request->query->getInt('month', (int) $now->format('n'));
        $selectedYear = $request->query->getInt('year', (int) $now->format('Y'));

        // Fetch sales
        $sales = $statisticsService->getSalesByMonth($selectedMonth, $selectedYear);

        // List of months to display in select
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        // Compute statistics
        $totalProductSold = $statisticsService->getTotalProductSold($sales);
        $totalRevenue = $statisticsService->getTotalRevenue($sales);
        $clientCount = $statisticsService->getClientCount($sales);
        $categoryStats = $statisticsService->getCategoryStatistics($sales);
        $dailyTrend = $statisticsService->getMonthlyDailyTrend($sales, $selectedMonth, $selectedYear);

        return $this->render('dashboard/monthly_statistiques.html.twig', [
            'month' => $selectedMonth,
            'year' => $selectedYear,
            'totalProductSold' => $totalProductSold,
            'totalRevenue' => $totalRevenue,
            'clientCount' => $clientCount,
            'categoryStats' => $categoryStats,
            'trendLabels' => array_keys($dailyTrend),
            'trendValues' => array_values($dailyTrend),
            'months' => $months,
        ]);
    }

    #[Route('/weekly-affluence', name: 'admin_weekly_affluence')]
    public function weeklyAffluence(SaleRepository $saleRepository): Response
    {
        $now = new \DateTime();
        $startOfWeek = (clone $now)->modify('monday this week')->setTime(0, 0, 0);
        $endOfWeek = (clone $startOfWeek)->modify('sunday this week 23:59:59');

        // Hours from 6:30 to 19:00 in 30-min increments
        $hours = [];
        for ($h = 6; $h <= 18; $h++) {
            $hours[] = sprintf('%02d:30', $h);
            $hours[] = sprintf('%02d:00', $h + 1);
        }
        $hours[] = '19:00'; // last point

        $days = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];

        // Initialize array
        $affluence = [];
        foreach ($days as $dayName) {
            $affluence[$dayName] = array_fill(0, count($hours), 0);
        }

        // Fetch sales for the week
        $sales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->getQuery()
            ->getResult();

        // Populate affluence array
        foreach ($sales as $sale) {
            $dayName = $days[(int)$sale->getCreatedAt()->format('N') - 1]; // 1 = Monday
            $hourFloat = (float)$sale->getCreatedAt()->format('G') + ( (int)$sale->getCreatedAt()->format('i') >= 30 ? 0.5 : 0 );
            
            // Only count if between 6.5 and 19
            if ($hourFloat >= 6.5 && $hourFloat <= 19) {
                $index = array_search(
                    sprintf('%02d:%02d', floor($hourFloat), ($hourFloat - floor($hourFloat)) > 0 ? 30 : 0),
                    $hours
                );
                if ($index !== false) {
                    $affluence[$dayName][$index]++;
                }
            }
        }

        return $this->render('dashboard/weekly_affluence.html.twig', [
            'hours' => $hours,
            'affluence' => $affluence,
        ]);
    }


    #[Route('sales-owing', name: 'admin_sales_owing')]
    public function salesOwing(SaleRepository $saleRepository): Response
    {
        // Fetch sales where payment_type is 'owing'
        $owingSales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.paymentType = :owing')
            ->andWhere('s.owingCompleted = false')
            ->setParameter('owing', 'owing')
            ->getQuery()
            ->getResult();

        // Count of owing sales
        $totalOwing = count($owingSales);

        // Prepare simplified array for JS
        $salesData = array_map(function($sale) {
            return [
                'id' => $sale->getId(),
                'total' => $sale->getTotal(),
                'createdAt' => $sale->getCreatedAt()->format('d/m/Y H:i'),
                'client' => $sale->getClientName(),
                'saleProducts' => array_map(function($sp) {
                    return [
                        'product' => ['name' => $sp->getProduct()->getName()],
                        'quantity' => $sp->getQuantity(),
                        'price' => $sp->getPrice(),
                    ];
                }, $sale->getSaleProducts()->toArray()), 
            ];
        }, $owingSales);

        return $this->render('dashboard/sales_owing.html.twig', [
            'owingSales' => $owingSales,
            'totalOwing' => $totalOwing,
            'salesData' => $salesData, 
        ]);
    }

    #[Route('/sale/owing-completed/{id}', name: 'admin_sale_owing_completed')]
    public function completeOwing(
        int $id, 
        SaleRepository $saleRepository, 
        EntityManagerInterface $em
    ): Response
    {
        $sale = $saleRepository->find($id);

        if (!$sale) {
            $this->addFlash('error', 'Sale not found');
            return $this->redirectToRoute('admin_sales_owing');
        }

        $sale->setOwingCompleted(true);
        $em->persist($sale);
        $em->flush();

        $this->addFlash('success', 'Vente complétée avec succès');

        // Redirect back to the owing sales page
        return $this->redirectToRoute('admin_sales_owing');
    }
}
