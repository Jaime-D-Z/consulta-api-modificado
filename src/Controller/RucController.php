<?php

declare(strict_types=1);

namespace App\Controller;

use Peru\Sunat\Async\Ruc;
use Peru\Sunat\Company;
use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        
        return $this->service
            ->get($ruc)
            ->then(function (?Company $company) use ($ruc, $logPath) {
                if (!$company) {
                    file_put_contents($logPath, sprintf("[%s] [RucController] RUC not found or parsing failed for: %s\n", date('Y-m-d H:i:s'), $ruc), FILE_APPEND);
                    throw new BadRequestHttpException();
                }

                file_put_contents($logPath, sprintf("[%s] [RucController] Successful search for RUC: %s - %s\n", date('Y-m-d H:i:s'), $ruc, $company->razonSocial), FILE_APPEND);
                return new JsonResponse($company);
            }, function (\Throwable $error) use ($ruc, $logPath) {
                file_put_contents($logPath, sprintf("[%s] [RucController] Promise rejected for RUC: %s - Error: %s\n", date('Y-m-d H:i:s'), $ruc, $error->getMessage()), FILE_APPEND);
                throw $error;
            });
    }
}