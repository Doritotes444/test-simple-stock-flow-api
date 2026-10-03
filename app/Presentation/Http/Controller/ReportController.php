<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller;

use App\Application\Ports\Inbound\IGetSalesReportUseCasePort;
use App\Presentation\Http\ProblemDetails\ProblemDetailsResponse;
use DateTimeImmutable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function __construct(
        private readonly IGetSalesReportUseCasePort $getSalesReportUseCase
    ) {
    }

    public function sales(Request $request): JsonResponse
    {
        try {
            $startDate = $request->query('start_date') ? new DateTimeImmutable((string) $request->query('start_date')) : null;
            $endDate = $request->query('end_date') ? new DateTimeImmutable((string) $request->query('end_date')) : null;

            $reportDTO = $this->getSalesReportUseCase->execute($startDate, $endDate);

            return response()->json([
                'total_transactions' => $reportDTO->totalTransactions,
                'total_revenue' => $reportDTO->totalRevenue,
                'currency' => $reportDTO->currency,
                'sales' => $reportDTO->sales,
            ]);
        } catch (Exception $e) {
            return ProblemDetailsResponse::create(
                title: 'Error al generar reporte',
                detail: $e->getMessage(),
                status: 400
            );
        }
    }
}
