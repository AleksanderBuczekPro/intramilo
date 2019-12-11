<?php

namespace App\Controller;

use App\Service\Stats;
use App\Service\Filter;
use App\Entity\Category;
use App\Entity\SubCategory;
use App\Repository\SheetRepository;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(SheetRepository $sheetRepo, DocumentRepository $docRepo, Request $request, ObjectManager $manager, Filter $filter)
    {
        $subCategories = $this->getUser()->getSubCategories();

        $f = $request->query->get('filter');

    
        // Si il y a un filtre
        if(isset($f) && $f != "all"){

            $files = $filter->getResults($f, $subCategories);

        }else{
            
            $sheets = [];
            foreach ($subCategories as $subCategory) {
                $sheet = $sheetRepo->findBySubCategory($subCategory);
                $sheets = array_merge_recursive($sheets, $sheet);
            }

            $documents = [];
            foreach ($subCategories as $subCategory) {
                $document = $docRepo->findBySubCategory($subCategory);
                $documents = array_merge_recursive($documents, $document);
            }

            $files = array_merge_recursive($sheets, $documents);

            usort($files, function($a, $b){ 
                return strcasecmp($a->getTitle(), $b->getTitle());
            });

        }

        

        return $this->render('admin/dashboard/index.html.twig', [
            // 'sheets' => $sheets,
            // 'documents' => $documents,
            'files' => $files,
            'subCategories' => $subCategories
        ]);
    }
}
