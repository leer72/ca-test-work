<?php

namespace App\Controller;

use App\Form\CalculationFormType;
use App\Form\Model\CalculationFormModel;
use App\Repository\CalculationRepository;
use App\Service\CalculationService;
use App\Service\CalculatorProducer;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculatorController extends AbstractController
{
    public function __construct(
        private readonly CalculatorProducer $producer,
        private readonly CalculationService $calculationService,
        private readonly CalculationRepository $calculationRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException|
     * @throws Exception
     */
    #[Route(
        path: '/',
        name: 'app_calculate',
        methods: ['POST', 'GET'],
    )]
    public function calculate(Request $request): Response
    {
        $result = null;
        $showValidationError = true;
        $form = $this->createForm(CalculationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('add')->isClicked()) {
                /** @var CalculationFormModel $calculation */
                $calculation = $form->getData();

                $msg = [
                    'argumentA' => $calculation->argumentA,
                    'operation' => $calculation->operation,
                    'argumentB' => $calculation->argumentB,
                ];

                $this->producer->getProducer()->publish(serialize($msg));

                $result = 'Вычисление добавлено в очередь';
            }
        }

        if ($form->getClickedButton() && 'show' === $form->getClickedButton()->getName()) {
            $calculation = $this->calculationRepository->findFirstCalculation();

            if (!is_null($calculation)) {
                $result = sprintf(
                    '%f %s %f = %f',
                    $calculation->getArgumentA(),
                    $calculation->getOperation()->value,
                    $calculation->getArgumentB(),
                    $calculation->calculate(),
                );

                $this->calculationService->delete($calculation);
            } else {
                $result = 'В очереди нет вычислений';
            }

            $showValidationError = false;
        }

        return $this->render('calculator.html.twig', [
            'calculatorForm' => $form->createView(),
            'result' => $result,
            'showValidationError' => $showValidationError,
        ]);
    }
}
