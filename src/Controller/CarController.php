<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CarController extends AbstractController
{
    #[Route('/', name: 'app_car_browse')]
    public function index(CarRepository $carRepo): Response
    {
        $carList = $carRepo->findAll();
        return $this->render('car/browse.html.twig', [
            "carList" => $carList
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_car_read', methods:"GET")]
    public function read(Car $car): Response
    {
        return $this->render('car/read.html.twig', [
            "car" => $car
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_car_edit', methods:['GET', 'POST'])]
    public function edit(EntityManagerInterface $em, Request $request, Car $car): Response
    {
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('success', 'Voiture modifié avec succès');

            return $this->redirectToRoute('app_car_read', ['id' => $car->getId()]);
        }

        return $this->render('car/edit.html.twig', [
            'form' => $form,
            'car' => $car,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'app_car_delete', methods:'GET')]
    public function delete(EntityManagerInterface $em, Car $car): Response
    {
        $em->remove($car);

        $em->flush();

        $this->addFlash('success', 'Suppression réussie');

        return $this->redirectToRoute('app_car_browse');
    }

    #[Route('/add', name: 'app_car_add', methods:['GET', 'POST'])]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $car = new Car();

        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($car);
            $em->flush();

            $this->addFlash('success', 'Voiture ajouté avec succès');
            
            return $this->redirectToRoute('app_car_browse');
        }

        return $this->render('car/add.html.twig', [
            'form' => $form,
            'car' => $car,
        ]);
    }
}
