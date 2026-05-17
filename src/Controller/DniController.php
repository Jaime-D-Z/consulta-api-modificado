<?php

declare(strict_types=1);

namespace App\Controller;

use Peru\Jne\Async\Dni;
use Peru\Reniec\Person;
use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DniController
{
    /**
     * @var Dni
     */
    private $service;

    /**
     * DniController constructor.
     *
     * @param Dni $service
     */
    public function __construct(Dni $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $dni
     *
     * @return PromiseInterface
     */
    public function index($dni): PromiseInterface
    {
        $logPath = dirname(__DIR__, 2) . '/var/log/dev.log';
        file_put_contents($logPath, sprintf("[%s] [DniController] Request received for DNI: %s\n", date('Y-m-d H:i:s'), $dni), FILE_APPEND);
        
        return $this->service
            ->get($dni)
            ->then(function (?Person $person) use ($dni, $logPath) {
                if (!$person) {
                    file_put_contents($logPath, sprintf("[%s] [DniController] DNI not found or parsing failed for: %s\n", date('Y-m-d H:i:s'), $dni), FILE_APPEND);
                    throw new BadRequestHttpException();
                }

                file_put_contents($logPath, sprintf("[%s] [DniController] Successful search for DNI: %s - %s\n", date('Y-m-d H:i:s'), $dni, $person->nombres), FILE_APPEND);
                return new JsonResponse($person);
            }, function (\Throwable $error) use ($dni, $logPath) {
                file_put_contents($logPath, sprintf("[%s] [DniController] Promise rejected for DNI: %s - Error: %s\n", date('Y-m-d H:i:s'), $dni, $error->getMessage()), FILE_APPEND);
                throw $error;
            });
    }
}