<?php

namespace App\Controller;

use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
   
    /**
     * Custom dashboard statistiques
     */
    #[Route('/statistiques', name: 'admin_statistiques')]
    public function statistiques(Request $request,SaleRepository $saleRepository): Response
    {
        $now = new \DateTime();

        $selectedMonth = $request->query->getInt('month', (int) $now->format('n'));
        $year = (int) $now->format('Y');

        $startOfMonth = new \DateTimeImmutable("$year-$selectedMonth-01 00:00:00");
        $daysInMonth = (int) $startOfMonth->format('t'); 
        $endOfMonth = $startOfMonth->modify("last day of this month 23:59:59");

        $sales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();

        //Initialise array
        $dailyRevenue = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayKey = sprintf('%02d', $d); // ex: '01', '02', ...
            $dailyRevenue[$dayKey] = 0;
        }

        //sales per day 
        foreach ($sales as $sale) {
            $day = $sale->getCreatedAt()->format('d');
            $dailyRevenue[$day] += $sale->getTotal();
        }

        return $this->render('dashboard/statistiques.html.twig', [
            'totalSales' => count($sales),
            'totalAmount' => array_sum($dailyRevenue),
            'dailyRevenueLabels' => array_keys($dailyRevenue),
            'dailyRevenueValues' => array_values($dailyRevenue),
            'months' => [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
            ],
            'currentMonth' => $selectedMonth,
        ]);
    }


    /**
     * Display sales that are not completed (owing)
     */
    #[Route('sales-owing', name: 'admin_sales_owing')]
    public function salesOwing(SaleRepository $saleRepository): Response
    {
        // Fetch sales where payment_type is 'owing'
        $owingSales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.paymentType = :owing')
            ->setParameter('owing', 'owing')
            ->getQuery()
            ->getResult();

        // Count of owing sales
        $totalOwing = count($owingSales);

        return $this->render('dashboard/sales_owing.html.twig', [
            'owingSales' => $owingSales,
            'totalOwing' => $totalOwing,
        ]);
    }
}
