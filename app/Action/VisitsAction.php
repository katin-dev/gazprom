<?php

namespace App\Action;

use App\Lib\VisitModel;
use Slim\Http\Request;
use Slim\Http\Response;

class VisitsAction extends ActionAbstract
{
    public function __invoke(Request $request, Response $response)
    {
        // Фильтр
        $filter = $request->getQueryParam('filter', []);

        // Постраничный вывод
        $onPage     = 10;
        $pageNumber = $request->getQueryParam('page', 1);
        $pageNumber = $pageNumber < 0 ? 0 : $pageNumber;

        // Сортировка
        $sortBy    = $this->getWhitelistedRequestParam('sort_by', $request, $this->container->visits->getSortingAvailableFields());
        $sortOrder = $this->getWhitelistedRequestParam('sort_order', $request, [VisitModel::SORT_ASC, VisitModel::SORT_DESC]);

        $total  = $this->container->visits->getVisitsTotal($filter);
        $visits = $this->container->visits->getVisits($filter, $sortBy, $sortOrder, $pageNumber, $onPage);

        return $response->withJson([
            'visits'     => $visits,
            'page_count' => floor($total / $onPage),
        ]);
    }

    /**
     * Получим значение из запроса, ограниченное по whitelist
     * @param string $name - наименование параметра в запросе
     * @param Request $request
     * @param array $whitelist - список разрешённых значений
     * @return string
     */
    private function getWhitelistedRequestParam($name, Request $request, array $whitelist)
    {
        $value = $request->getQueryParam($name);
        return in_array($value, $whitelist) ? $value : $whitelist[0];
    }
}