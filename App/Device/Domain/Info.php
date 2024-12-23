<?php
namespace App\Device\Domain;

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
                    $type = '`device`.`wp_name`';
                    break;
                case 'serial':
                    $type = '`device`.`wp_serial`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_power_focus` AS `device`
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

    public function count_privileage(array $condi = [],array $privileage = []): int
    {
        $add = " ";
        $join = " ";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`device`.`wp_name`';
                    break;
                case 'serial':
                    $type = '`device`.`wp_serial`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        if (empty($privileage['type']) === false && empty($privileage['keyword']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'company':
                    $join = " INNER JOIN `wi_company_power_focus` AS `cpf` ON cpf.wp_idx = device.wp_idx and wc_idx = ".$privileage['keyword']." ";
                    #$add .= " AND  device.wp_idx = (select IFNULL(wp_idx,'0') from wi_company_power_focus where wc_idx = ".$privileage['keyword']."  ) ";
                    break;
                case 'agent':
                    $join = " INNER JOIN `wi_agent_user` AS `au` ON au.wu_idx = virtual.wu_idx and au.wa_idx = ".$privileage['keyword']." ";
                    #$add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['keyword']."  ) ";
                    break;
                case 'user':
                    $add .= " AND  1 = 2 ";
                    break;
            }
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_power_focus` AS `device`
                LEFT JOIN `wi_virtual_station` AS `virtual` ON device.wp_idx = virtual.wp_idx
                ".$join."
            WHERE
                1 = 1
                " . $add . "
                GROUP BY
                `device`.`wp_idx`
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();

        $row = $stmt->fetch();

        if(!$row['cnt']){
            $row['cnt'] = 0;
        }

        return $row['cnt'];
    }

    /**
     * [exist description]
     * @param  string|null $serial [description]
     * @return [type]              [description]
     */
    public function exist(string $serial = null): int
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    `wi_power_focus`
                WHERE
                    `wp_serial` = :serial
                LIMIT 1
            ) AS row
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':serial', $serial, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['row'];
    }

    /**
     * [get description]
     * @param  int|integer $device [description]
     * @return [type]              [description]
     */
    public function get(int $device = 0): array
    {
        $sql = "
            SELECT
                `device`.`wp_idx` AS `device_idx`,
                `device`.`wp_name` AS `device_name`,
                `device`.`wp_serial` AS `device_serial`,
                `device`.`wp_sw_version` AS `device_sw_version`,
                `device`.`wp_hw_version` AS `device_hw_version`,
                `device`.`wp_server` AS `device_server`,
                `device`.`wp_delete_date` AS `device_delete_date`,
                `device`.`wp_update_date` AS `device_update_date`,
                `device`.`wp_create_date` AS `device_create_date`
            FROM
                `wi_power_focus` AS `device`
            WHERE
                `device`.`wp_idx` = :device
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
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
                    $type = '`device`.`wp_name`';
                    break;
                case 'serial':
                    $type = '`device`.`wp_serial`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                `device`.`wp_idx` AS `device_idx`,
                `device`.`wp_name` AS `device_name`,
                `device`.`wp_serial` AS `device_serial`,
                `device`.`wp_sw_version` AS `device_sw_version`,
                `device`.`wp_hw_version` AS `device_hw_version`,
                `device`.`wp_server` AS `device_server`,
                `device`.`wp_delete_date` AS `device_delete_date`,
                `device`.`wp_update_date` AS `device_update_date`,
                `device`.`wp_create_date` AS `device_create_date`
            FROM
                `wi_power_focus` AS `device`
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `device`.`wp_idx` DESC
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
    public function lists_privileage(array $condi = [],array $privileage = [], int $page = 0, int $limit = 15): array
    {
        $add = "";
        $join = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`device`.`wp_name`';
                    break;
                case 'serial':
                    $type = '`device`.`wp_serial`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }
        //error_log("here:".print_r($privileage,1)."::", 0);
        if (empty($privileage['type']) === false && empty($privileage['keyword']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'company':
                    $join = " INNER JOIN `wi_company_power_focus` AS `cpf` ON cpf.wp_idx = device.wp_idx and wc_idx = ".$privileage['keyword']." ";
                    #$add .= " AND  device.wp_idx = (select IFNULL(wp_idx,'0') from wi_company_power_focus where wc_idx = ".$privileage['keyword']."  ) ";
                    break;
                case 'agent':
                    $join = " INNER JOIN `wi_agent_user` AS `au` ON au.wu_idx = virtual.wu_idx and au.wa_idx = ".$privileage['keyword']." ";
                    #$add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['keyword']."  ) ";
                    break;
                case 'user':
                    $add .= " AND  1 = 2 ";
                    break;
            }
        }

        $sql = "
            SELECT
                `device`.`wp_idx` AS `device_idx`,
                `device`.`wp_name` AS `device_name`,
                `device`.`wp_serial` AS `device_serial`,
                `device`.`wp_sw_version` AS `device_sw_version`,
                `device`.`wp_hw_version` AS `device_hw_version`,
                `device`.`wp_server` AS `device_server`,
                `device`.`wp_delete_date` AS `device_delete_date`,
                `device`.`wp_update_date` AS `device_update_date`,
                `device`.`wp_create_date` AS `device_create_date`
            FROM
                `wi_power_focus` AS `device`
                LEFT JOIN `wi_virtual_station` AS `virtual` ON device.wp_idx = virtual.wp_idx
                ".$join."
            WHERE
                1 = 1
                " . $add . "
            GROUP BY
                `device`.`wp_idx`
            ORDER BY
                `device`.`wp_idx` DESC
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
     * [modify description]
     * @param  int|integer $device [description]
     * @param  array       $bind    [description]
     * @return [type]               [description]
     */
    public function modify(int $device = 0, array $bind = []): int
    {
        $sql = "
            UPDATE
                `wi_power_focus`
            SET
                `wp_name` = :name,
                `wp_serial` = :serial,
                `wp_sw_version` = :sw_version,
                `wp_hw_version` = :hw_version,
                `wp_server` = :server
            WHERE
                `wp_idx` = :device
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':serial', $bind['serial'], PDO::PARAM_STR);
        $stmt->bindParam(':sw_version', $bind['sw_version'], PDO::PARAM_STR);
        $stmt->bindParam(':hw_version', $bind['hw_version'], PDO::PARAM_STR);
        $stmt->bindParam(':server', $bind['server'], PDO::PARAM_STR);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
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
                `wi_power_focus`
                (
                    `wp_name`,
                    `wp_serial`,
                    `wp_sw_version`,
                    `wp_hw_version`,
                    `wp_server`,
                    `wp_create_date`
                )
            VALUES
                (
                    :name,
                    :serial,
                    :sw_version,
                    :hw_version,
                    :server,
                    NOW()
                )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':serial', $bind['serial'], PDO::PARAM_STR);
        $stmt->bindParam(':sw_version', $bind['sw_version'], PDO::PARAM_STR);
        $stmt->bindParam(':hw_version', $bind['hw_version'], PDO::PARAM_STR);
        $stmt->bindParam(':server', $bind['server'], PDO::PARAM_STR);
        $stmt->execute();

        return $dbh->lastInsertId();
    }

    /**
     * [restore description]
     * @param  int|integer  $device   [description]
     * @param  bool|boolean $isRepair [description]
     * @return [type]                 [description]
     */
    public function restore(int $device = 0, bool $isRepair = true): int
    {
        $set = "`wp_delete_date` = NOW()";
        if ($isRepair === true) {
            $set = "`wp_delete_date` = '0000-00-00 00:00:00'";
        }

        $sql = "
            UPDATE
                `wi_power_focus`
            SET
                " . $set . "
            WHERE
                `wp_idx` = :device
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [restore description]
     * @param  int|integer  $device   [description]
     * @param  bool|boolean $isRepair [description]
     * @return [type]                 [description]
     */
    public function remove(int $wv_idx = 0): int
    {


        $sql = "
            DELETE FROM
                `wi_virtual_station`
            WHERE
                `wv_idx` = :wv_idx
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':wv_idx', $wv_idx, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
