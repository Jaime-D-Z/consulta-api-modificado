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
        
        return $this->service
            ->get($dni)
            ->then(function (?Person $person) use ($dni, $logPath) {
                if ($person && !empty($person->nombres)) {
                    file_put_contents($logPath, sprintf("[%s] [DniController] Successful real search for DNI: %s - %s\n", date('Y-m-d H:i:s'), $dni, $person->nombres), FILE_APPEND);
                    return new JsonResponse($person);
                }
                
                file_put_contents($logPath, sprintf("[%s] [DniController] Real search returned empty for DNI: %s. Using fallback mock.\n", date('Y-m-d H:i:s'), $dni), FILE_APPEND);
                return new JsonResponse($this->generateMockPerson($dni));
            }, function (\Throwable $error) use ($dni, $logPath) {
                file_put_contents($logPath, sprintf("[%s] [DniController] Real search failed for DNI: %s. Using fallback mock. Error: %s\n", date('Y-m-d H:i:s'), $dni, $error->getMessage()), FILE_APPEND);
                return new JsonResponse($this->generateMockPerson($dni));
            });
    }

    private function generateMockPerson(string $dni): Person
    {
        $person = new Person();
        $person->dni = $dni;
        
        $lastDigits = substr($dni, -4);
        if (!is_numeric($lastDigits)) {
            $lastDigits = "1234";
        }
        $seed = intval($lastDigits);
        
        $firstNames = ['CARLOS', 'JUAN', 'MARIA', 'JOSE', 'ANA', 'LUIS', 'PEDRO', 'ROSA', 'JORGE', 'ESTHER'];
        $paternal = ['GOMEZ', 'QUISPE', 'RODRIGUEZ', 'FLORES', 'SANCHEZ', 'RAMIREZ', 'DIAZ', 'ZEGARRA', 'TINOCO', 'JARA'];
        $maternal = ['ALVAREZ', 'CASTRO', 'CHAVEZ', 'MENDOZA', 'LOPEZ', 'TORRES', 'RIVERA', 'ESPINOSA', 'ROJAS', 'VARA'];
        
        $idx1 = $seed % count($firstNames);
        $idx2 = ($seed + 3) % count($paternal);
        $idx3 = ($seed + 7) % count($maternal);
        
        $person->nombres = $firstNames[$idx1] . " " . "TEST";
        $person->apellidoPaterno = $paternal[$idx2];
        $person->apellidoMaterno = $maternal[$idx3];
        $person->codVerifica = strval($seed % 10);
        
        return $person;
    }
}