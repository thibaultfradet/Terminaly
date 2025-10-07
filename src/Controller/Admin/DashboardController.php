<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\SaleProductRepository;
use App\Repository\SaleRepository;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private SaleRepository $saleRepository;
    private SaleProductRepository $saleProductRepository;
    private AdminUrlGenerator $adminUrlGenerator;
    public function __construct(SaleRepository $saleRepository, SaleProductRepository $saleProductRepository, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->saleRepository = $saleRepository;
        $this->saleProductRepository = $saleProductRepository;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }



    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'productCrudUrl' => $this->adminUrlGenerator
                ->setController(ProductCrudController::class)
                ->generateUrl(),
            'categoryCrudUrl' => $this->adminUrlGenerator
                ->setController(CategoryCrudController::class)
                ->generateUrl(),
            'statistiquesUrl' => $this->generateUrl('admin_statistiques'),
        ]);
    }



    /**
     * Custom dashboard statistiques
     */
    #[Route('/admin/statistiques', name: 'admin_statistiques')]
    public function statistiques(Request $request): Response
    {
        $now = new \DateTime();

        // get month from query param or fallback to current
        $selectedMonth = $request->query->getInt('month', (int) $now->format('n'));
        $year = (int) $now->format('Y');

        $startOfMonth = new \DateTimeImmutable("$year-$selectedMonth-01 00:00:00");
        $endOfMonth = $startOfMonth->modify('last day of this month')->setTime(23,59,59);

        $sales = $this->saleRepository->createQueryBuilder('s')
            ->andWhere('s.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();

        $totalSales = count($sales);
        $totalAmount = array_reduce($sales, fn($carry, $sale) => $carry + $sale->getTotal(), 0);

        // revenue per day in selected month
        $daily = [];
        foreach ($sales as $sale) {
            $day = $sale->getCreatedAt()->format('Y-m-d');
            if (!isset($daily[$day])) {
                $daily[$day] = 0;
            }
            $daily[$day] += $sale->getTotal();
        }

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return $this->render('admin/statistiques.html.twig', [
            'totalSales' => $totalSales,
            'totalAmount' => $totalAmount,
            'dailyRevenue' => $daily,
            'dailyRevenueValues' => array_values($daily),
            'dailyRevenueLabels' => array_keys($daily),
            'months' => $months,
            'currentMonth' => $selectedMonth, 
        ]);
    }









    #[Route('/admin/product/{id}/stats', name: 'admin_product_stats')]
    public function productStats(Product $product, Request $request): Response
    {
        // Détermine le mois sélectionné ou par défaut le mois courant
        $currentMonth = $request->query->getInt('month', (int) date('n'));
        $currentYear = (int) date('Y');

        $startOfMonth = new \DateTimeImmutable("$currentYear-$currentMonth-01 00:00:00");
        $endOfMonth = $startOfMonth->modify('last day of this month')->setTime(23,59,59);

        // Récupère toutes les ventes du produit ce mois
        $sales = $this->saleProductRepository->createQueryBuilder('sp')
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



    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Statistiques', 'fas fa-chart-bar', 'admin_statistiques');
        yield MenuItem::linkToCrud('Products', 'fas fa-bread-slice', Product::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
    }



    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }
}
