<?php

namespace App\Controller;

use App\Service\Stats;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, Stats $stats)
    {

        $allStats = $stats->getStats();

        $bestAds = $stats->getAdsStats('DESC');
        $worstAds = $stats->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $allStats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
