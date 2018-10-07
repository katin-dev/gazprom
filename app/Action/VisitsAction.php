<?php

namespace App\Action;

use PDO;
use Slim\Http\Request;
use Slim\Http\Response;

class VisitsAction extends ActionAbstract
{
    public function __invoke(Request $request, Response $response)
    {
        $onPage = 10;
        $pageNumber = 0;
        $offset = $pageNumber * $onPage;

        $sortBy    = $this->getWhitelistedRequestParam('sort_by', $request, ['browser', 'os']);
        $sortOrder = $this->getWhitelistedRequestParam('sort_order', $request, ['asc', 'desc']);

        $sql = "
            SELECT vr.ip, vr.browser, vr.os , vf.referer as first_referer, vl.path as last_path, t.unique_visits
            FROM (
                SELECT ip, MIN(id) as first_visit_id, MAX(id) as last_visit_id, COUNT(DISTINCT path) as unique_visits
                FROM visit 
                GROUP BY ip
            ) as t
            JOIN visit vf ON vf.id = t.first_visit_id
            JOIN visit vl ON vl.id = t.last_visit_id
            JOIN visitor vr ON vr.ip = t.ip
            ORDER BY `$sortBy` $sortOrder
            LIMIT $offset, $onPage
        ";

        $stmt = $this->container->db->prepare($sql);
        $status = $stmt->execute([

        ]);

        if ($status === false) {
            throw new \Exception('SQL Error: ' . $stmt->errorInfo()[2] . "\n" . $stmt->queryString);
        }

        $visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $response->withJson([
            'visits' => $visits
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