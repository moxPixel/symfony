<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
     public function __construct(EntityManagerInterface $manager,UserPasswordHasherInterface $passwordHash){
        $this->manager = $manager;
         $this->passwordHash = $passwordHash;
     }

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response
    {
         $user = new User();// Nouvelle instance de user
         $form = $this->createForm(RegisterType::class, $user);// Création du formulaire
         $form->handleRequest($request);// Traitement du formulaire
         if($form->isSubmitted() && $form->isValid()) { //Si le formulaire et soumis et valide alor..
            $emptyPassword = $form->get('password')->getData();
    
            if($emptyPassword == null){
             $user->setPassword($user->getPassword());
            }else{
               $passwordEncod =  $this->passwordHash->hashPassword($user , $emptyPassword);
               $user->setPassword($passwordEncod);
            }


                $this->manager->persist($user);// On persiste l'utilisateur
                $this->manager->flush();// On flush     
                return $this->redirectToRoute('app_login');
         }

        return $this->render('register/index.html.twig', [
           'myForm' => $form->createView() // On passe le formulaire à la vue
        ]);
    }
}
