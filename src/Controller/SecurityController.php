<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login() {
        if ($this->getUser())
            return $this->redirectToRoute('home');
        return $this->render('Security/login.html.twig', ['title' => 'login']);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {}
}