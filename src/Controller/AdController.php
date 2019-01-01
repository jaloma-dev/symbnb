<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $adRepo)
    {
        $ads = $adRepo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de créé une annonce
     * @Route("/ads/new", name="ads_new")
     * 
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager){
        $ad = new Ad();
        
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "l'annonce <strong> \"{$ad->getTitle()}\"</strong> a bien été enregistée"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug(),
            ]);
        }
            

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show(Ad $ad){
        return $this->render('ad/show.html.twig',[
            'ad' => $ad,
        ]);
    }

}
