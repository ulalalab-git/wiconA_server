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
class Info
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * [confirm description]
     * @param  array  $agents [description]
     * @param  string $access   [description]
     * @return [type]           [description]
     */
    public function confirm(array $agents = [], string $access = 'Y'): int
    {
        if (empty($agents) === true) {
            return 0;
        }

        $count = count($agents);
        $add   = "";
        for ($i = 0; $i < $count; ++$i) {
            $comma = ($i === ($count - 1)) ? '' : ', ';
            $add .= ':bind' . $i . $comma;
        }

        $sql = "
            UPDATE
                `wi_agent`
            SET
                `wa_access` = :access
            WHERE
                `wa_idx` IN ( " . $add . " )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':access', $access, PDO::PARAM_STR);
        for ($i = 0; $i < $count; ++$i) {
            $stmt->bindParam(':bind' . $i, $agents[$i], PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [count description]
     * @param  array  $condi [description]
     * @return [type]        [description]
     */
    public function count(array $condi = []): int
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`agent`.`wa_name`';
                    break;
                case 'tel':
                    $type = '`agent`.`wa_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_agent` AS `agent`
                LEFT JOIN (
                    SELECT
                        `agent_user`.`wa_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_agent_user` AS `agent_user`
                    GROUP BY
                        `agent_user`.`wa_idx`
                ) AS `agent_user` ON (`agent_user`.`wa_idx` = `agent`.`wa_idx`)

            WHERE
                1 = 1
                " . $add . "
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'];
    }

    /**
     * [count description]
     * @param  array  $condi [description]
     * @return [type]        [description]
     */
    public function count_privileage(array $condi = [],array $privileage = []): int
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`agent`.`wa_name`';
                    break;
                case 'tel':
                    $type = '`agent`.`wa_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        if (empty($privileage['type']) === false && empty($privileage['wu_idx']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'agent':
                    $add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['wu_idx']."  ) ";
                    break;
                case 'user':
                    $add .= " AND  1 = 2 ";
                    break;
                case 'company':
                    $add .= " AND  1 = 2 ";
                    break;
            }
        }


        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_agent` AS `agent`
                LEFT JOIN (
                    SELECT
                        `agent_user`.`wa_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_agent_user` AS `agent_user`
                    GROUP BY
                        `agent_user`.`wa_idx`
                ) AS `agent_user` ON (`agent_user`.`wa_idx` = `agent`.`wa_idx`)

            WHERE
                1 = 1
                " . $add . "
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'];
    }

    /**
     * [exist description]
     * @param  string|null $agentName [description]
     * @return [type]                   [description]
     */
    public function exist(string $agentName = null): int
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    `wi_agent`
                WHERE
                    `wa_name` = :name
                LIMIT 1
            ) AS row
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $agentName, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['row'];
    }

    /**
     * [get description]
     * @param  string|null $email [description]
     * @return [type]             [description]
     */
    public function get(int $agent = 0): array
    {
        $sql = "
            SELECT
                `agent`.`wa_idx` AS `agent_idx`,
                `agent`.`wa_name` AS `agent_name`,
                `agent`.`wa_ceo` AS `agent_ceo`,
                `agent`.`wa_business` AS `agent_business`,
                `agent`.`wa_logo` AS `agent_logo`,
                `agent`.`wa_tel` AS `agent_tel`,
                `agent`.`wa_zip` AS `agent_zip`,
                `agent`.`wa_address` AS `agent_address`,
                `agent`.`wa_address_detail` AS `agent_address_detail`,
                `agent`.`wa_access` AS `agent_access`,
                `agent`.`wa_delete_date` AS `agent_delete_date`,
                `agent`.`wa_update_date` AS `agent_update_date`,
                `agent`.`wa_create_date` AS `agent_create_date`
            FROM
                `wi_agent` AS `agent`
            WHERE
                `agent`.`wa_idx` = :agent
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
            $row = [];
        }

        return $row;
    }

    /**
     * [lists description]
     * @param  array       $condi [description]
     * @param  int|integer $page  [description]
     * @param  int|integer $limit [description]
     * @return [type]             [description]
     */
    public function lists(array $condi = [], int $page = 0, int $limit = 15): array
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`agent`.`wa_name`';
                    break;
                case 'tel':
                    $type = '`agent`.`wa_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                `agent`.`wa_idx` AS `agent_idx`,
                `agent`.`wa_name` AS `agent_name`,
                `agent`.`wa_ceo` AS `agent_ceo`,
                `agent`.`wa_business` AS `agent_business`,
                `agent`.`wa_logo` AS `agent_logo`,
                `agent`.`wa_tel` AS `agent_tel`,
                `agent`.`wa_zip` AS `agent_zip`,
                `agent`.`wa_address` AS `agent_address`,
                `agent`.`wa_address_detail` AS `agent_address_detail`,
                `agent`.`wa_access` AS `agent_access`,
                `agent`.`wa_delete_date` AS `agent_delete_date`,
                `agent`.`wa_update_date` AS `agent_update_date`,
                `agent`.`wa_create_date` AS `agent_create_date`,
                IFNULL(`agent_user`.`cnt`, 0) AS `agent_user_cnt`
            FROM
                `wi_agent` AS `agent`
                LEFT JOIN (
                    SELECT
                        `agent_user`.`wa_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_agent_user` AS `agent_user`
                    GROUP BY
                        `agent_user`.`wa_idx`
                ) AS `agent_user` ON (`agent_user`.`wa_idx` = `agent`.`wa_idx`)
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `agent`.`wa_idx` DESC
            LIMIT :limit OFFSET :page
        ";

        $page = ((max($page, 1) - 1) * $limit);

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
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
     * [lists description]
     * @param  array       $condi [description]
     * @param  int|integer $page  [description]
     * @param  int|integer $limit [description]
     * @return [type]             [description]
     */
    public function lists_privileage(array $condi = [], array $privileage = [],int $page = 0, int $limit = 15): array
    {

        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`agent`.`wa_name`';
                    break;
                case 'tel':
                    $type = '`agent`.`wa_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        if (empty($privileage['type']) === false && empty($privileage['wu_idx']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'agent':
                    $add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['wu_idx']."  ) ";
                    break;
                case 'user':
                    $add .= " AND  1 = 2 ";
                    break;
                case 'company':
                    $add .= " AND  1 = 2 ";
                    break;
            }
        }

        $sql = "
            SELECT
                `agent`.`wa_idx` AS `agent_idx`,
                `agent`.`wa_name` AS `agent_name`,
                `agent`.`wa_ceo` AS `agent_ceo`,
                `agent`.`wa_business` AS `agent_business`,
                `agent`.`wa_logo` AS `agent_logo`,
                `agent`.`wa_tel` AS `agent_tel`,
                `agent`.`wa_zip` AS `agent_zip`,
                `agent`.`wa_address` AS `agent_address`,
                `agent`.`wa_address_detail` AS `agent_address_detail`,
                `agent`.`wa_access` AS `agent_access`,
                `agent`.`wa_delete_date` AS `agent_delete_date`,
                `agent`.`wa_update_date` AS `agent_update_date`,
                `agent`.`wa_create_date` AS `agent_create_date`,
                IFNULL(`agent_user`.`cnt`, 0) AS `agent_user_cnt`
            FROM
                `wi_agent` AS `agent`
                LEFT JOIN (
                    SELECT
                        `agent_user`.`wa_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_agent_user` AS `agent_user`
                    GROUP BY
                        `agent_user`.`wa_idx`
                ) AS `agent_user` ON (`agent_user`.`wa_idx` = `agent`.`wa_idx`)
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `agent`.`wa_idx` DESC
            LIMIT :limit OFFSET :page
        ";

        $page = ((max($page, 1) - 1) * $limit);

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->bindParam(':page', $page, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        // error_log("here:".print_r($privileage,1)."::", 0);
        // error_log("here:".print_r($page,1)."::", 0);
        // error_log("here:".print_r($limit,1)."::", 0);

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }

    /**
     * [register description]
     * @param  array  $bind [description]
     * @return [type]       [description]
     */
    public function register(array $bind = []): int
    {
        $sql = "
            INSERT INTO
                `wi_agent`
                (
                    `wa_name`,
                    `wa_ceo`,
                    `wa_business`,
                    `wa_tel`,
                    `wa_zip`,
                    `wa_address`,
                    `wa_address_detail`,
                    `wa_access`,
                    `wa_create_date`
                )
            VALUES
                (
                    :name,
                    :ceo,
                    :business,
                    :tel,
                    :zip,
                    :address,
                    :address_detail,
                    :access,
                    NOW()
                )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':ceo', $bind['ceo'], PDO::PARAM_STR);
        $stmt->bindParam(':business', $bind['business'], PDO::PARAM_STR);
        $stmt->bindParam(':tel', $bind['tel'], PDO::PARAM_STR);
        $stmt->bindParam(':zip', $bind['zip'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $bind['address'], PDO::PARAM_STR);
        $stmt->bindParam(':address_detail', $bind['address_detail'], PDO::PARAM_STR);
        $stmt->bindParam(':access', $bind['access'], PDO::PARAM_STR);
        $stmt->execute();
        vardump($stmt);

        return $dbh->lastInsertId();
    }

    /**
     * [restore description]
     * @param  int|integer  $agent  [description]
     * @param  bool|boolean $isRepair [description]
     * @return [type]                 [description]
     */
    public function restore(int $agent = 0, bool $isRepair = true): int
    {
        $set = "`wa_delete_date` = NOW()";
        if ($isRepair === true) {
            $set = "`wa_delete_date` = '0000-00-00 00:00:00'";
        }

        $sql = "
            UPDATE
                `wi_agent`
            SET
                " . $set . "
            WHERE
                `wa_idx` = :agent
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [modify description]
     * @param  int|integer $agent [description]
     * @param  array       $bind    [description]
     * @return [type]               [description]
     */
    public function modify(int $agent = 0, array $bind = []): int
    {
        $sql = "
            UPDATE
                `wi_agent`
            SET
                `wa_name` = :name,
                `wa_ceo` = :ceo,
                `wa_business` = :business,
                `wa_tel` = :tel,
                `wa_zip` = :zip,
                `wa_address` = :address,
                `wa_address_detail` = :address_detail
            WHERE
                `wa_idx` = :agent
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':ceo', $bind['ceo'], PDO::PARAM_STR);
        $stmt->bindParam(':business', $bind['business'], PDO::PARAM_STR);
        $stmt->bindParam(':tel', $bind['tel'], PDO::PARAM_STR);
        $stmt->bindParam(':zip', $bind['zip'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $bind['address'], PDO::PARAM_STR);
        $stmt->bindParam(':address_detail', $bind['address_detail'], PDO::PARAM_STR);
        $stmt->bindParam(':agent', $agent, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
