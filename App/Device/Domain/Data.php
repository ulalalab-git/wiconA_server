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
class Data
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
        if (empty($condi['device']) === false) {
            $add .= " AND `data`.`wp_idx` = :device ";
        }
        if (empty($condi['virtual']) === false) {
            $add .= " AND `data`.`wv_idx` = :virtual ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_pf_data` AS `data`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `data`.`wu_idx`)
                LEFT JOIN `wi_virtual_station` AS `virtual` ON (`virtual`.`wv_idx` = `data`.`wv_idx`)
                LEFT JOIN `wi_power_focus` AS `device` ON (`device`.`wp_idx` = `data`.`wp_idx`)
            WHERE
                (`data`.`wd_create_date` >= :start AND `data`.`wd_create_date` <= :end)
                " . $add . "
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['device']) === false) {
            $stmt->bindParam(':device', $condi['device'], PDO::PARAM_INT);
        }
        if (empty($condi['virtual']) === false) {
            $stmt->bindParam(':virtual', $condi['virtual'], PDO::PARAM_INT);
        }
        $stmt->bindParam(':start', $condi['start'], PDO::PARAM_STR);
        $stmt->bindParam(':end', $condi['end'], PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'];
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
        if (empty($condi['device']) === false) {
            $add .= " AND `data`.`wp_idx` = :device ";
        }
        if (empty($condi['virtual']) === false) {
            $add .= " AND `data`.`wv_idx` = :virtual ";
        }

        $sql = "
            SELECT
                `data`.`wd_idx` AS `data_idx`,
                `data`.`wp_idx` AS `device_idx`,
                `data`.`wv_idx` AS `virtual_idx`,
                `data`.`wu_idx` AS `user_idx`,
                `data`.`wd_torque` AS `data_torque`,
                `data`.`wd_angle` AS `data_angle`,
                `data`.`wd_set` AS `data_set`,
                `data`.`wd_status` AS `data_status`,
                `data`.`wd_update_date` AS `data_update_date`,
                `data`.`wd_create_date` AS `data_create_date`,
                `user`.`wu_idx` AS `user_idx`,
                `user`.`wu_email` AS `user_email`,
                `user`.`wu_passwd` AS `user_passwd`,
                `user`.`wu_name` AS `user_name`,
                `user`.`wu_name_last` AS `user_name_last`,
                `user`.`wu_level` AS `user_level`,
                `user`.`wu_tel` AS `user_tel`,
                `user`.`wu_comment` AS `user_comment`,
                `user`.`wu_access` AS `user_access`,
                `user`.`wu_delete_date` AS `user_delete_date`,
                `user`.`wu_update_date` AS `user_update_date`,
                `user`.`wu_create_date` AS `user_create_date`,
                `virtual`.`wv_idx` AS `virtual_idx`,
                `virtual`.`wp_idx` AS `device_idx`,
                `virtual`.`wu_idx` AS `user_idx`,
                `virtual`.`wv_port` AS `virtual_port`,
                `virtual`.`wv_state` AS `virtual_state`,
                `virtual`.`wv_update_date` AS `virtual_update_date`,
                `virtual`.`wv_create_date` AS `virtual_create_date`,
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
                `wi_pf_data` AS `data`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `data`.`wu_idx`)
                LEFT JOIN `wi_virtual_station` AS `virtual` ON (`virtual`.`wv_idx` = `data`.`wv_idx`)
                LEFT JOIN `wi_power_focus` AS `device` ON (`device`.`wp_idx` = `data`.`wp_idx`)
            WHERE
                (`data`.`wd_create_date` >= :start AND `data`.`wd_create_date` <= :end) 
                " . $add . "
            ORDER BY
                `data`.`wd_idx` DESC
            LIMIT :limit OFFSET :page
        ";

        $page = ((max($page, 1) - 1) * $limit);

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($condi['device']) === false) {
            $stmt->bindParam(':device', $condi['device'], PDO::PARAM_INT);
        }
        if (empty($condi['virtual']) === false) {
            $stmt->bindParam(':virtual', $condi['virtual'], PDO::PARAM_INT);
        }
        $stmt->bindParam(':start', $condi['start'], PDO::PARAM_STR);
        $stmt->bindParam(':end', $condi['end'], PDO::PARAM_STR);
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
     * [analysis description]
     * @param  int|integer $device [description]
     * @param  string|null $start  [description]
     * @param  string|null $end    [description]
     * @return [type]              [description]
     */
    public function analysis(int $virtualDevice = 0, string $start = null, string $end = null): array
    {
        $sql = "
            SELECT
                `data`.`wd_idx` AS `data_idx`,
                `data`.`wp_idx` AS `device_idx`,
                `data`.`wv_idx` AS `virtual_idx`,
                `data`.`wu_idx` AS `user_idx`,
                `data`.`wd_torque` AS `data_torque`,
                `data`.`wd_angle` AS `data_angle`,
                `data`.`wd_set` AS `data_set`,
                `data`.`wd_status` AS `data_status`,
                `data`.`wd_update_date` AS `data_update_date`,
                `data`.`wd_create_date` AS `data_create_date`
            FROM
                `wi_pf_data` AS `data`
            WHERE
                `data`.`wv_idx` = :virtual
                AND (`data`.`wd_create_date` >= :start AND `data`.`wd_create_date` <= :end)
            ORDER BY
                `data`.`wd_idx` ASC
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':virtual', $virtualDevice, PDO::PARAM_INT);
        $stmt->bindParam(':start', $start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $end, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }
}
