<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

abstract class AbstractTest extends WebTestCase
{
    /**
     * Базовый урл
     */
    public const URL = '';

    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    /**
     * @var Generator
     */
    private static Generator $faker;

    private EntityManager $entityManager;

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        parent::setUp();
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param string $method
     * @param array | null $params
     * @param array | null $body
     * @param string | null $url
     * @param array | null $headers
     *
     * @return Response
     */
    protected function sendRequest(
        string $method,
        ?array $params = [],
        ?array $body = null,
        ?string $url = null,
        ?array $headers = [],
    ): Response {
        $url = !is_null($url) ? self::URL . $url : self::URL . static::URL;

        $this->client->request(
            $method,
            $url,
            $params,
            [],
            $headers,
            $body,
            false,
        );

        return $this->client->getResponse();
    }

    /**
     * @throws Exception
     */
    protected function clearDatabase(): void
    {
        $commands = [
            ['doctrine:schema:drop', '--env=test', '--force'],
            ['doctrine:schema:create', '--env=test'],
        ];

        foreach ($commands as $command) {
            $process = new Process(array_merge(['php', $this->client->getContainer()->getParameter('consolePath')], $command));
            $process->start();

            while (Process::STATUS_TERMINATED !== $process->getStatus()) {
                usleep(50);
            }
        }
    }

    /**
     * Ассерты на ошибки
     *
     * @param array $content
     */
    protected function assertErrors(array $content): void
    {
        self::assertArrayHasKey('data', $content);
        self::assertArrayHasKey('errors', $content['data']);

        foreach ($content['data']['errors'] as $error) {
            self::assertArrayHasKey('message', $error);
            self::assertArrayHasKey('code', $error);
        }
    }

    /**
     * @param array $expectedStructure
     * @param array $actualStructure
     *
     * @return void
     */
    protected function assertArrayStructure(array $expectedStructure, array $actualStructure): void
    {
        foreach ($expectedStructure as $key => $value) {
            if (is_array($value)) {
                self::assertArrayHasKey($key, $actualStructure);
                $this->assertArrayStructure($value, $actualStructure[$key]);
            } else {
                self::assertArrayHasKey($value, $actualStructure);
            }
        }
    }

    /**
     * @return Generator
     */
    public static function getFaker(): Generator
    {
        if (empty(self::$faker)) {
            self::$faker = Factory::create('ru_RU');
        }

        return self::$faker;
    }
}
