<?php

namespace App\Controller;

use App\Entity\Duck;
use App\Entity\Quack;
use App\Repository\DuckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuackRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
            if(($this->getUser() == $quack->getAuthor()) or $this->getUser()->getRoles()[0] == 'ROLE_MODERATOR') {
                $entityManager->remove($quack);
                $entityManager->flush();
                return $this->json(['message' => 'Your quack has been deleted.'], 200, [], ['groups' => 'quack']);
            }
            return $this->json(['message' => 'This is not your quack, you can\'t delete it.'], 200);
    }

    /**
     * @Route("/api/duck/{id}", name="update", methods={"PUT"})
     */
    public function updateDuck(Duck $duck, Request $request, EntityManagerInterface $entityManager): Response
    {
        $receivedJson = json_decode($request->getContent());

        // dd($this->getUser());
        if ($this->getUser() == $duck) {
            $duck->setLastName($receivedJson->lastName);
            $duck->setFirstName($receivedJson->firstName);

            $entityManager->persist($duck);
            $entityManager->flush();

            return $this->json($duck, 200, [], ['groups' => 'quack']);
        }
        return $this->json(['message' => 'You can\'t update this Duck, You are not '. $duck->getDuckName()], 200);
    }

    /**
     * @Route("/api/duck", name="create", methods={"POST"})
     */
    public function createDuck(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $receivedJson = $request->getContent();
        $pwd = json_decode($receivedJson)->password;
        $duck = new Duck();
        $pwd = $passwordEncoder->encodePassword($duck, $pwd);
        $duck = $serializer->deserialize($receivedJson, Duck::class, 'json');
        $duck->setPassword($pwd);
        $entityManager->persist($duck);
        $entityManager->flush();

        return $this->json($duck, 200, [], ['groups' => 'quack']);
    }

    /**
     * @Route("/api/whoami", name="whoami", methods={"GET"})
     */
    public function whoami(): Response
    {
        return $this->json($this->getUser(), 200, [], ['groups' => 'quack']);
    }
}
