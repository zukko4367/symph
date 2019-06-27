<?php

namespace App\Controller;


use App\Entity\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;


class ApplicationController extends AbstractController {

  public function all()
  {
    $repository = $this->getDoctrine()->getRepository(Application::class);

    $applicationsQuery = $repository->createQueryBuilder('application')
      ->orderBy('application.created', 'ASC')
      ->orderBy('application.status', 'ASC')
      ->getQuery();

    $applications = $applicationsQuery->getResult();

    return $this->render('application/all.html.twig', [
      'applications' => $applications,
    ]);
  }

  public function view($id, Request $request)
  {
    $application = $this->getDoctrine()
      ->getRepository(Application::class)
      ->find($id);

    if (!$application) {
      throw $this->createNotFoundException(
        "Заявка с номером #$id не найдена!"
      );
    }

    $form = $this->createFormBuilder($application)
      ->add('status', ChoiceType::class, [
        'choices'  => array_flip(Application::getStatuses())
      ])
      ->add('save', SubmitType::class, ['label' => 'Изменить статус'])
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($application);
      $entityManager->flush();
      return $this->redirectToRoute('application_list');
    }

    return $this->render('application/view.html.twig', [
      'application' => $application,
      'status_edit_form' =>  $form->createView()
    ]);

  }

  public function new(Request $request)
  {
    $application = new Application();

    $form = $this->createFormBuilder($application)
      ->add('title', TextType::class)
      ->add('text', TextareaType::class)
      ->add('save', SubmitType::class, ['label' => 'Cоздать '])
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      /** @var $application Application*/
      $application = $form->getData();
      $application->setStatus(Application::APPLICATION_STATUS_NEW);
      $application->setCreated(time());
      if ($form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($application);
        $entityManager->flush();
        return $this->redirectToRoute('application_list');
      } else {
        $form->addError(new FormError('Не заполнены поля'));
      }
    }

    return $this->render('application/new.html.twig', [
      'form' => $form->createView(),
    ]);
  }
}
