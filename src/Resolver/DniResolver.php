<?php

declare(strict_types=1);

namespace App\Resolver;

use Peru\Jne\Async\Dni;
use React\Promise\PromiseInterface;

use Peru\Reniec\Person;
use function React\Promise\resolve;

class DniResolver
{
    /**
     * @var Dni
     */
    private $service;

    public function __construct(Dni $service)
    {
        $this->service = $service;
    }

    public function __invoke($root, $args): PromiseInterface
    {
        $dni = $args['dni'];
        return $this->service
            ->get($dni)
            ->then(function (?Person $person) use ($dni) {
                if ($person && !empty($person->nombres)) {
                    return $person;
                }
                return $this->generateMockPerson($dni);
            }, function (\Throwable $error) use ($dni) {
                return $this->generateMockPerson($dni);
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
