<?php

namespace App\Controller;

use App\Entity\Quack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuackRepository;


class ApiController extends AbstractController
{
    /**
     * @Route("/api/quack", name="index", methods={"GET"})
     */
    public function index(QuackRepository $quackRepository): Response
    {
        return $this->json($quackRepository->findAll(), 200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/search/{tag}", name="tag", methods={"GET"})
     */
    public function search(QuackRepository $quackRepository, $tag): Response
    {
        return $this->json($quackRepository->searchByTag($tag), 200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/{id}", name="show", methods={"GET"})
     */
    public function show(QuackRepository $quackRepository, Quack $quack): Response
    {
        return $this->json($quackRepository->find(['id' => $quack->getId()]), 200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/{id}", name="delete", methods={"DELETE"})
     */
    public function deleteProduct(EntityManagerInterface $entityManager, Quack $quack): Response
    {
            $entityManager->remove($quack);
            $entityManager->flush();

            return $this->json(['message' => 'Your quack has been deleted.'], 200, [], ['groups' => 'quack']);
    }

//    /**
//     * @Route("/api/quack/test/{tag}", name="test", methods={"GET"})
//     */
//    public function test(TagRepository $tagRepository, Tag $tag)
//    {
//        $tag = $tagRepository->find($tag->getId());
//        $quacks = $tag->getQuacks();
//        return $this->json($quacks, 200, [], ['groups' => 'quack']);
//    }
}
