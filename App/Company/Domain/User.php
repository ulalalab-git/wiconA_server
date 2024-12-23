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
class User
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
                case 'email':
                    $type = '`user`.`wu_email`';
                    break;
                case 'name':
                    $type = '`user`.`wu_name`';
                    break;
                case 'tel':
                    $type = '`user`.`wu_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_company_user` AS `company_user`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `company_user`.`wu_idx`)
            WHERE
                1 = 1
                AND `company_user`.`wc_idx` = :company
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
            'wu_idx',
        ];
        $upsert = [];
        foreach ($users as $user) {
            $upsert[] = [
                'wc_idx' => $company,
                'wu_idx' => $user,
            ];
        }

        $onDulpe = [];
        foreach ($columns as $column) {
            $onDulpe[] = " `" . $column . "` = VALUES(`" . $column . "`)";
        }
        $after = " ON DUPLICATE KEY UPDATE " . implode(', ', $onDulpe);

        $dbh = $this->maria;
        $sql = "INSERT INTO `wi_company_user` (`" . implode('`,`', $columns) . "`) VALUES ";
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
     * [get description]
     * @param  string|null $email [description]
     * @return [type]             [description]
     */
    public function get(int $user = 0): array
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
                `wi_company_user` AS `company_user`
                LEFT JOIN `wi_company` AS `company` ON (`company`.`wc_idx` = `company_user`.`wc_idx`)
            WHERE
                1 = 1
                AND `company_user`.`wu_idx` = :user
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
            $row = [];
        }

        return $row;
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
                case 'email':
                    $type = '`user`.`wu_email`';
                    break;
                case 'name':
                    $type = '`user`.`wu_name`';
                    break;
                case 'tel':
                    $type = '`user`.`wu_tel`';
                    break;
            }
            $add .= " AND " . $type . " LIKE CONCAT('%', :keyword, '%') ";
        }

        $sql = "
            SELECT
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
                `user`.`wu_create_date` AS `user_create_date`
            FROM
                `wi_company_user` AS `company_user`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `company_user`.`wu_idx`)
            WHERE
                1 = 1
                AND `company_user`.`wc_idx` = :company
                " . $add . "
            ORDER BY
                `user`.`wu_idx` DESC
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
                `wi_company_user`
            WHERE
                `wc_idx` = :company
                AND `wu_idx` IN ( " . $add . " )
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
