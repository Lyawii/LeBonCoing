<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/create", name="createUser")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder) {

        $form = $this->createForm(UserType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            // redirect on the login route
            return $this->redirectToRoute('login');
        }
        return $this->render('User/User.html.twig', ['title' => 'Create a new user.', 'form' => $form->createView()]);
    }
    /**
     * @Route("/users/{name}", name="UserFindByName")
     * @param EntityManagerInterface $em
     * @param string $name
     * @return Response
     */
    public function getAccount(EntityManagerInterface $em, string $name) {

        $user = $em->getRepository(User::class)->findByPseudo($name);

        return $this->render('UserOne.html.twig', ['title' => $name, 'information' => $user]);
    }
    /**
     * @Route("/user", name="UserAccount")
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function findAccount(EntityManagerInterface $em) {
        if (!$this->getUser())
            return $this->redirectToRoute('home');
        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        return $this->render('User/UserOne.html.twig', ['title' => 'Current Account', 'user' => $user]);
    }
    /**
     * @Route("/user/edit", name="EditAccountData")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(EntityManagerInterface $em, Request $request) {

        if (!$this->getUser())
            return $this->redirectToRoute('home');

        $form = $this->createForm(UserEditType::class);

        if ($request->isMethod('post'))

            $form->handleRequest($request);
        else

            $form->setData($em->getRepository(User::class)->find($this->getUser()->getId()));

        if ($form->isSubmitted() && $form->isValid()) {

            $newData = $form->getData();
            $user = $em->getRepository(User::class)->find($this->getUser()->getId());
            $user
                ->setFirstname($newData->getFirstName())
                ->setLastname($newData->getLastName())
                ->setInformation($newData->getInformation());
            $em->flush();

            return $this->redirectToRoute('UserAccount');
        }

        return $this->render('User/editAccount.html.twig', ['title' => 'Change your personal information.', 'form' => $form->createView()]);
    }
}