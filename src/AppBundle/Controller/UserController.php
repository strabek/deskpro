<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\UserType;
use AppBundle\Service\APIService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri(rtrim($this->container->getParameter('api_users_endpoint'), '/'))
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Not found");
      }

      $users = json_decode($apiConn->getData())->data;

      return $this->render('default/user_list.html.twig', [
          'users' => $users,
      ]);
    }

    /**
     * @Route("/users/{id}", name="user_details", methods={"GET"})
     */
    public function detailsAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_users_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("User not found");
      }

      $user = json_decode($apiConn->getData())->data;

      return $this->render('default/user_details.html.twig', [
          'user' => $user,
      ]);
    }

    /**
     * @Route("/users/update/{id}", name="user_update", methods={"GET", "POST"})
     */
    public function updateAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn  
          ->setUri($this->container->getParameter('api_users_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("User not found");
      }

      $user = json_decode($apiConn->getData())->data;

      $form = $this->createForm(UserType::class, $user);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $data = $form->getData();

          $apiConn
            ->setUri($this->container->getParameter('api_users_endpoint') . $id)
            ->setMethod('PUT')
            ->setData([
              'name' => $data->name,
            ])
            ->connect()
          ;

          return $this->redirectToRoute('user_list');
      }
      
      return $this->render('default/user_update.html.twig', [
          'form' => $form->createView(),
      ]);
    }    

    /**
     * @Route("/users/delete/{id}", name="user_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_users_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("User not found");
      }

      $user = json_decode($apiConn->getData())->data;

      $apiConn
        ->setUri($this->container->getParameter('api_users_endpoint') . $user->id)
        ->setMethod('DELETE')
        ->connect()
      ;

      return $this->redirectToRoute('user_list');
    }
}
