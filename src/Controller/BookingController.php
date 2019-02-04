<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad,Request $request, ObjectManager $manager)
    {
        $booking= new Booking;
        $form = $this->CreateForm(BookingType::class, $booking, [
            'validation_groups' => ['Default', 'front']
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setBooker($this->getUser())
                    ->setAd($ad);

            if(!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisi sont déjà réservé"
                );
            } else {
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->CreateView()
        ]);
    }

    /**
     * Permet d'afficher une réservation
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     */
    public function show(Booking $booking, ObjectManager $manager, Request $request){

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($booking->getBooker());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire à bien été enregistré.'
            );
        }

        return $this->render('booking/show.html.twig',[
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }
}
