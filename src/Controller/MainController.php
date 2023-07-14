<?php

namespace App\Controller;

use App\Entity\ReceiptRegistration;
use App\Repository\ReceiptRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("inscrie-te", name="signUp")
     */
    public function signUp(): Response
    {
        return $this->render('main/signup.html.twig');
    }

    /**
     * @Route("/castigatori", name="winners")
     */
    public function winners(): Response
    {
        return $this->render('main/winners.html.twig');
    }

    /**
     * @Route("/politica-cookies", name="cookies")
     */
    public function cookies(): Response
    {
        return $this->render('main/cookies.html.twig');
    }

    /**
     * @Route("/regulament", name="rules")
     */

    public function rules(): Response
    {
        return $this->render('main/rules.html.twig');
    }

    /**
     * @Route("/termeni-si-conditii", name="terms")
     */
    public function terms(): Response
    {
        return $this->render('main/terms.html.twig');
    }

    /**
     * @Route("/produse", name="products")
     */
    public function products(): Response
    {
        return $this->render('main/products.html.twig');
    }

    /**
     * @Route("/register-campaign", name="register-campaign")
     */
    public function registerCampaign(Request $request, EntityManagerInterface $em, ReceiptRegistrationRepository  $receiptRegistrationRepository): Response
    {
        $receiptRegistration = new ReceiptRegistration();
        $currentDate = new \DateTime();
        $campaignStartDate = new \DateTime($this->getParameter("campaign_start_date"));
        $campaignEndDate = new \DateTime($this->getParameter("campaign_end_date"));

        $jsonResponse = [
            'success' => true,
            'errors' => [],
            'status' => 1,
            'prize' => 0,
            'messageStatus' => '',
            'message' => ''
        ];

        if($currentDate < $campaignStartDate) {
            $jsonResponse['messageStatus'] = 'precampanie';
            $jsonResponse['message'] = 'Campania "TUC Neversea" nu a început încă. Mai ai un picuț de răbdare. Ca să te înscrii în campanie, trebuie să cumperi produse participante în valoare de minimum 15 lei, pe același bon fiscal, în perioada 12.07.2023 – 12.08.2023, și să înscrii bonul pe www.oreopromo.ro.';
            return new JsonResponse($jsonResponse, 200);
        } else if($currentDate >= $campaignEndDate) {
            $jsonResponse['messageStatus'] = 'postcampanie';
            $jsonResponse['message'] = 'Uf, ai întârziat puțin. Campania "TUC Neversea" s-a încheiat pe data de 12.08.2023. Îți mulțumim pentru participare și te așteaptăm la următoarele campanii promoționale.';
            return new JsonResponse($jsonResponse, 200);
        }

        $idNet = microtime(true) * 1000;

        $telefon = $request->get('phone');
        $nrBon = $request->get('receiptNumber');
        $dataBon = $request->get('date');
        $acordTermeni = $request->get('terms');
        $acordVarsta = $request->get('age');
        $acordRegulament = $request->get('rule');
        $magazin = $request->get('store');

        if (empty($telefon) || empty($nrBon) || empty($dataBon) || empty($acordTermeni) || empty($acordVarsta) || empty($acordRegulament) || empty($magazin)) {
            $jsonResponse['messageStatus'] = 'incorect';
            $jsonResponse['message'] = 'Lipsesc unul sau mai multi parametri.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptDateValidator = Validation::createValidator();
        $constraintReceiptDate = new Assert\Regex([
            'pattern' => '/^\d{2}\/\d{2}\/\d{4}$/',
            'message' => 'Data bonului nu este în formatul așteptat (zz/ll/aaaa).',
        ]);
        $errorsReceiptDate = $receiptDateValidator->validate($dataBon, $constraintReceiptDate);

        if (count($errorsReceiptDate) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Data bonului nu este în formatul așteptat (zz/ll/aaaa).';
            return new JsonResponse($jsonResponse, 200);
        }

        $phoneNumberValidator = Validation::createValidator();
        $constraintPhoneNumber = new Assert\Regex([
            'pattern' => '/^07\d{8}$/',
            'message' => 'Numărul de telefon nu este un numar valid din Romania.'
        ]);
        $errorsPhoneNumber = $phoneNumberValidator->validate($telefon, $constraintPhoneNumber);

        if (count($errorsPhoneNumber) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Numărul de telefon nu este un numar valid din Romania.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptCodeValidator = Validation::createValidator();
        $constraintReceiptCode = new Assert\Regex([
            'pattern' => '/^\d+$/',
            'message' => 'Codul bonului trebuie să conțină doar cifre.',
        ]);
        $errorsReceiptCode = $receiptCodeValidator->validate($nrBon, $constraintReceiptCode);

        if (count($errorsReceiptCode) > 0) {
            $jsonResponse['messageStatus'] = 'INVALIDPARAMS';
            $jsonResponse['message'] = 'Codul bonului trebuie sa contina doar cifre.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptWeekCount = $receiptRegistrationRepository->weekReceiptsCounter($telefon, $currentDate);
        if($receiptWeekCount >= 10) {
            $lastWeekStart = (new \Datetime($this->getParameter("campaign_end_date")))->format('- 7 days');
            $endDate = new \Datetime($this->getParameter("campaign_end_date"));

            if($currentDate >= $lastWeekStart && $currentDate <= $endDate) {
                $jsonResponse['messageStatus'] = 'blocat_corecte';
                $jsonResponse['message'] = 'Campania "TUC Neversea” se încheie săptămâna aceasta și se pare că ai atins limita de înscrieri. Mulțumim pentru participare. Verifică rezultatul tragerii la sorți și vezi dacă te afli printre câștigători!';
                return new JsonResponse($jsonResponse, 200);
            }

            $jsonResponse['messageStatus'] = 'blocat_corecte';
            $jsonResponse['message'] = 'Ne bucurăm că-ți plac atât de mult produsele noastre, dar se pare că ai atins limita de zece înscrieri într-o săptămână de campanie. Mulțumim pentru participare și te așteptăm pentru noi înscrieri săptămâna viitoare.';
            return new JsonResponse($jsonResponse, 200);
        }

        $values = [
            'nrBon' => $nrBon,
            'dataBon' => $dataBon,
            'telefon' => $telefon,
            'magazin' => $magazin
        ];

        $existingValues = $receiptRegistrationRepository->findBy($values);

        if($existingValues) {
            $jsonResponse['messageStatus'] = 'dubla';
            $jsonResponse['message'] = 'Acest număr de bon fiscal a mai fost înscris în campanie. Pentru a putea participa, te rugăm să introduci alt bon fiscal.';
            return new JsonResponse($jsonResponse, 200);
        }

        $receiptRegistration->setTelefon($telefon);
        $receiptRegistration->setIdNet($idNet);
        $receiptRegistration->setNrBon($nrBon);
        $receiptRegistration->setDataBon($dataBon);
        $receiptRegistration->setAcordTermeni($acordTermeni);
        $receiptRegistration->setAcordVarsta($acordVarsta);
        $receiptRegistration->setAcordRegulament($acordRegulament);
        $receiptRegistration->setMagazin($magazin);
        $receiptRegistration->setSubmittedAt($currentDate);

        try {
            $em->persist($receiptRegistration);
            $em->flush();
        } catch (ORMException $exception) {
            return new Response($exception->getMessage());
        }

        $jsonResponse['messageStatus'] = 'corect';
        $jsonResponse['message'] = 'Felicitări! Te-ai înscris cu succes în campania "Oreo of the Day". Îți reamintim că, pentru mai multe șanse de câștig, poți înregistra până la 10 bonuri fiscale pe săptămână de campanie . Și nu uita să păstrezi pentru validare bonul fiscal! Succes!';
        return new JsonResponse($jsonResponse, 200);
    }

}
