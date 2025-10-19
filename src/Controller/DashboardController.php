<?php

namespace App\Controller;

use App\Repository\SaleRepository;
use App\Service\DailyStatisticsService;
use App\Service\MonthlyStatisticsService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
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
    public function weeklyAffluence(Request $request, SaleRepository $saleRepository): Response
    {
        // Get selected date from query or default to today
        $selectedDate = $request->query->get('date');
        $date = $selectedDate ? new \DateTime($selectedDate) : new \DateTime();

        // Determine the ISO week and year based on the selected date
        $week = (int) $date->format('W');
        $year = (int) $date->format('o');

        // Create Monday (start) and Sunday (end) of that week
        $startOfWeek = (new \DateTime())->setISODate($year, $week, 1)->setTime(0, 0, 0);
        $endOfWeek = (clone $startOfWeek)->modify('sunday this week 23:59:59');

        // Hours from 6:00 to 19:00 (1-hour increments)
        $hours = [];
        $midpoints = [];
        for ($h = 6; $h < 19; $h++) {
            $hours[] = sprintf('%02d:00', $h);        // Start of the hour (used for counting)
            $midpoints[] = sprintf('%02d:30', $h);    // Midpoint (for chart labels)
        }

        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

        // Initialize affluence array
        $affluence = [];
        foreach ($days as $dayName) {
            $affluence[$dayName] = array_fill(0, count($hours), 0);
        }

        // Fetch sales for the selected week
        $sales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfWeek)
            ->setParameter('end', $endOfWeek)
            ->getQuery()
            ->getResult();

        // Populate affluence array
        foreach ($sales as $sale) {
            $dayName = $days[(int) $sale->getCreatedAt()->format('N') - 1]; // 1 = Monday
            $saleHour = (int) $sale->getCreatedAt()->format('H');

            // Only count if between 6:00 and 18:59
            if ($saleHour >= 6 && $saleHour < 19) {
                $index = $saleHour - 6;
                $affluence[$dayName][$index]++;
            }
        }

        return $this->render('dashboard/weekly_affluence.html.twig', [
            'hours' => $midpoints,
            'affluence' => $affluence,
            'weekNumber' => $week,
            'selectedDate' => $date->format('Y-m-d'),
            'year' => $year,
            'selectedWeek' => sprintf('%d-W%02d', $year, $week),
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
        $salesData = array_map(function ($sale) {
            return [
                'id' => $sale->getId(),
                'total' => $sale->getTotal(),
                'createdAt' => $sale->getCreatedAt()->format('d/m/Y H:i'),
                'client' => $sale->getClientName(),
                'saleProducts' => array_map(function ($sp) {
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
    ): Response {
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



    #[Route('/monthly-excel-export/{month}', name: 'app_monthly_export')]
    public function monthlyExport(int $month, SaleRepository $saleRepository): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Produit');
        $sheet->setCellValue('B1', 'Prix unitaire (€)');
        $sheet->setCellValue('C1', 'Quantité vendue');
        $sheet->setCellValue('D1', 'Total (€)');

        $year = (int) date('Y');

        // Compute start and end of month
        $startOfMonth = new DateTimeImmutable(sprintf('%d-%02d-01 00:00:00', $year, $month));
        $endOfMonth = $startOfMonth->modify('last day of this month 23:59:59');

        // Fetch all sales in the month 
        $sales = $saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();

        //  Aggregate products by name + unit price 
        $productData = [];
        foreach ($sales as $sale) {
            foreach ($sale->getSaleProducts() as $sp) {
                // Use product ID + price as key to avoid merging different prices
                $key = $sp->getProduct()->getId() . '||' . $sp->getPrice();

                if (!isset($productData[$key])) {
                    $productData[$key] = [
                        'name' => $sp->getProduct()->getName(),
                        'unit_price' => $sp->getPrice(),
                        'quantity' => 0,
                        'total' => 0,
                    ];
                }

                $productData[$key]['quantity'] += $sp->getQuantity();
                $productData[$key]['total'] += $sp->getQuantity() * $sp->getPrice();
            }
        }

        //  Fill spreadsheet 
        $row = 2;
        $grandTotal = 0;
        foreach ($productData as $item) {
            $sheet->setCellValue("A{$row}", $item['name']);
            $sheet->setCellValue("B{$row}", number_format($item['unit_price'], 2, ',', ' '));
            $sheet->setCellValue("C{$row}", number_format($item['quantity'], 2, ',', ' '));
            $sheet->setCellValue("D{$row}", number_format($item['total'], 2, ',', ' '));

            $grandTotal += $item['total'];
            $row++;
        }

        //  Add total row 
        $sheet->setCellValue("A{$row}", 'Total :');
        $sheet->setCellValue("D{$row}", number_format($grandTotal, 2, ',', ' '));
        $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);

        //  Auto size columns 
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = sprintf('Facturation-%02d-%d.xlsx', $month, $year);

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment;filename=\"{$fileName}\"");
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
