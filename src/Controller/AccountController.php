<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error  = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username,
        ]);
    }

    /**
     * Permet de déconncter
     * 
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout(){

    }

    /**
     * permet d'afficher le fomrulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();
        
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre Compte à bien été créé."
            );
            return $this->redirectToRoute('account_login');
        }

        
        return $this->render('account/registration.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher et de traiter le forumulaire de modificaton du profile
     * 
     * @Route("/account/profile", name="account_profile")
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre Compte à bien été modifié."
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Elle permet de modifié le mot de passe de l'utilisateur
     * 
     * @Route("/account/password", name="account_password")
     * @Security("is_granted('ROLE_USER')")
     *
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $passwordUpdate = new PasswordUpdate();
        
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                $this->addFlash(
                    'danger',
                    "L'ancien mot de passe n'est pas correct"
                );
            } else{
                $user->setHash($encoder->encodePassword($user, $passwordUpdate->getNewPassword()));
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Mot de passe modifié avec succès"
                );
                return $this->redirectToRoute('account_profile');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @Security("is_granted('ROLE_USER')")
     * 
     * @return Response
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
