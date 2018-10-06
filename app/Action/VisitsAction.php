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

        $sql = "
            SELECT vr.ip, vr.browser, vr.os , vf.referer as first_referer, vl.path as last_path, t.unique_visits
            FROM (
                SELECT ip, MIN(id) as first_visit_id, MAX(id) as last_visit_id, COUNT(DISTINCT path) as unique_visits
                FROM visit 
                GROUP BY ip
                LIMIT $offset, $onPage
            ) as t
            JOIN visit vf ON vf.id = t.first_visit_id
            JOIN visit vl ON vl.id = t.last_visit_id
            JOIN visitor vr ON vr.ip = t.ip;
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
}