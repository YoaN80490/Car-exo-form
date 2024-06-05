<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Car;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BrandController extends AbstractController
{
    #[Route('/brand', name: 'app_brand_browse')]
    public function index(BrandRepository $brandRepo): Response
    {
        $brandList = $brandRepo->findAll();
        return $this->render('brand/browse.html.twig', [
            "brandList" => $brandList
        ]);
    }

    #[Route('/brand/{id<\d+>}', name: 'app_brand_read', methods:"GET")]
    public function read(Brand $brand): Response
    {
        return $this->render('brand/read.html.twig', [
            "brand" => $brand
        ]);
    }

    #[Route('/brand/{id<\d+>}/edit', name: 'app_brand_edit', methods:['GET', 'POST'])]
    public function edit(EntityManagerInterface $em, Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('success', 'Marque modifié avec succès');

            return $this->redirectToRoute('app_brand_read', ['id' => $brand->getId()]);
        }

        return $this->render('brand/edit.html.twig', [
            'form' => $form,
            'brand' => $brand,
        ]);
    }

    #[Route('/brand/{id<\d+>}/delete', name: 'app_brand_delete', methods:'GET')]
    public function delete(EntityManagerInterface $em, Brand $brand, CarRepository $carRepo): Response
    {
        $carList = $carRepo->findAll();
        foreach($carList as $car){
            if ($car->getbrand()->getId() == $brand->getId()){
                $em->remove($car);
            }
        };
        $em->remove($brand);
        $em->flush();

        $this->addFlash('success', 'Suppression réussie');

        return $this->redirectToRoute('app_brand_browse');
    }

    #[Route('/brand/add', name: 'app_brand_add', methods:['GET', 'POST'])]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $brand = new Brand();

        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($brand);
            $em->flush();

            $this->addFlash('success', 'Marque ajouté avec succès');
            
            return $this->redirectToRoute('app_brand_browse');
        }

        return $this->render('brand/add.html.twig', [
            'form' => $form,
            'brand' => $brand,
        ]);
    }
}
