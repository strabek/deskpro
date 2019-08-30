<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\TicketType;
use AppBundle\Service\APIService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    /**
     * @Route("/tickets", name="ticket_list", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri(rtrim($this->container->getParameter('api_tickets_endpoint'), '/'))
          // ->setQuery(['limit' => 5]) // Adding limit here cause a problem and throws an error: 400 Bad request
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Not found");
      }

      $tickets = json_decode($apiConn->getData())->data;

      return $this->render('default/ticket_list.html.twig', [
          'tickets' => $tickets,
      ]);
    }

    /**
     * @Route("/tickets/{id}", name="ticket_details", methods={"GET"})
     */
    public function detailsAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_tickets_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Ticket not found");
      }

      $ticket = json_decode($apiConn->getData())->data;

      return $this->render('default/ticket_details.html.twig', [
          'ticket' => $ticket,
      ]);
    }

    /**
     * @Route("/tickets/update/{id}", name="ticket_update", methods={"GET", "POST"})
     */
    public function updateAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_tickets_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Ticket not found");
      }

      $ticket = json_decode($apiConn->getData())->data;

      $form = $this->createForm(TicketType::class, $ticket);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $data = $form->getData();

          $apiConn
            ->setUri($this->container->getParameter('api_tickets_endpoint') . $id)
            ->setMethod('PUT')
            ->setData([
              'subject' => $data->subject
            ])
            ->connect()
          ;

          return $this->redirectToRoute('ticket_list');
      }
      
      return $this->render('default/ticket_update.html.twig', [
          'form' => $form->createView(),
      ]);
    }    

    /**
     * @Route("/tickets/delete/{id}", name="ticket_delete", methods={"GET"})
     */
    public function deleteAction(Request $request, $id)
    {
      $apiConn = $this->container->get('api_service');

      try {
        $apiConn
          ->setUri($this->container->getParameter('api_tickets_endpoint') . $id)
          ->connect()
        ;
      } catch (\Exception $e) {
        throw new NotFoundHttpException("Ticket not found");
      }

      $ticket = json_decode($apiConn->getData())->data;

      $apiConn
        ->setUri($this->container->getParameter('api_tickets_endpoint') . $ticket->id)
        ->setMethod('DELETE')
        ->connect()
      ;

      return $this->redirectToRoute('ticket_list');
    }
}
