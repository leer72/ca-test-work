<?php

namespace App\Controller;

use App\Form\CalculationFormType;
use App\Form\Model\CalculationFormModel;
use App\Repository\CalculationRepository;
use App\Service\CalculationService;
use App\Service\SendMessageInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalculatorController extends AbstractController
{
    public function __construct(
        private readonly CalculationService $calculationService,
        private readonly CalculationRepository $calculationRepository,
        private readonly SendMessageInterface $sendMessage,
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

        if ($form->isSubmitted() && $form->isValid() && $form->get('add')->isClicked()) {
            $result = $this->checkCalculations($form);
        }

        if ($form->getClickedButton() && 'show' === $form->getClickedButton()->getName()) {
            $result = $this->findCalculationsResult();
            $showValidationError = false;
        }

        return $this->render('calculator.html.twig', [
            'calculatorForm' => $form->createView(),
            'result' => $result,
            'showValidationError' => $showValidationError,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function checkCalculations(FormInterface $form): array
    {
        /** @var CalculationFormModel $calculationData */
        $calculationData = $form->getData();

        $calculation = $this->calculationRepository->findCalculationByArgumentsAndOperator(
            argumentA: $calculationData->argumentA,
            argumentB: $calculationData->argumentB,
            operation: $calculationData->operation,
        );

        if (!is_null($calculation)) {
            $result[] = $calculation->getResult()
                ? sprintf(
                    '%f %s %f = %f',
                    $calculation->getArgumentA(),
                    $calculation->getOperation()->value,
                    $calculation->getArgumentB(),
                    $calculation->getResult()
                )
                : 'Вычисление уже в очереди. Ожидайте расчета.';
        } else {
            $calculation = $this->calculationService->create(
                argumentA: $calculationData->argumentA,
                argumentB: $calculationData->argumentB,
                operation: $calculationData->operation,
            );

            $msg = [
                CalculationService::CALCULATION_ID_ARRAY_KEY => $calculation->getId(),
            ];

            $this->sendMessage->sendMessage($msg);

            $result[] = 'Вычисление добавлено в очередь';
        }

        return $result;
    }

    private function findCalculationsResult(): array
    {
        $calculations = $this->calculationRepository->findNoShownCalculations();

        if (!empty($calculations)) {
            foreach ($calculations as $calculation) {
                if (!is_null($calculation->getResult())) {
                    $result[] = sprintf(
                        '%f %s %f = %f',
                        $calculation->getArgumentA(),
                        $calculation->getOperation()->value,
                        $calculation->getArgumentB(),
                        $calculation->getResult()
                    );

                    $calculation->setIsShown(true);
                }
            }

            $this->calculationRepository->flush();
        }

        if (empty($result)) {
            $result[] = 'Нет готовых результатов';
        }

        return $result;
    }
}
