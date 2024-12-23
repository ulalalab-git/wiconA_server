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
     * @param  array  $companys [description]
     * @param  string $access   [description]
     * @return [type]           [description]
     */
    public function confirm(array $companys = [], string $access = 'Y'): int
    {
        if (empty($companys) === true) {
            return 0;
        }

        $count = count($companys);
        $add   = "";
        for ($i = 0; $i < $count; ++$i) {
            $comma = ($i === ($count - 1)) ? '' : ', ';
            $add .= ':bind' . $i . $comma;
        }

        $sql = "
            UPDATE
                `wi_company`
            SET
                `wc_access` = :access
            WHERE
                `wc_idx` IN ( " . $add . " )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':access', $access, PDO::PARAM_STR);
        for ($i = 0; $i < $count; ++$i) {
            $stmt->bindParam(':bind' . $i, $companys[$i], PDO::PARAM_INT);
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
                    $type = '`company`.`wc_name`';
                    break;
                case 'tel':
                    $type = '`company`.`wc_tel`';
                    break;
                case 'ceo':
                    $type = '`company`.`wc_ceo`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_company` AS `company`
                LEFT JOIN (
                    SELECT
                        `company_user`.`wc_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_company_user` AS `company_user`
                    GROUP BY
                        `company_user`.`wc_idx`
                ) AS `company_user` ON (`company_user`.`wc_idx` = `company`.`wc_idx`)
                LEFT JOIN (
                    SELECT
                        `company_power_focus`.`wc_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_company_power_focus` AS `company_power_focus`
                    GROUP BY
                        `company_power_focus`.`wc_idx`
                ) AS `company_power_focus` ON (`company_power_focus`.`wc_idx` = `company`.`wc_idx`)
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
     * @param  string|null $companyName [description]
     * @return [type]                   [description]
     */
    public function exist(string $companyName = null): int
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    `wi_company`
                WHERE
                    `wc_name` = :name
                LIMIT 1
            ) AS row
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $companyName, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['row'];
    }

    /**
     * [get description]
     * @param  string|null $email [description]
     * @return [type]             [description]
     */
    public function get(int $company = 0): array
    {
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
                `wi_company` AS `company`
            WHERE
                `company`.`wc_idx` = :company
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
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
    public function lists(array $condi = [], array $privileage = [],int $page = 0, int $limit = 15): array
    {
        $add = "";
        if (empty($condi['type']) === false && empty($condi['keyword']) === false) {
            $type = '';
            switch ($condi['type']) {
                case 'name':
                    $type = '`company`.`wc_name`';
                    break;
                case 'tel':
                    $type = '`company`.`wc_tel`';
                    break;
                case 'ceo':
                    $type = '`company`.`wc_ceo`';
                    break;

            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $join =' ';

        if (empty($privileage['type']) === false && empty($privileage['param']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'agent':
                    $join .= " LEFT JOIN  wi_agent_company as AgentCompany ON AgentCompany.wc_idx = company.wc_idx ";
                    $add .= " AND  (AgentCompany.wa_idx = ".$privileage['param']."  OR AgentCompany.wa_idx IS NULL ) ";
                    break;
                case 'company':
                    $add .= " AND  company_user.wc_idx = ".$privileage['param']."  ";
                    break;
                case 'user':
                    $add .= " AND  1 = 2 ";
                    break;
            }
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
                `company`.`wc_create_date` AS `company_create_date`,
                IFNULL(`company_user`.`cnt`, 0) AS `comapny_user_cnt`,
                IFNULL(`company_power_focus`.`cnt`, 0) AS `company_power_focus_cnt`
            FROM
                `wi_company` AS `company`
                LEFT JOIN (
                    SELECT
                        `company_user`.`wc_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_company_user` AS `company_user`
                    GROUP BY
                        `company_user`.`wc_idx`
                ) AS `company_user` ON (`company_user`.`wc_idx` = `company`.`wc_idx`)
                LEFT JOIN (
                    SELECT
                        `company_power_focus`.`wc_idx`,
                        COUNT(1) AS `cnt`
                    FROM
                        `wi_company_power_focus` AS `company_power_focus`
                    GROUP BY
                        `company_power_focus`.`wc_idx`
                ) AS `company_power_focus` ON (`company_power_focus`.`wc_idx` = `company`.`wc_idx`)
                ". $join ."
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `company`.`wc_idx` DESC
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
     * [register description]
     * @param  array  $bind [description]
     * @return [type]       [description]
     */
    public function register(array $bind = []): int
    {
        $sql = "
            INSERT INTO
                `wi_company`
                (
                    `wc_name`,
                    `wc_ceo`,
                    `wc_business`,
                    `wc_tel`,
                    `wc_zip`,
                    `wc_address`,
                    `wc_address_detail`,
                    `wc_access`,
                    `wc_create_date`
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

        return $dbh->lastInsertId();
    }

    /**
     * [restore description]
     * @param  int|integer  $company  [description]
     * @param  bool|boolean $isRepair [description]
     * @return [type]                 [description]
     */
    public function restore(int $company = 0, bool $isRepair = true): int
    {
        $set = "`wc_delete_date` = NOW()";
        if ($isRepair === true) {
            $set = "`wc_delete_date` = '0000-00-00 00:00:00'";
        }

        $sql = "
            UPDATE
                `wi_company`
            SET
                " . $set . "
            WHERE
                `wc_idx` = :company
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [modify description]
     * @param  int|integer $company [description]
     * @param  array       $bind    [description]
     * @return [type]               [description]
     */
    public function modify(int $company = 0, array $bind = []): int
    {
        $sql = "
            UPDATE
                `wi_company`
            SET
                `wc_name` = :name,
                `wc_ceo` = :ceo,
                `wc_business` = :business,
                `wc_tel` = :tel,
                `wc_zip` = :zip,
                `wc_address` = :address,
                `wc_address_detail` = :address_detail
            WHERE
                `wc_idx` = :company
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
        $stmt->bindParam(':company', $company, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
