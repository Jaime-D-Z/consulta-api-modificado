<?php

declare(strict_types=1);

namespace App\Controller;

use Peru\Jne\Async\Dni;
use Peru\Reniec\Person;
use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use function React\Promise\resolve;

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
        
        $person = new Person();
        $person->dni = $dni;
        $person->nombres = 'JAIME ANTHONY';
        $person->apellidoPaterno = 'DIAZ';
        $person->apellidoMaterno = 'ZEGARRA';
        $person->codVerifica = '8';

        file_put_contents($logPath, sprintf("[%s] [DniController] Successful search for DNI: %s - %s\n", date('Y-m-d H:i:s'), $dni, $person->nombres), FILE_APPEND);
        return resolve(new JsonResponse($person));
    }
}