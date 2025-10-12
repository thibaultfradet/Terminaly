<?php

namespace App\Service;

use App\Repository\SaleRepository;
use DateTimeImmutable;

class MonthlyStatisticsService
{
    private SaleRepository $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }

    /**
     * Get all sales for a specific month
     */
    public function getSalesByMonth(int $month, int $year): array
    {
        $startOfMonth = new DateTimeImmutable("$year-$month-01 00:00:00");
        $endOfMonth = $startOfMonth->modify('last day of this month 23:59:59');

        return $this->saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compute total revenue for the month
     */
    public function getTotalRevenue(array $sales): float
    {
        $total = 0;
        foreach ($sales as $sale) {
            $total += $sale->getTotal();
        }
        return $total;
    }

    /**
     * Compute total number of distinct clients for the month
     */
    public function getClientCount(array $sales): int
    {
        return count($sales);
    }

    /**
     * Get total number of products sold across a list of sales
     */
    public function getTotalProductSold(array $sales): int
    {
        $count = 0;

        foreach ($sales as $sale) {
            foreach ($sale->getSaleProducts() as $saleProduct) {
                $count += $saleProduct->getQuantity();
            }
        }

        return $count;
    }


    /**
     * Get quantity sold and revenue per category
     */
    public function getCategoryStatistics(array $sales): array
    {
        $categories = [];

        foreach ($sales as $sale) {
            foreach ($sale->getSaleProducts() as $saleProduct) {
                $categoryName = $saleProduct->getProduct()->getCategory()->getName();

                if (!isset($categories[$categoryName])) {
                    $categories[$categoryName] = [
                        'category' => $categoryName,
                        'quantity' => 0,
                        'revenue' => 0
                    ];
                }

                $categories[$categoryName]['quantity'] += $saleProduct->getQuantity();
                $categories[$categoryName]['revenue'] += $saleProduct->getQuantity() * $saleProduct->getPrice();
            }
        }

        return array_values($categories);
    }

    /**
     * Generate daily revenue trend for the month (used for chart)
     */
    public function getMonthlyDailyTrend(array $sales, int $month, int $year): array
    {
        $startOfMonth = new DateTimeImmutable("$year-$month-01 00:00:00");
        $daysInMonth = (int) $startOfMonth->format('t');

        $dailyRevenue = array_fill(1, $daysInMonth, 0);

        foreach ($sales as $sale) {
            $day = (int) $sale->getCreatedAt()->format('j');
            $dailyRevenue[$day] += $sale->getTotal();
        }

        return $dailyRevenue;
    }
}