<?php
namespace App\Dashboard\Domain;

use App\TComponent;
use App\TContainer;
use Exception;
use PDO;
use PDOException;

/**
 *
 *
 */
class Packet
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * [statistics description]
     * @param  int|integer $device [description]
     * @param  int|integer $last   [description]
     * @param  string|null $date   [description]
     * @return [type]              [description]
     */
    public function statistics(int $device = 0, int $last = 0, string $date = null): array
    {
        if (empty($date) === true) {
            $date = 'CURDATE()';
        }

        $add = "";
        if (empty($last) === false) {
            $add .= " AND `data`.`wd_idx` > :last ";
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
                COALESCE(`calc`.`max_torque`, 0) AS `calc_max_torque`,
                COALESCE(`calc`.`min_torque`, 0) AS `calc_min_torque`,
                COALESCE(`calc`.`avg_torque`, 0) AS `calc_avg_torque`,
                COALESCE(`calc`.`s3_torque`, 0) AS `calc_s3_torque`,
                COALESCE(`calc`.`ok_status`, 0) AS `calc_ok_status`,
                COALESCE(`calc`.`nok_status`, 0) AS `calc_nok_status`
            FROM
                `wi_pf_data` AS `data`
                LEFT JOIN (
                    SELECT
                        `wp_idx`, `wv_idx`,
                        MAX(`wd_torque`) AS `max_torque`,
                        MIN(`wd_torque`) AS `min_torque`,
                        AVG(`wd_torque`) AS `avg_torque`,
                        (3 * STDDEV_SAMP(`wd_torque`)) AS `s3_torque`,
                        COUNT(CASE WHEN `wd_status` = 1 THEN 1 END) AS `ok_status`,
                        COUNT(CASE WHEN `wd_status` = 0 THEN 1 END) AS `nok_status`
                    FROM
                        `wi_pf_data`
                    WHERE
                        `wp_idx` = :calc_device
                        AND DATE_FORMAT(`wd_create_date`, '%Y-%m-%d') = :calc_date
                    GROUP BY
                        `wp_idx`, `wv_idx`
                ) AS `calc` ON (`calc`.`wp_idx` = `data`.`wp_idx` AND `calc`.`wv_idx` = `data`.`wv_idx`)
            WHERE
                `data`.`wp_idx` = :device
                AND DATE_FORMAT(`wd_create_date`, '%Y-%m-%d') = :date
                " . $add . "
            ORDER BY
                `data`.`wd_idx` DESC
            LIMIT 15
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($last) === false) {
            $stmt->bindParam(':last', $last, PDO::PARAM_INT);
        }
        $stmt->bindParam(':calc_device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':calc_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }

    public function statisticsport(int $device = 0, int $last = 0, string $date = null,int $virtual = 0): array
    {
        if (empty($date) === true) {
            $date = 'CURDATE()';
        }

        $add = "";
        if (empty($last) === false) {
            $add .= " AND `data`.`wd_idx` > :last ";
        }

        $sql = "
            SELECT
                `data`.`wd_idx` AS `data_idx`,
                `data`.`wp_idx` AS `device_idx`,
                `data`.`wv_idx` AS `virtual_idx`,
                `data`.`wu_idx` AS `user_idx`,
                `data`.`wd_torque` AS `data_torque`,
                `data`.`wd_torque_max` AS `wd_torque_max`,
                `data`.`wd_angle` AS `data_angle`,
                `data`.`wd_angle_max` AS `wd_angle_max`,
                `data`.`wd_set` AS `data_set`,
                `data`.`wd_status` AS `data_status`,
                `data`.`wd_update_date` AS `data_update_date`,
                `data`.`wd_create_date` AS `data_create_date`,
                COALESCE(`calc`.`max_torque`, 0) AS `calc_max_torque`,
                COALESCE(`calc`.`min_torque`, 0) AS `calc_min_torque`,
                COALESCE(`calc`.`avg_torque`, 0) AS `calc_avg_torque`,
                COALESCE(`calc`.`s3_torque`, 0) AS `calc_s3_torque`,
                COALESCE(`calc`.`ok_status`, 0) AS `calc_ok_status`,
                COALESCE(`calc`.`nok_status`, 0) AS `calc_nok_status`
            FROM
                `wi_pf_data` AS `data`
                LEFT JOIN (
                    SELECT
                        `wp_idx`, `wv_idx`,
                        MAX(`wd_torque`) AS `max_torque`,
                        MIN(`wd_torque`) AS `min_torque`,
                        AVG(`wd_torque`) AS `avg_torque`,
                        (3 * STDDEV_SAMP(`wd_torque`)) AS `s3_torque`,
                        COUNT(CASE WHEN `wd_status` = 1 THEN 1 END) AS `ok_status`,
                        COUNT(CASE WHEN `wd_status` = 0 THEN 1 END) AS `nok_status`
                    FROM
                        `wi_pf_data`
                    WHERE
                        `wp_idx` = :calc_device
                        AND DATE_FORMAT(`wd_create_date`, '%Y-%m-%d') = :calc_date
                    GROUP BY
                        `wp_idx`, `wv_idx`
                ) AS `calc` ON (`calc`.`wp_idx` = `data`.`wp_idx` AND `calc`.`wv_idx` = `data`.`wv_idx`)
            WHERE
                `data`.`wv_idx` = :virtual
                AND DATE_FORMAT(`wd_create_date`, '%Y-%m-%d') = :date
                " . $add . "
            ORDER BY
                `data`.`wd_idx` DESC
            LIMIT 15
        ";
        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($last) === false) {
            $stmt->bindParam(':last', $last, PDO::PARAM_INT);
        }
        $stmt->bindParam(':calc_device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':virtual', $virtual, PDO::PARAM_INT);
        //$stmt->bindParam(':device', $device, PDO::PARAM_INT);
        $stmt->bindParam(':calc_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        if ($rows === false) {
            $rows = [];
        }

        return $rows;
    }
}
