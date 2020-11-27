<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Quack;
use App\Form\CommentType;
use App\Form\QuackType;
use App\Form\SearchType;
use App\Repository\CommentRepository;
use App\Repository\QuackRepository;
use App\Service\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class QuackController extends AbstractController
{
    /**
     * @Route("/quack", name="quack_index", methods={"GET","POST"})
     * @param QuackRepository $quackRepository
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @return Response
     */
    public function new(UserInterface $user, QuackRepository $quackRepository, Request $request, UploaderHelper $uploaderHelper): Response
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

            return $this->redirectToRoute('quack_index');
        }

        $searchForm = $this->createFormBuilder()
            ->add('q')
            ->setMethod('GET')
            ->getForm();

        $searchForm->handleRequest($request);

        $data = $quackRepository->findAllDesc();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {

            $author = $searchForm->get('q')->getData();

            $data = $quackRepository->search($author);

        }

        return $this->render('quack/index.html.twig', [
            'quacks' => $data,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/quack/{id}", name="quack_show", methods={"GET","POST"})
     * @param Quack $quack
     * @param CommentRepository $commentRepository
     * @param Request $request
     * @return Response
     */
    public function show(Quack $quack, CommentRepository $commentRepository, Request $request): Response
    {
        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime('now'));
        $comment->setAuthor($this->getUser());
        $comment->setQuack($quack);
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('quack_show', ['id' => $quack->getId()]);
        }
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
            'comments' => $commentRepository->findBy(array(), array('createdAt' => 'DESC')), 'comment' => $comment,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route("/quack/{id}/edit", name="quack_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Quack $quack
     * @param UploaderHelper $uploaderHelper
     * @return Response
     */
    public function edit(Request $request, Quack $quack, UploaderHelper $uploaderHelper): Response
    {
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if (!$this->isGranted("ROLE_MODERATOR")) {
            $this->denyAccessUnlessGranted("DELETE_QUACK", $quack);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['image']->getData();

            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadQuackImage($uploadedFile);
                $quack->setFileName($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quack_show', ['id' => $quack->getId()]);
        }

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form->createView()
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
        if (!$this->isGranted("ROLE_MODERATOR")) {
            $this->denyAccessUnlessGranted("DELETE_QUACK", $quack);
        }

        if ($this->isCsrfTokenValid('delete' . $quack->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('duck_show', ['id' => $quack->getAuthor()->getId()]);
    }
}
