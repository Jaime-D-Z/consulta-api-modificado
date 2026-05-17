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
        $person = new Person();
        $person->dni = $args['dni'];
        $person->nombres = 'JAIME ANTHONY';
        $person->apellidoPaterno = 'DIAZ';
        $person->apellidoMaterno = 'ZEGARRA';
        $person->codVerifica = '8';

        return resolve($person);
    }
}
