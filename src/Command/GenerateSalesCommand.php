<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\SaleProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-sales',
    description: 'Génère 400 ventes aléatoires pour tester les statistiques'
)]
class GenerateSalesCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Début génération des ventes (400)...');

        $products = $this->em->getRepository(Product::class)->findAll();
        if (!$products) {
            $output->writeln('❌ Aucun produit trouvé !');
            return Command::FAILURE;
        }

        $year = 2025;
        $months = [8, 9, 10]; // Août, Septembre, Octobre
        $totalSales = 400;

        for ($i = 0; $i < $totalSales; $i++) {
            // Choisir un mois aléatoire parmi Août, Septembre, Octobre
            $month = $months[array_rand($months)];
            $daysInMonth = (int)(new \DateTimeImmutable("$year-$month-01"))->format('t');
            $day = rand(1, $daysInMonth);

            $saleDate = new \DateTimeImmutable("$year-$month-$day " . rand(8, 18) . ":" . rand(0,59) . ":" . rand(0,59));
            $sale = new Sale();
            $sale->setCreatedAt($saleDate);
            $sale->setPaymentType(rand(0,1) ? 'cash' : 'card');
            $this->em->persist($sale);

            // Ajouter 1 à 5 produits à la vente
            $numProducts = rand(1, 5);
            $selectedKeys = array_rand($products, min($numProducts, count($products)));
            // s'assurer que c'est toujours un tableau
            $selectedKeys = is_array($selectedKeys) ? $selectedKeys : [$selectedKeys];

            foreach ($selectedKeys as $key) {
                $product = $products[$key];
                $saleProduct = new SaleProduct();
                $saleProduct->setSale($sale);
                $saleProduct->setProduct($product);
                $saleProduct->setQuantity(rand(1, 5));
                $saleProduct->setPrice($product->getPrice());
                $this->em->persist($saleProduct);
            }
        }

        $this->em->flush();
        $output->writeln('✅ Génération de 400 ventes terminée !');

        return Command::SUCCESS;
    }
}