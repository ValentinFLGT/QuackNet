<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Entity\Quack;
use App\Form\DuckType;
use App\Form\QuackType;
use App\Repository\QuackRepository;
use App\Service\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/{id}", name="duck_show", methods={"GET", "POST"})
     * @param Duck $duck
     * @return Response
     */
    public function show(Duck $duck, QuackRepository $quackRepository, Request $request, UploaderHelper $uploaderHelper): Response
    {
        $quack = new Quack();
        $quack->setCreatedAt(new \DateTime('now'));
        $quack->setAuthor($this->getUser());
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();


            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadQuackImage($uploadedFile);
                $quack->setFileName($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('duck_show', ['id' => $duck->getId()]);
        }

        return $this->render('duck/show.html.twig', [
            'duck' => $duck,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="duck_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Duck $duck
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function edit(Request $request, Duck $duck, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
    {
        $form = $this->createForm(DuckType::class, $duck);
        $form->handleRequest($request);

        if (!$this->isGranted('EDIT_DUCK', $duck)) {
            throw $this->createAccessDeniedException('Who the duck do you think you are?!');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedPassword = $form['plainPassword']->getData();

            if ($updatedPassword) {
                // encode the plain password
                $duck->setPassword(
                    $passwordEncoder->encodePassword(
                        $duck,
                        $updatedPassword
                    )
                );
            }
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();


            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadDuckImage($uploadedFile);
                $duck->setFileName($newFilename);
            }
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
        if (!$this->isGranted('DELETE_DUCK', $duck)) {
            throw $this->createAccessDeniedException('Oh my god! You\'re about to kill your friend!');
        }

        if ($this->isCsrfTokenValid('delete' . $duck->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($duck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quack_index');
    }
}
