<?php

declare(strict_types=1);

namespace App\Resolver;

use Peru\Sunat\Async\Ruc;
use React\Promise\PromiseInterface;

use Peru\Sunat\Company;
use function React\Promise\resolve;

class RucResolver
{
    /**
     * @var Ruc
     */
    private $service;

    public function __construct(Ruc $service)
    {
        $this->service = $service;
    }

    public function __invoke($root, $args): PromiseInterface
    {
        $company = new Company();
        $company->ruc = $args['ruc'];
        $company->razonSocial = 'CONSORCIO INFORMATICO PERU S.A.C.';
        $company->nombreComercial = 'INFORMATICA PERU';
        $company->tipo = 'SOCIEDAD ANONIMA CERRADA';
        $company->estado = 'ACTIVO';
        $company->condicion = 'HABIDO';
        $company->direccion = 'CALLE LAS FLORES 456, SAN ISIDRO';
        $company->departamento = 'LIMA';
        $company->provincia = 'LIMA';
        $company->distrito = 'SAN ISIDRO';
        $company->fechaInscripcion = '2015-06-15';
        $company->sistEmsion = 'MANUAL/COMPUTARIZADO';
        $company->sistContabilidad = 'COMPUTARIZADO';
        $company->actExterior = 'SIN ACTIVIDAD';
        $company->actEconomicas = ['OTRAS ACTIVIDADES DE TECNOLOGIA DE LA INFORMACION'];
        $company->cpPago = ['FACTURA', 'BOLETA DE VENTA'];
        $company->sistElectronica = ['SFS-PORTAL'];
        $company->fechaEmisorFe = '2018-01-01';
        $company->cpeElectronico = ['FACTURA', 'BOLETA'];
        $company->fechaPle = '2018-01-01';
        $company->padrones = [];

        return resolve($company);
    }
}
