<?php

namespace App\Lib;

use PDO;

class VisitModel
{
    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    /** @var PDO  */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Получить список просмотров
     * @param array $filter - фильтр, напримерм ['ip' => '192']
     * @param string $sortBy - по какому полю хотим сортировать
     * @param string $sortOrder - направление сортировки
     * @param int $pageNumber - номер страницы
     * @param int $onPage - сколько элементов на странице
     * @return array
     * @throws \Exception
     */
    public function getVisits(array $filter, $sortBy, $sortOrder, $pageNumber, $onPage = 10)
    {
        if (!in_array(strtolower($sortOrder), [self::SORT_ASC, self::SORT_DESC])) {
            throw new \Exception('Unknown sort order ' . $sortOrder);
        }

        if (!in_array(strtolower($sortBy), $this->getSortingAvailableFields())) {
            throw new \Exception('Unable to sort by ' . $sortBy);
        }

        list($where, $bind) = $this->getWherePart($filter);

        $offset = ($pageNumber - 1) * $onPage;

        $sql = str_replace(
            ['{select}', '{where}', '{limit}', '{order}'],
            [
                'vr.ip, vr.browser, vr.os , vf.referer as first_referer, vl.path as last_path, t.unique_visits',
                $where ? 'WHERE ' . implode(',', $where) : '',
                "LIMIT $offset, $onPage",
                "ORDER BY `$sortBy` $sortOrder"
            ],
            $this->getSqlTemplate()
        );

        return $this->executeQuery($sql, $bind);
    }

    /**
     * Получить список просмотров, удовлетворяющих условию
     * @param array $filter - условие фильтрации
     * @return int - кол-во строк в БД
     * @throws \Exception
     */
    public function getVisitsTotal(array $filter)
    {
        list($where, $bind) = $this->getWherePart($filter);

        $sql = str_replace(
            ['{select}', '{where}', '{limit}', '{order}'],
            [
                'COUNT(*) as total',
                $where ? 'WHERE ' . implode(',', $where) : '',
                '',
                ''
            ],
            $this->getSqlTemplate()
        );

        $result = $this->executeQuery($sql, $bind);

        return $result ? $result[0]['total'] : 0;
    }

    /**
     * Доступные для сортировки поля
     * @return array
     */
    public function getSortingAvailableFields()
    {
        return ['browser', 'os'];
    }

    private function getSqlTemplate()
    {
        return "
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
            {order}
            {limit}
        ";
    }

    /**
     * Метод-хелпер для разбора условия фильтрации
     * @param $filter
     * @return array - вернёт массив из 2х значений [where, bind]
     */
    private function getWherePart($filter)
    {
        // Условия поиска
        $where = [];
        $bind  = [];

        // Поиск по IP
        if (!empty($filter['ip'])) {
            // Необходимо экранировать спецсимволы для LIKE оператора:
            $ip = str_replace(array('+', '%', '_'), array('++', '+%', '+_'), $filter['ip']);
            $where[] = 'ip LIKE :ip';
            $bind['ip'] = '%' . $ip . '%';
        }

        return [$where, $bind];
    }

    private function executeQuery($sql, $bind)
    {
        $stmt   = $this->db->prepare($sql);
        $status = $stmt->execute($bind);

        if ($status === false) {
            throw new \Exception('SQL Error: ' . $stmt->errorInfo()[2] . "\n" . $stmt->queryString);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}