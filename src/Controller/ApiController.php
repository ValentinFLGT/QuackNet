<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Repository\QuackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/quack", name="api_quack_index", methods={"GET"})
     */
    public function index(QuackRepository $quackRepository): Response
    {
        return $this->json($quackRepository->findAll(), 200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/{id}", name="api_quack_detail", methods={"GET"})
     */
    public function detail(QuackRepository $quackRepository, Quack $quack): Response
    {
        return $this->json($quackRepository->find(['id' => $quack->getId()]) ,200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/search/{tag}", name="api_quack_search", methods={"GET"})
     */
    public function search(QuackRepository $quackRepository, $tag): Response
    {
        return $this->json($quackRepository->searchByTag($tag) ,200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/quack/{id}", name="api_quack_delete", methods={"DELETE"})
     */
    public function delete(QuackRepository $quackRepository, Quack $quack): Response
    {
        return $this->json($quackRepository->find(['id' => $quack->getId()]) ,200, [], ['groups' => 'quack']);
    }
}
