<?php

namespace App\Action;

use PDO;
use Slim\Http\Request;
use Slim\Http\Response;

class VisitsAction extends ActionAbstract
{
    public function __invoke(Request $request, Response $response)
    {
        // Сортировка
        $sortBy    = $this->getWhitelistedRequestParam('sort_by', $request, ['browser', 'os']);
        $sortOrder = $this->getWhitelistedRequestParam('sort_order', $request, ['asc', 'desc']);

        // Условия поиска
        $where = '';
        $bind = [];

        // Поиск по IP
        $filter = $request->getQueryParam('filter');
        if (!empty($filter['ip'])) {
            // Необходимо экранировать спецсимволы для LIKE оператора:
            $ip = str_replace(array('+', '%', '_'), array('++', '+%', '+_'), $filter['ip']);
            $where = 'WHERE ip LIKE :ip';
            $bind['ip'] = '%' . $ip . '%';
        }

        // Постраничный вывод
        $onPage     = 10;
        $pageNumber = $request->getQueryParam('page', 1);
        $pageNumber = $pageNumber < 0 ? 0 : $pageNumber;
        $offset = ($pageNumber - 1) * $onPage;

        $sql = "
            SELECT {select}
            FROM (
                SELECT ip, MIN(id) as first_visit_id, MAX(id) as last_visit_id, COUNT(DISTINCT path) as unique_visits
                FROM visit
                {where} 
                GROUP BY ip
            ) as t
            JOIN visit vf ON vf.id = t.first_visit_id
            JOIN visit vl ON vl.id = t.last_visit_id
            JOIN visitor vr ON vr.ip = t.ip            
            ORDER BY `$sortBy` $sortOrder
            {limit}
        ";

        // SELECT для получения итогового кол-ва записей
        $countSql = str_replace(
            ['{select}', '{where}', '{limit}'],
            [
                'COUNT(*) as total',
                $where,
                ''
            ],
            $sql
        );

        // SELECT для извлечения строк на текущей странице
        $selectSql = str_replace(
            ['{select}', '{where}', '{limit}'],
            [
                'vr.ip, vr.browser, vr.os , vf.referer as first_referer, vl.path as last_path, t.unique_visits',
                $where,
                "LIMIT $offset, $onPage"
            ],
            $sql
        );

        $total  = $this->executeQuery($countSql,  $bind)[0]['total'];
        $visits = $this->executeQuery($selectSql, $bind);

        return $response->withJson([
            'visits' => $visits,
            'page_count'  => floor($total / $onPage)
        ]);
    }

    private function executeQuery($sql, $bind)
    {
        $stmt = $this->container->db->prepare($sql);
        $status = $stmt->execute($bind);

        if ($status === false) {
            throw new \Exception('SQL Error: ' . $stmt->errorInfo()[2] . "\n" . $stmt->queryString);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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