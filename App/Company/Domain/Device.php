<?php
namespace App\Company\Domain;

use App\TComponent;
use App\TContainer;
use Exception;
use PDO;
use PDOException;

/**
 *
 *
 */
class Device
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
    public function count(int $company = 0, array $condi = []): int
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
                `wi_company_power_focus` AS `company_power_focus`
                LEFT JOIN `wi_power_focus` AS `device` ON (`device`.`wp_idx` = `company_power_focus`.`wp_idx`)
            WHERE
                1 = 1
                AND `company_power_focus`.`wc_idx` = :company
                " . $add . "
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $stmt->bindParam(':keyword', $condi['keyword'], PDO::PARAM_STR);
        }
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['cnt'];
    }

    /**
     * [designate description]
     * @param  int|integer $company [description]
     * @param  array       $users   [description]
     * @return [type]               [description]
     */
    public function designate(int $company = 0, array $users = []): void
    {
        $columns = [
            'wc_idx',
            'wp_idx',
        ];
        $upsert = [];
        foreach ($users as $user) {
            $upsert[] = [
                'wc_idx' => $company,
                'wp_idx' => $user,
            ];
        }

        $onDulpe = [];
        foreach ($columns as $column) {
            $onDulpe[] = " `" . $column . "` = VALUES(`" . $column . "`)";
        }
        $after = " ON DUPLICATE KEY UPDATE " . implode(', ', $onDulpe);

        $dbh = $this->maria;
        $sql = "INSERT INTO `wi_company_power_focus` (`" . implode('`,`', $columns) . "`) VALUES ";
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
     * [company description]
     * @param  int|integer $company [description]
     * @return [type]               [description]
     */
    public function company(int $company = 0): array
    {
        $add = "";
        if (empty($company) === false) {
            $add = 'AND `company_power_focus`.`wc_idx`';
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
                `wi_company_power_focus` AS `company_power_focus`
                LEFT JOIN `wi_power_focus` AS `device` ON (`device`.`wp_idx` = `company_power_focus`.`wp_idx`)
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `device`.`wp_idx` DESC
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($company) === false) {
            $stmt->bindParam(':company', $company, PDO::PARAM_INT);
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
     * @param  int|integer $company [description]
     * @param  array       $condi   [description]
     * @param  int|integer $page    [description]
     * @param  int|integer $limit   [description]
     * @return [type]               [description]
     */
    public function lists(int $company = 0, array $condi = [], int $page = 0, int $limit = 15): array
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
                `wi_company_power_focus` AS `company_power_focus`
                LEFT JOIN `wi_power_focus` AS `device` ON (`device`.`wp_idx` = `company_power_focus`.`wp_idx`)
            WHERE
                1 = 1
                AND `company_power_focus`.`wc_idx` = :company
                " . $add . "
            ORDER BY
                `device`.`wp_idx` DESC
            LIMIT :limit OFFSET :page
        ";

        $page = ((max($page, 1) - 1) * $limit);

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
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
     * @param  int|integer $company [description]
     * @param  array       $users   [description]
     * @return [type]               [description]
     */
    public function undesignate(int $company = 0, array $users = []): int
    {
        $count = count($users);
        $add   = "";
        for ($i = 0; $i < $count; ++$i) {
            $comma = ($i === ($count - 1)) ? '' : ', ';
            $add .= ':bind' . $i . $comma;
        }

        $sql = "
            DELETE FROM
                `wi_company_power_focus`
            WHERE
                `wc_idx` = :company
                AND `wp_idx` IN ( " . $add . " )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
        for ($i = 0; $i < $count; ++$i) {
            $stmt->bindParam(':bind' . $i, $users[$i], PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->rowCount();
    }
}
