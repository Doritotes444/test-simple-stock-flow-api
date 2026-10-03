<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\SaleResponseDTO;
use App\Application\DTO\SalesReportDTO;
use App\Application\Ports\Inbound\IGetSalesReportUseCasePort;
use App\Application\Ports\Outbound\ISaleRepositoryPort;
use DateTimeImmutable;

class GetSalesReportUseCase implements IGetSalesReportUseCasePort
{
    public function __construct(
        private readonly ISaleRepositoryPort $saleRepository
    ) {
    }

    public function execute(?DateTimeImmutable $startDate = null, ?DateTimeImmutable $endDate = null): SalesReportDTO
    {
        $sales = $this->saleRepository->findByDateRange($startDate, $endDate);

        $totalRevenue = 0.0;
        $currency = 'COP';
        $saleDTOs = [];

        foreach ($sales as $sale) {
            $totalRevenue += $sale->getTotal()->getAmount();
            $currency = $sale->getTotal()->getCurrency();

            $items = [];
            foreach ($sale->getItems() as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'productId' => $item->getProductId(),
                    'productName' => $item->getProductName(),
                    'quantity' => $item->getQuantity()->getValue(),
                    'unitPrice' => $item->getUnitPrice()->getAmount(),
                    'subtotal' => $item->getSubtotal()->getAmount(),
                ];
            }

            $saleDTOs[] = new SaleResponseDTO(
                id: (int) $sale->getId(),
                userId: $sale->getUserId(),
                total: $sale->getTotal()->getAmount(),
                currency: $currency,
                createdAt: $sale->getCreatedAt()->format('Y-m-d H:i:s'),
                items: $items
            );
        }

        return new SalesReportDTO(
            totalTransactions: count($sales),
            totalRevenue: round($totalRevenue, 2),
            currency: $currency,
            sales: $saleDTOs
        );
    }
}
