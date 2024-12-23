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
class Virtual
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * [all description]
     * @param  int|integer $device [description]
     * @return [type]              [description]
     */
    public function all(int $device = 0): array
    {
        $sql = "
            SELECT
                `virtual`.`wv_idx` AS `virtual_idx`,
                `virtual`.`wp_idx` AS `device_idx`,
                `virtual`.`wu_idx` AS `user_idx`,
                `virtual`.`wv_port` AS `virtual_port`,
                `virtual`.`wv_state` AS `virtual_state`,
                `virtual`.`wv_update_date` AS `virtual_update_date`,
                `virtual`.`wv_create_date` AS `virtual_create_date`,
                -- `user`.`wu_idx` AS `user_idx`,
                `user`.`wu_email` AS `user_email`,
                -- `user`.`wu_passwd` AS `user_passwd`,
                `user`.`wu_name` AS `user_name`,
                `user`.`wu_name_last` AS `user_name_last`,
                `user`.`wu_level` AS `user_level`,
                `user`.`wu_tel` AS `user_tel`,
                `user`.`wu_comment` AS `user_comment`,
                `user`.`wu_access` AS `user_access`,
                `user`.`wu_delete_date` AS `user_delete_date`,
                `user`.`wu_update_date` AS `user_update_date`,
                `user`.`wu_create_date` AS `user_create_date`
            FROM
                `wi_virtual_station` AS `virtual`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `virtual`.`wu_idx`)
            WHERE
                `virtual`.`wp_idx` = :device
            ORDER BY
                `virtual`.`wv_idx` ASC
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }

    public function lists(array $condi = []): array
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'user':
                    $type = '`virtual`.`wu_idx`';
                    $add .= " AND " . $type . " =:keyword ";
                    break;
                case 'device':
                    $type = '`virtual`.`wp_idx`';
                    $add .= " AND " . $type . " =:keyword ";
                    break;
            }

        }

        $sql = "
            SELECT
                `virtual`.`wv_idx` AS `virtual_idx`,
                `virtual`.`wp_idx` AS `device_idx`,
                `virtual`.`wu_idx` AS `user_idx`,
                `virtual`.`wv_port` AS `virtual_port`,
                `virtual`.`wv_state` AS `virtual_state`,
                `virtual`.`wv_update_date` AS `virtual_update_date`,
                `device`.`wp_name` AS `wp_name`,
                `device`.`wp_serial` AS `wp_serial`,
                `device`.`wp_sw_version` AS `wp_sw_version`,
                `device`.`wp_hw_version` AS `wp_hw_version`,
                -- `user`.`wu_idx` AS `user_idx`,
                `user`.`wu_email` AS `user_email`,
                -- `user`.`wu_passwd` AS `user_passwd`,
                `user`.`wu_name` AS `user_name`,
                `user`.`wu_name_last` AS `user_name_last`,
                `user`.`wu_level` AS `user_level`,
                `user`.`wu_tel` AS `user_tel`,
                `user`.`wu_comment` AS `user_comment`,
                `user`.`wu_access` AS `user_access`,
                `user`.`wu_delete_date` AS `user_delete_date`,
                `user`.`wu_update_date` AS `user_update_date`,
                `user`.`wu_create_date` AS `user_create_date`
            FROM
                `wi_virtual_station` AS `virtual`
                INNER JOIN  wi_power_focus as device ON device.wp_idx = virtual.wp_idx
                INNER JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `virtual`.`wu_idx`)
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `virtual`.`wv_idx` ASC
        ";


        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_INT);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        //error_log("here:".print_r($rows,1)."::", 0);
        return $rows;
    }



    /**
     * [exist description]
     * @param  int|integer $device [description]
     * @param  int|integer $port   [description]
     * @return [type]              [description]
     */
    public function exist(int $device = 0, int $port = 0): int
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    `wi_virtual_station`
                WHERE
                    `wp_idx` = :device
                    AND `wv_port` = :port
                LIMIT 1
            ) AS row
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':port', $port, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['row'];
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
                `wi_virtual_station`
                (
                    `wp_idx`,
                    `wu_idx`,
                    `wv_port`,
                    `wv_state`,
                    `wv_create_date`
                )
            VALUES
                (
                    :device,
                    :user,
                    :port,
                    :state,
                    NOW()
                )
            ON DUPLICATE KEY UPDATE
                `wu_idx` = :update_user
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':device', $bind['device'], PDO::PARAM_INT);
        $stmt->bindParam(':user', $bind['user'], PDO::PARAM_INT);
        $stmt->bindParam(':port', $bind['port'], PDO::PARAM_INT);
        $stmt->bindParam(':state', $bind['state'], PDO::PARAM_STR);
        $stmt->bindParam(':update_user', $bind['user'], PDO::PARAM_INT);
        $stmt->execute();

        return $dbh->lastInsertId();
    }

    /**
     * [state description]
     * @param  int|integer $device [description]
     * @param  int|integer $port   [description]
     * @param  string      $use    [description]
     * @return [type]              [description]
     */
    public function state(int $device = 0, int $port = 0, string $state = 'Y'): int
    {
        $sql = "
            UPDATE
                `wi_virtual_station`
            SET
                `wv_state` = :state
            WHERE
                `wp_idx` = :device
                AND `wv_port` = :port
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':state', $state, PDO::PARAM_STR);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':port', $port, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
