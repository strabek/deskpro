<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\OrganizationType;
use AppBundle\Service\APIService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrganizationController extends Controller
{
    /**
     * @Route("/organizations", name="organization_list", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri(rtrim($this->container->getParameter('api_organizations_endpoint'), '/'))
          ->setQuery(['limit' => $this->container->getParameter('api_default_limit')])
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Not found");
      }

      $organizations = json_decode($apiConn->getData())->data;

      return $this->render('default/organization_list.html.twig', [
          'organizations' => $organizations,
      ]);
    }

    /**
     * @Route("/organizations/{id}", name="organization_details", methods={"GET"})
     */
    public function detailsAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_organizations_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Organization not found");
      }

      $organization = json_decode($apiConn->getData())->data;

      return $this->render('default/organization_details.html.twig', [
          'organization' => $organization,
      ]);
    }

    /**
     * @Route("/organizations/update/{id}", name="organization_update", methods={"GET", "POST"})
     */
    public function updateAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_organizations_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Organization not found");
      }

      $organization = json_decode($apiConn->getData())->data;

      $form = $this->createForm(OrganizationType::class, $organization);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $data = $form->getData();

          $apiConn
            ->setUri($this->container->getParameter('api_organizations_endpoint') . $id)
            ->setMethod('PUT')
            ->setData([
              'name' => $data->name
            ])
            ->connect()
          ;

          return $this->redirectToRoute('organization_list');
      }
      
      return $this->render('default/organization_update.html.twig', [
          'form' => $form->createView(),
      ]);
    }    

    /**
     * @Route("/organizations/delete/{id}", name="organization_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_organizations_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Organization not found");
      }

      $organization = json_decode($apiConn->getData())->data;

      $apiConn
        ->setUri($this->container->getParameter('api_organizations_endpoint') . $organization->id)
        ->setMethod('DELETE')
        ->connect()
      ;

      return $this->redirectToRoute('organization_list');
    }
}
