<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Advert;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(EntityManagerInterface $em) {
        $advert = $em->getRepository(Advert::class)->findAll();
        return $this->render('home.html.twig', ['title' => 'Home page','AllAdvert'=> $advert]);
    }
}