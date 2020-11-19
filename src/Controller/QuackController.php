<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Entity\Quack;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuackController extends AbstractController
{
    /**
     * @Route("/quack", name="quack_index", methods={"GET"})
     * @param QuackRepository $quackRepository
     * @return Response
     */
    public function index(QuackRepository $quackRepository): Response
    {
        return $this->render('quack/index.html.twig', [
            'quacks' => $quackRepository->findAll(),
        ]);
    }

    /**
     * @Route("/quack/new", name="quack_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $quack = new Quack();
        $quack->setCreatedAt(new \DateTime('now'));
        $quack->setAuthor($this->getUser());
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('quack_index');
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quack/{id}", name="quack_show", methods={"GET"})
     * @param Quack $quack
     * @return Response
     */
    public function show(Quack $quack): Response
    {
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    /**
     * @Route("/quack/{id}/edit", name="quack_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Quack $quack
     * @return Response
     */
    public function edit(Request $request, Quack $quack): Response
    {
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if (!$this->isGranted('EDIT_QUACK', $quack)) {
            throw $this->createAccessDeniedException('Hands off other quack!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quack_index');
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/quack/{id}", name="quack_delete", methods={"DELETE"})
     * @param Request $request
     * @param Quack $quack
     * @return Response
     */
    public function delete(Request $request, Quack $quack): Response
    {
        if (!$this->isGranted('DELETE_QUACK', $quack)) {
            throw $this->createAccessDeniedException('Are you sure bout\' quack ?');
        }

        if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quack_index');
    }
}
