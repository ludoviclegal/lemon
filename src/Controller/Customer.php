<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CustomerRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Customer extends AbstractController
{
    /**
     * @Route("/customer/subscribe")
     */
    public function register(Request $request)
    {
        $form = $this->createForm(CustomerRegistrationFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            /*$customer->setPassword($passwordEncoder->encodePassword(
                $customer,
                $customer->getPassword()
            ));*/
            $customer = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();
        }

        return $this->render('customer/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}