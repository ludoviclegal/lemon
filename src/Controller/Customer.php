<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CustomerRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\CountryRepository;
//use Symfony\Component\HttpClient\HttpClient;
//use Symfony\Component\HttpClient\CurlHttpClient;

class Customer extends AbstractController
{
    /**
     * @Route("/customer/subscribe", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, CountryRepository $countryRepository)
    {
        
        // recup pays du client
        $customerCountry = $this->getCustomerCountry($request->getClientIp());
        $country = $countryRepository->findOneByCountryCode($customerCountry->countryCode);

        $form = $this->createForm(CustomerRegistrationFormType::class, null, ['country' => $country]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Customer $customer */
            $customer = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $customer->setPassword($passwordEncoder->encodePassword(
                $customer,
                $customer->getPassword()
            ));
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirectToRoute('app_register_confirm');
        }

        return $this->render('customer/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/customer/registerConfirm", name="app_register_confirm")
     */
    public function registerConfirm(Request $request)
    {
        return $this->render('customer/registerConfirm.html.twig');
    }

    public function getCustomerCountry(string $ip)
    {
        $url = 'http://ip-api.com/json';
        if ($ip = '127.0.0.1') {
            $request = '';
        } else {
            $request = json_encode(['ip' => $ip]);
        }
        $request = '';
        $curl = curl_init();                          //Initiate cURL.
        $arrayRequest = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            //CURLOPT_VERBOSE => 1,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => [
                //"Cache-Control: no-cache",
                "Content-Type: application/json",
            ]
        ];
        curl_setopt_array($curl, $arrayRequest);
        $geoloc = curl_exec($curl);//Execute the request       
        $geoloc = json_decode($geoloc);
        
        return $geoloc;
    }
}