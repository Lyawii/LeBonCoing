<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Category;
use App\Form\AdvertType;
use App\Form\EditAdvertType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractController
{
    /**
     * @Route("/advert/{nameUser}/{name}", name="JsonAdvertByName")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $nameUser
     * @param $name
     * @return JsonResponse
     */
    public function AdvertJson(EntityManagerInterface $em, Request $request, $nameUser, $name) {

        $advert = $em->getRepository(Advert::class)->findByName($name, $em->getRepository(User::class)->findByPseudo($nameUser));

        if (count($advert) == 0)
            return $this->redirectToRoute('home');

        return new JsonResponse($this->convertOneAdvert($advert[0]));
    }
    /**
     * @Route("/adverts/{nameUser}/{name}", name="AdvertByName")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $nameUser
     * @param $name
     * @return Response;
     */
    public function AdvertByName(EntityManagerInterface $em, Request $request, $nameUser, $name) {
        $advert = $em->getRepository(Advert::class)->findByName($name, $em->getRepository(User::class)->findByPseudo($nameUser)[0]->getId());
        if ($request->isXmlHttpRequest())
            return new JsonResponse($this->convertOneAdvert($advert));
        return $this->render('Advert/AdvertOne.html.twig', ['title' => $name, 'AllAdvert' => $advert]);
    }

    /**
     * @Route("/advert/{nameUser}", name="AllAdvertOfUserPseudo")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $name
     * @return Response
     */
    public function AllAdvertOfUser(EntityManagerInterface $em, Request $request, $nameUser) {
        $adverts = $em->getRepository(Advert::class)->findAllByIdCreator(
            $em->getRepository(User::class)->findByPseudo($nameUser)
        );
        if ($request->isXmlHttpRequest())
            return new JsonResponse($this->convertArrayOnJson($adverts[0]));
        return $this->render('Advert/AdvertAll.html.twig', ['title' => $nameUser, 'AllAdvert' => $adverts]);
    }
    /**
     * @param $advert
     * @return array
     */
    private function convertOneAdvert(Advert $advert) {
        $imageContent = [];
        foreach ($advert->getImages() as $image) {
            $imageContent[] = $image->getImagePath();
        }
        return [
            'id' => $advert->getId(),
            'name' => $advert->getName(),
            'description' => $advert->getDescription(),
            'price' => $advert->getPrice(),
            'category' => $advert->getCategory(),
            'user' => $advert->getUser()->getPseudo(),
            'image' => $advert,
            'created_at' => $advert->getCreatedAt()
        ];
    }
    /**
     * @Route("/newAdvert", name="createAdvert")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(EntityManagerInterface $em, Request $request) {
        if (!$this->getUser())
            return $this->redirectToRoute('AllAdvert');
        if ($request->isMethod('POST')) {
            $advert = new Advert();

            $advert
                ->setName($request->request->get('advert')['name'])
                ->setUser($em->getRepository(User::class)->find($this->getUser()->getId()))
                ->setDescription($request->request->get('advert')['description'])
                ->setCategory($em->getRepository(Category::class)->find($request->request->get('advert')['category']))
                ->setCreatedAt(new \DateTime())
                ->setCity($request->request->get('advert')['city'])
                ->setAdress($request->request->get('advert')['adress'])
                ->setPostalcode($request->request->get('advert')['postalcode'])
                ->setPrice($request->request->get('advert')['price']);

            $currentDir = getcwd();
            $filesystem = new Filesystem();
            if (!$filesystem->exists($currentDir . '/Image/' . 'creator\'s name/' . $advert->getName()))
                $filesystem->mkdir($currentDir . '/Image/' . 'creator\'s name/' . $advert->getName(), 0700);
            foreach ($request->files->get('advert')['images'] as $image) {
                $image->move($currentDir . '/Image/' . 'creator\'s name/' . $advert->getName(), $image->getClientOriginalName());
                $newImage = new Image();
                $newImage
                    ->setImagePath('/Image/' . 'creator\'s name/' . $advert->getName() . '/' . $image->getClientOriginalName())
                    ->setAdvert($advert);
                $em->persist($newImage);
            }
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('home');
        } else {
            $form = $this->createForm(AdvertType::class)->handleRequest($request);

        }

        return $this->render('Advert/Advert.html.twig', ['title' => 'Create of a new advert.', 'form' => $form->createView()]);
    }
    /**
     * @Route("/search/{search}", name="searchAdvert")
     *
     * @param EntityManagerInterface $em
     * @param $search
     * @return Response
     */
    public function findAllByName(EntityManagerInterface $em, $search) {
        $advert = $em->getRepository(Advert::class)->findAllByName($search);
        return $this->render('Advert/AdvertAll.html.twig', ['title' => $search, 'AllAdvert' => $advert]);
    }
    /**
     * @Route("/update/{name}", name="updateAdvert")
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $name
     * @return RedirectResponse|Response
     */
    public function update(EntityManagerInterface $em, Request $request, $name) {
        $user = $em->getRepository(User::class)->find($this->getUser()->getId());
        $form = $this->createForm(EditAdvertType::class);
        $advert = $em->getRepository(Advert::class)->findByName($name, $user->getId());

        if ($request->isMethod('POST')) {
            $advert[0]
                ->setName($request->request->get('edit_advert')['name'])
                ->setUser($em->getRepository(User::class)->find($this->getUser()->getId()))
                ->setDescription($request->request->get('edit_advert')['description'])
                ->setCategory($em->getRepository(Category::class)->find($request->request->get('edit_advert')['category']))
                ->setCity($request->request->get('edit_advert')['city'])
                ->setAdress($request->request->get('edit_advert')['adress'])
                ->setPostalcode($request->request->get('edit_advert')['postalcode'])
                ->setPrice($request->request->get('edit_advert')['price']);

            $em->flush();
            return $this->redirectToRoute('AdvertByName', ['nameUser' => $user->getPseudo(), 'name' => $advert[0]->getName()]);
        }
        $form->handleRequest($request);
        $CategoryName = [];
        foreach ($em->getRepository(Category::class)->findAll() as $it)
            $CategoryName[$it->getName()] = $it->getId();
        $form->add('category', ChoiceType::class, [
            'choices' => $CategoryName
        ]);
        if ($form->isSubmitted() && $form->isValid()) {
            $UpdateAdvert = $form->getData();
            $advert[0]
                ->setName($UpdateAdvert->getName())
                ->setDescription($UpdateAdvert->getDescription())
                ->setEtat($UpdateAdvert->getEtat())
                ->setPrix($UpdateAdvert->getPrix())
                ->setCategory($em->getRepository(Category::class)->find($UpdateAdvert->getCategory()));
            $em->flush();
            return $this->redirectToRoute('AdvertByName', ['nameUser' => $user->getPseudo(), 'name' => $advert[0]->getName()]);
        }
        return $this->render('Advert/editAdvert.html.twig', ['title' => 'Changeover', 'form' => $form->createView()]);
    }
}