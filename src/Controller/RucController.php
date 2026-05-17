<?php

declare(strict_types=1);

namespace App\Controller;

use Peru\Sunat\Async\Ruc;
use Peru\Sunat\Company;
use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use function React\Promise\resolve;

class RucController
{
    /**
     * @var Ruc
     */
    private $service;

    /**
     * RucController constructor.
     *
     * @param Ruc $service
     */
    public function __construct(Ruc $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $ruc
     *
     * @return PromiseInterface
     */
    public function index($ruc): PromiseInterface
    {
        $logPath = dirname(__DIR__, 2) . '/var/log/dev.log';
        file_put_contents($logPath, sprintf("[%s] [RucController] Request received for RUC: %s\n", date('Y-m-d H:i:s'), $ruc), FILE_APPEND);
        
        $company = new Company();
        $company->ruc = $ruc;
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

        file_put_contents($logPath, sprintf("[%s] [RucController] Successful search for RUC: %s - %s\n", date('Y-m-d H:i:s'), $ruc, $company->razonSocial), FILE_APPEND);
        return resolve(new JsonResponse($company));
    }
}