<?php
namespace App\Agent\Domain;

use App\TComponent;
use App\TContainer;
use Exception;
use PDO;
use PDOException;

/**
 *
 *
 */
class Company
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * [count description]
     * @param  array  $condi [description]
     * @return [type]        [description]
     */
    public function count(int $agent = 0, array $condi = []): int
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`company`.`wc_name`';
                    break;
                case 'ceo':
                    $type = '`company`.`wc_ceo`';
                    break;
                case 'tel':
                    $type = '`company`.`wc_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_agent_company` AS `agent_company`
                LEFT JOIN `wi_company` AS `company` ON (`company`.`wc_idx` = `agent_company`.`wc_idx`)
            WHERE
                1 = 1
                AND `agent_company`.`wa_idx` = :agent
                " . $add . "
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'];
    }

    /**
     * [designate description]
     * @param  int|integer $agent [description]
     * @param  array       $users   [description]
     * @return [type]               [description]
     */
    public function designate(int $agent = 0, array $users = []): void
    {
        $columns = [
            'wa_idx',
            'wc_idx',
        ];
        $upsert = [];
        foreach ($users as $user) {
            $upsert[] = [
                'wa_idx' => $agent,
                'wc_idx' => $user,
            ];
        }
        //error_log(json_encode($upsert));

        $onDulpe = [];
        foreach ($columns as $column) {
            $onDulpe[] = " `" . $column . "` = VALUES(`" . $column . "`)";
        }
        $after = " ON DUPLICATE KEY UPDATE " . implode(', ', $onDulpe);

        $dbh = $this->maria;
        $sql = "INSERT INTO `wi_agent_company` (`" . implode('`,`', $columns) . "`) VALUES ";
        foreach (array_chunk($upsert, 50) as $data) {
            $innerData = $innerQuery = [];
            foreach ($data as $key => $inner) {
                $duple = [];
                foreach ($columns as $column) {
                    $switch             = ":" . $column . "_" . $key;
                    $duple[]            = $switch;
                    $innerData[$switch] = $inner[$column];
                }
                $innerQuery[] = "(" . implode(',', $duple) . ")";
            }

            $stmt = $dbh->prepare($sql . implode(', ', $innerQuery) . $after);
            $stmt->execute($innerData);
        }
    }

    /**
     * [agent description]
     * @param  int|integer $agent [description]
     * @return [type]               [description]
     */
    public function agent(int $agent = 0): array
    {
        $add = "";
        if (empty($agent) === false) {
            $add = 'AND `agent_company`.`wa_idx`';
        }

        $sql = "
            SELECT
                `company`.`wc_idx` AS `company_idx`,
                `company`.`wc_name` AS `company_name`,
                `company`.`wc_ceo` AS `company_ceo`,
                `company`.`wc_business` AS `company_business`,
                `company`.`wc_logo` AS `company_logo`,
                `company`.`wc_tel` AS `company_tel`,
                `company`.`wc_zip` AS `company_zip`,
                `company`.`wc_address` AS `company_address`,
                `company`.`wc_address_detail` AS `company_address_detail`,
                `company`.`wc_access` AS `company_access`,
                `company`.`wc_delete_date` AS `company_delete_date`,
                `company`.`wc_update_date` AS `company_update_date`,
                `company`.`wc_create_date` AS `company_create_date`
            FROM
                `wi_agent_company` AS `agent_company`
                LEFT JOIN `wi_company` AS `company` ON (`company`.`wc_idx` = `agent_company`.`wc_idx`)
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `company`.`wc_idx` DESC
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($agent) === false) {
            $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }

    /**
     * [lists description]
     * @param  int|integer $agent [description]
     * @param  array       $condi   [description]
     * @param  int|integer $page    [description]
     * @param  int|integer $limit   [description]
     * @return [type]               [description]
     */
    public function lists(int $agent = 0, array $condi = [], int $page = 0, int $limit = 15): array
    {


        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`company`.`wc_name`';
                    break;
                case 'ceo':
                    $type = '`company`.`wc_ceo`';
                    break;
                case 'tel':
                    $type = '`company`.`wc_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                `company`.`wc_idx` AS `company_idx`,
                `company`.`wc_name` AS `company_name`,
                `company`.`wc_ceo` AS `company_ceo`,
                `company`.`wc_business` AS `company_business`,
                `company`.`wc_logo` AS `company_logo`,
                `company`.`wc_tel` AS `company_tel`,
                `company`.`wc_zip` AS `company_zip`,
                `company`.`wc_address` AS `company_address`,
                `company`.`wc_address_detail` AS `company_address_detail`,
                `company`.`wc_access` AS `company_access`,
                `company`.`wc_delete_date` AS `company_delete_date`,
                `company`.`wc_update_date` AS `company_update_date`,
                `company`.`wc_create_date` AS `company_create_date`
            FROM
                `wi_agent_company` AS `agent_company`
                LEFT JOIN `wi_company` AS `company` ON (`company`.`wc_idx` = `agent_company`.`wc_idx`)
            WHERE
                1 = 1
                AND `agent_company`.`wa_idx` = :agent
                " . $add . "
            ORDER BY
                `company`.`wc_idx` DESC
            LIMIT :limit OFFSET :page
        ";

        $page = ((max($page, 1) - 1) * $limit);

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->bindParam(':page', $page, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }

    /**
     * [undesignate description]
     * @param  int|integer $agent [description]
     * @param  array       $users   [description]
     * @return [type]               [description]
     */
    public function undesignate(int $agent = 0, array $users = []): int
    {
        $count = count($users);
        $add   = "";
        for ($i = 0; $i < $count; ++$i) {
            $comma = ($i === ($count - 1)) ? '' : ', ';
            $add .= ':bind' . $i . $comma;
        }

        $sql = "
            DELETE FROM
                `wi_agent_company`
            WHERE
                `wa_idx` = :agent
                AND `wc_idx` IN ( " . $add . " )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        for ($i = 0; $i < $count; ++$i) {
            $stmt->bindParam(':bind' . $i, $users[$i], PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->rowCount();
    }
}
