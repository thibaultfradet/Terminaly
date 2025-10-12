<?php

namespace App\Service;

use App\Repository\SaleRepository;
use DateTimeImmutable;

class DailyStatisticsService
{
    private SaleRepository $saleRepository;

    public function __construct(SaleRepository $saleRepository)
    {
        $this->saleRepository = $saleRepository;
    }


   


    /**
     * Get all sales of a specific day
     */
    public function getSalesByDay(DateTimeImmutable $day): array
    {
        $startOfDay = $day->setTime(0, 0, 0);
        $endOfDay = $day->setTime(23, 59, 59);

        return $this->saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getResult();
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
     * Compute the total revenue for a specific day
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
     * Get total number of distinct clients for a day
     */
    public function getClientCount(array $sales): int
    {
        return count($sales);
    }

    /**
     * Get total revenue per payment type with translation
     */
    public function getRevenueByPaymentType(array $sales): array
    {
        // Initialize all payment types to 0
        $byPaymentType = [
            'Carte bancaire' => 0,
            'Espèce' => 0,
            'Chèque' => 0,
            'Dû' => 0,
        ];

        foreach ($sales as $sale) {
            $type = strtolower($sale->getPaymentType());
            switch ($type) {
                case 'card':
                    $translatedType = 'Carte bancaire';
                    break;
                case 'cash':
                    $translatedType = 'Espèce';
                    break;
                case 'check':
                    $translatedType = 'Chèque';
                    break;
                case 'owing':
                    $translatedType = 'Dû';
                    break;
                default:
                    // If an unknown payment type, we can skip or add it dynamically
                    $translatedType = ucfirst($type); // fallback
                    if (!isset($byPaymentType[$translatedType])) {
                        $byPaymentType[$translatedType] = 0;
                    }
            }

            $byPaymentType[$translatedType] += $sale->getTotal();
        }

        return $byPaymentType;
    }


  /**
     * Get detailed sales summary grouped by product category
     */
    public function getDetailedSalesByCategory(array $sales): array
    {
        $categorySummary = [];

        foreach ($sales as $sale) {
            foreach ($sale->getSaleProducts() as $saleProduct) {
                $product = $saleProduct->getProduct();
                $categoryName = $product->getCategory()->getName(); 
                $productName = $product->getName();
                $quantity = $saleProduct->getQuantity();
                $total = $saleProduct->getPrice() * $quantity;

                // Initialize category if not already created
                if (!isset($categorySummary[$categoryName])) {
                    $categorySummary[$categoryName] = [
                        'categoryName' => $categoryName,
                        'totalSales' => 0,
                        'totalQuantity' => 0,
                        'productList' => [],
                    ];
                }

                // Initialize product inside the category if not already there
                if (!isset($categorySummary[$categoryName]['productList'][$productName])) {
                    $categorySummary[$categoryName]['productList'][$productName] = [
                        'name' => $productName,
                        'quantity' => 0,
                        'total' => 0,
                    ];
                }

                // Increment product data
                $categorySummary[$categoryName]['productList'][$productName]['quantity'] += $quantity;
                $categorySummary[$categoryName]['productList'][$productName]['total'] += $total;
                $categorySummary[$categoryName]['totalQuantity'] += $quantity;


                // Add to category total
                $categorySummary[$categoryName]['totalSales'] += $total;
            }
        }

        // Convert inner associative arrays to numeric arrays for clean JSON structure
        foreach ($categorySummary as &$category) {
            $category['productList'] = array_values($category['productList']);
        }

        // Re-index the outer array
        return array_values($categorySummary);
    }  
}