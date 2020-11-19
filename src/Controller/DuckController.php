<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Form\DuckType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Security\DuckEditingVoter;

/**
 * @Route("/duck")
 */
class DuckController extends AbstractController
{

    /**
     * @Route("/new", name="duck_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $duck = new Duck();
        $form = $this->createForm(DuckType::class, $duck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($duck);
            $entityManager->flush();

            return $this->redirectToRoute('quack_index');
        }

        return $this->render('duck/new.html.twig', [
            'duck' => $duck,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="duck_show", methods={"GET"})
     * @param Duck $duck
     * @return Response
     */
    public function show(Duck $duck): Response
    {
        return $this->render('duck/show.html.twig', [
            'duck' => $duck,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="duck_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Duck $duck
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, Duck $duck, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(DuckType::class, $duck);
        $form->handleRequest($request);

        if (!$this->isGranted('NOM', $duck)) {
            throw $this->createAccessDeniedException('Hands off others quackmation!');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $duck->setPassword(
                $passwordEncoder->encodePassword(
                    $duck,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($duck);
            $entityManager->flush();

            return $this->redirectToRoute('duck_show', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('duck/edit.html.twig', [
            'duck' => $duck,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="duck_delete", methods={"DELETE"})
     * @param Request $request
     * @param Duck $duck
     * @return Response
     */
    public function delete(Request $request, Duck $duck): Response
    {
        if ($this->isCsrfTokenValid('delete' . $duck->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($duck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quack_index');
    }
}
