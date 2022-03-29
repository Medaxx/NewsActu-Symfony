<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaryController extends AbstractController
{

    /**
     * @Route("/ajouter-un-commentaire?article_id={id}", name="add_commentary", methods={"GET|POST"})
     */
    public function addCommentary(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentary = new Commentary();

        $form = $this->createForm(CommentaryFormType::class, $commentary)->handleRequest($request);

        # Cas où le formulaire n'est pas valide. Lorsque le champ 'comment' est vide, il y'a la contrainte NoBlank qui se déclenche.
        if($form->isSubmitted() && $form->isValid() === false) {
            $this->addFlash('warning', 'Votre commentaire est vide !');

            return $this->redirectToRoute('show_article', [
                'cat_alias' => $article->getCategory()->getAlias(),
                'article_alias' => $article->getAlias(),
                'id' => $article->getId()
            ]);
        }

        if($form->isSubmitted() && $form->isValid()) {
            $commentary->setArticle($article);
            $commentary->setCreatedAt(new DateTime);
            $commentary->setUpdatedAt(new DateTime);

            // on set l'auteur du commentaire
            $commentary->setAuthor($this->getUser());

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez commenté l'article <strong>". $article->getTitle()."</strong> avec succés !");

            return $this->redirectToRoute('show_article', [
                'cat_alias' => $article->getCategory()->getAlias(),
                'article_alias' => $article->getAlias(),
                'id' => $article->getId()
            ]);
        }

        return $this->render('rendered/form_commentary.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * 1ère façon : 
     *          Inconvénient : C'est très verbeux.
     *                         Les paramêtres attendus de la route pour faire un redirectToRoute() peuvent ne pas être accessibles.
     *          Avantage :  La redirection sera STATIQUE (unique), tous les utilisateurs seront redirigés  au même endroit.
     * 
     * 2ème façon : 
     *          Inconvénient : La redirection se fera en fonction de l'url de provenance de la requête, à savoir si vous utilisez cette action à plusieurs endroits différents de votre site, l'utilisateur sera redirigé ailleurs que ce que vous avez décidé.
     *          Avantage : La redirection devient DYNAMIQUE. (elle changera en fonction de la provenance de la requête)
     * @Route("/archiver-mon-commentaire_{id}=", name="soft_delete_commentary", methods={"GET"})
     */
    public function softDeleteCommentary(Commentary $commentary, EntityManagerInterface $entityManager, Request $request): Response
    {

        /*
        * PARCE QUE nous allons rediriger vers 'show_article' qui attend 3 arguments, nous avons injecté Request l'objet Request ↑↑↑
        * Cela nous permettra d'accéder aux superglobales PHP ($_GET & $_SERVER => appelés dans l'ordre : query & server)
        *  ==> Suite du commentaire voir 'return'
        * Nous allons voir 2 façons pour rediriger sur la route souhaitée.
        */
        $commentary->setDeletedAt(new DateTime());

        #======== 1ère façon ========#
       //dd($request->query->get('article_alias'));
        #======== 2ème façon ========#
       //dd($request->server->get('HTTP_REFERER'));

        $entityManager->persist($commentary);
        $entityManager->flush();
        
        $this->addFlash('success', "Votre commentaire est archivé");

        #======== 1ère façon ========#
        # Nous récuperons les valeurs des paramètres passés dans $_GET (query)
        # CLa construction de l'URL a lieu dans le fichier 'show_article.html.twig' sur l'attribut HTML 'href' de la balise <a>.
            # ==> VOIR 'show_article.html.twig' pour la suite de la 1ère façon

            # Ici, nous récupérons les valeurs des paramêtres passés dans l'url $_GET (query)
            // return $this->redirectToRoute('show_article', [
            //            'cat_alias' => $request->query->get('cat_alias'),
            //            'article_alias' => $request->query->get('article_alias'),
            //            'id' => $request->query->get('article_id')
            //        ]);
        
            #======== ============================================= ========#

            #======== 2ème façon ========#
                # Pour cette façon, nous avons retirés les paramètres de l'URL dans le fichier 'show_article.html.twig' sur l'attribut HTML 'href' de la balise <a>.

                # Ici, nous utilisions une clé du serveur $_SERVER (server) qui s'appelle 'HTTP_REFERER'
                # Cette clé contient l'URL de provenance de la requête ($request) 
            return $this->redirect($request->server->get('HTTP_REFERER'));

    }

    /**
     * @Route("/restaurer-mon-commentaire_{id}=", name="restore_commentary", methods={"GET"})
     */
    public function restoreCommentary(Commentary $commentary, EntityManagerInterface $entityManager): Response
    {
        $commentary->setDeletedAt();

        $entityManager->persist($commentary);
        $entityManager->flush();

        $this->addFlash('success', 'Votre commentaire est bien restauré');
        return $this->redirectToRoute('show_user_commentaries');
    }


}