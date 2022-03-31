<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="show_profile", methods={"GET"})
     */
    public function showProfile(): Response
    {
        return $this->render('profile/show_profile.html.twig');
    }

    /**
     * @Route("/profile/tous-mes-commentaires", name="show_user_commentaries", methods={"GET"})
     */
    public function showUserCommentaries(EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findBy(['author' => $this->getUser()]);

        // Statistiques depuis le Controller (voir la vue show_user_commentaries.html.twig)
        $total = count($commentaries);

        // dd($total);

        return $this->render('profile/show_user_commentaries.html.twig', [
            'commentaries' => $commentaries
        ]);
    }


    /**
     * @Route("/nouveau-rendez-vous", name="app_rendez_vous_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RendezVousRepository $rendezVousRepository): Response
    {
        $rendezVou = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendezVou->setUser($this->getUser());
            $rendezVousRepository->add($rendezVou);
            return $this->redirectToRoute('show_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/new.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }
}
