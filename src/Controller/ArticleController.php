<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/admin/all/article", name="app_all_article")
     */
    public function allArtice(): Response
    {
        $articles = $this->manager->getRepository(Article::class)->findAll();
        // logique stocker dans une variable avec tout les articles

        return $this->render('article/allArticle.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route('/admin/article', name: 'app_article')]
    public function index(Request $request): Response
    {
        $article = new Article(); // Nouvelle instance de article
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // recuperer l'utilisateur connecter et envoyer le prenom dans le setAuteur.

            $article->setAuteur($this->getUser()->getNomComplet());

            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        };

        return $this->render('article/index.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }



    #[Route('/admin/article/delete/{id}', name: 'app_article_delete')]
    public function articleDelete(Article $article): Response
    {
        $this->manager->remove($article);
        $this->manager->flush();
        return $this->redirectToRoute('app_home');
    }



    #[Route('/admin/article/edit/{id}', name: 'app_article_edit')]
    public function articleEdit(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article); // Création du formulaire
        $form->handleRequest($request); // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($article);
            $this->manager->flush();
            return $this->redirectToRoute('app_home');
        };

        return $this->render('article/editArticle.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }




    /**
     * @Route("/single/article/{id}", name="app_view_article")
     */
    public function singleArtice(Article $article, Request $request): Response
    {

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setDate(new \DateTime());
            $commentaire->setAuteur($this->getUser());
            $commentaire->setArticle($article);
            $this->manager->persist($commentaire);
            $this->manager->flush();
            return $this->redirectToRoute('app_view_article', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('article/singleArticle.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }
}
