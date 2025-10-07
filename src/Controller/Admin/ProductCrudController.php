<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // === Définir l’action personnalisée “Statistiques” ===
        $viewStats = Action::new('viewStats', 'Statistiques')
            ->setIcon('fa fa-chart-bar')
            ->linkToRoute('admin_product_stats', function ($entity) {
                // Passe l’ID du produit à la route
                return ['id' => $entity->getId()];
            })
            ->addCssClass('btn btn-outline-info') // bouton discret style EasyAdmin
            ->displayIf(static fn($entity) => $entity->getId() !== null); // sécurité

        return $actions
            // === Ajouter le bouton sur la page INDEX ===
            ->add(Crud::PAGE_INDEX, $viewStats)

            // === Réordonner les boutons dans la colonne des actions ===
            ->reorder(Crud::PAGE_INDEX, [Action::EDIT, 'viewStats', Action::DELETE])

            // === Renommer les actions existantes ===
            ->update(Crud::PAGE_INDEX, Action::NEW, fn(Action $a) => $a->setLabel('Créer un produit'))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $a) => $a->setLabel('Modifier'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn(Action $a) => $a->setLabel('Supprimer'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, fn(Action $a) => $a->setLabel('Créer et ajouter un autre'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, fn(Action $a) => $a->setLabel('Sauvegarder'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, fn(Action $a) => $a->setLabel('Sauvegarder les modifications'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, fn(Action $a) => $a->setLabel('Sauvegarder et continuer l’édition'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('name', 'Libellé'),
            NumberField::new('price', 'Prix')->setNumDecimals(2),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ->setPageTitle('index', 'Liste des produits')
            ->setPageTitle('new', 'Créer un produit');
    }
}