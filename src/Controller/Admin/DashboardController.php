<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('bundles/EasyAdminBundle/pages/login.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TrouvezMoi Com');
    }

	public function configureMenuItems(): iterable
	{
		yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

		yield MenuItem::section('Utilisateurs');
		yield MenuItem::linkTo( UserCrudController::class,'Utilisateurs', 'fa fa-user');

		yield MenuItem::section('Réservations');
		yield MenuItem::linkTo(AgendaCrudController::class,'Agenda', 'fa fa-calendar');
				
		yield MenuItem::section('Immobilier');
		yield MenuItem::linkTo(RealEstateCrudController::class,'Biens', 'fa fa-building' );
		yield MenuItem::linkTo(CategorieCrudController::class,'Catégories', 'fa fa-tags' );

		yield MenuItem::section('Paiements');
		yield MenuItem::linkTo(PaymentCrudController::class,'Paiements', 'fa fa-credit-card' );
	}
}
