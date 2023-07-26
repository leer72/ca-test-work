<?php

namespace App\Tests\Functional;

use App\Enum\CalculatorOperationsEnum;
use App\Tests\AbstractTest;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use PhpAmqpLib\Message\AMQPMessage;

class FormTest extends AbstractTest
{
    private const SEND_JOB_SUCCESS_MESSAGE = 'Вычисление добавлено в очередь';

    private const OPERATION_FORM_MAP = [
        '+' => 0,
        '-' => 1,
        '*' => 2,
        '/' => 3,
    ];

    /**
     * @throws \Exception
     */
    #[NoReturn]
    public function testShowForm(): void
    {
        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);
    }

    /**
     * @throws \Exception
     */
    #[NoReturn]
    public function testSendValidValues(): void
    {
        $this->clearDatabase();

        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);

        $formData = [
            'argumentA' => $this->getFaker()->randomFloat(),
            'argumentB' => $this->getFaker()->randomFloat(),
            'operation' => (string) $this->getFaker()->randomElement(self::OPERATION_FORM_MAP),
        ];

        $values['calculation_form'] = array_merge($values['calculation_form'], $formData);

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_add')
            ->form(
                $values,
                $form->getMethod(),
            );

        $this->client->submit($form);

        $message = $this->client->getCrawler()->filter('.alert-success')->text();

        self::assertSame(self::SEND_JOB_SUCCESS_MESSAGE, $message);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function testSendInvalidArgumentA(): void
    {
        $this->clearDatabase();

        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);

        $formData = [
            'argumentA' => 'A',
            'argumentB' => $this->getFaker()->randomFloat(),
            'operation' => (string) $this->getFaker()->randomElement(self::OPERATION_FORM_MAP),
        ];

        $values['calculation_form'] = array_merge($values['calculation_form'], $formData);

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_add')
            ->form(
                $values,
                $form->getMethod(),
            );

        $this->client->submit($form);

        $errors = $this->client->getCrawler()->filter('.alert-danger')->count();

        self::assertGreaterThan(0, $errors);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function testSendInvalidArgumentB(): void
    {
        $this->clearDatabase();

        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);

        $formData = [
            'argumentA' => $this->getFaker()->randomFloat(),
            'argumentB' => 'B',
            'operation' => (string) $this->getFaker()->randomElement(self::OPERATION_FORM_MAP),
        ];

        $values['calculation_form'] = array_merge($values['calculation_form'], $formData);

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_add')
            ->form(
                $values,
                $form->getMethod(),
            );

        $this->client->submit($form);

        $errors = $this->client->getCrawler()->filter('.alert-danger')->count();

        self::assertGreaterThan(0, $errors);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function testGetResult(): void
    {
        $this->clearDatabase();

        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);

        $argumentA = 1.0;
        $argumentB = 2.0;
        $operation = '+';

        $formData = [
            'argumentA' => $argumentA,
            'argumentB' => $argumentB,
            'operation' => (string) self::OPERATION_FORM_MAP[$operation],
        ];

        $values['calculation_form'] = array_merge($values['calculation_form'], $formData);

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_add')
            ->form(
                $values,
                $form->getMethod(),
            );

        $this->client->submit($form);

        self::getContainer()->get('app.consumer.calculator')->execute(
            msg: new AMQPMessage(
                serialize(
                    [
                        'argumentA' => $argumentA,
                        'operation' => CalculatorOperationsEnum::from($operation),
                        'argumentB' => $argumentB,
                    ]
                ),
            ),
        );

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_show')
            ->form(
                null,
                $form->getMethod(),
            );

        $this->client->submit($form);

        $message = $this->client->getCrawler()->filter('.alert-success')->text();

        $expectedResult = sprintf(
            '%f %s %f = %f',
            $argumentA,
            $operation,
            $argumentB,
            $argumentA + $argumentB,
        );

        self::assertSame($expectedResult, $message);
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public function testNoCalculationInQuery(): void
    {
        $this->clearDatabase();

        $response = $this->sendRequest(
            method: 'GET',
        );

        self::assertSame(200, $response->getStatusCode());

        $form = $this->client->getCrawler()->filter('form[name="calculation_form"]')->form();
        $values = $form->getPhpValues();

        self::assertSame('', $values['calculation_form']['argumentA']);
        self::assertSame('', $values['calculation_form']['argumentB']);
        self::assertSame('0', $values['calculation_form']['operation']);

        $form = $this->client->getCrawler()
            ->filter('button#calculation_form_show')
            ->form(
                null,
                $form->getMethod(),
            );

        $this->client->submit($form);

        $message = $this->client->getCrawler()->filter('.alert-success')->text();

        self::assertSame('В очереди нет вычислений', $message);
    }
}
