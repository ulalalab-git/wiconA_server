<?php
namespace App\User\Domain;

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
     * @param  array  $users  [description]
     * @param  string $access [description]
     * @return [type]         [description]
     */
    public function confirm(array $users = [], string $access = 'Y'): int
    {
        if (empty($users) === true) {
            return 0;
        }

        $count = count($users);
        $add   = "";
        for ($i = 0; $i < $count; ++$i) {
            $comma = ($i === ($count - 1)) ? '' : ', ';
            $add .= ':bind' . $i . $comma;
        }

        $sql = "
            UPDATE
                `wi_user`
            SET
                `wu_access` = :access
            WHERE
                `wu_idx` IN ( " . $add . " )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':access', $access, PDO::PARAM_STR);
        for ($i = 0; $i < $count; ++$i) {
            $stmt->bindParam(':bind' . $i, $users[$i], PDO::PARAM_INT);
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
                `wi_user` AS `user`
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

        if (empty($privileage['type']) === false && empty($privileage['wu_idx']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'company':
                    $add .= " AND  company.wu_idx = (select wc_idx from wi_company_user where wu_idx = ".$privileage['wu_idx']."  ) ";
                    break;
                case 'agent':
                    $add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['wu_idx']."  ) ";
                    break;
                case 'user':
                    $add .= " AND  user.wu_idx = ".$privileage['wu_idx']." ";
                    break;
            }
        }


        $sql = "
            SELECT
                COUNT(1) AS `cnt`
            FROM
                `wi_user` AS `user`
                 LEFT JOIN `wi_agent_user` AS `agent` ON agent.wu_idx = user.wu_idx
                LEFT JOIN `wi_company_user` AS `company` ON company.wu_idx = user.wu_idx
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
     * @param  string|null $email [description]
     * @return [type]             [description]
     */
    public function exist(string $email = null): int
    {
        $sql = "
            SELECT EXISTS(
                SELECT
                    1
                FROM
                    `wi_user`
                WHERE
                    `wu_email` = :email
                LIMIT 1
            ) AS row
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();

        return $row['row'];
    }

    /**
     * [find description]
     * @param  string|null $email [description]
     * @return [type]             [description]
     */
    public function find(string $email = null): array
    {
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
                `wi_user` AS `user`
            WHERE
                `user`.`wu_email` = :email
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
            $row = [];
        }

        return $row;
    }

    /**
     * [get description]
     * @param  int|integer $user [description]
     * @return [type]            [description]
     */
    public function get(int $user = 0): array
    {
        $sql = "SELECT  
              *,
              (SELECT IF(wa_idx IS NOT NULL, wa_idx,'') FROM `wi_agent_user` WHERE wu_idx = T.user_idx) AS is_agent,
              (SELECT IF(wc_idx IS NOT NULL, wc_idx,'') FROM `wi_company_user` WHERE wu_idx = T.user_idx) AS is_company 
        FROM (
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
                `wi_user` AS `user`
            WHERE
                `user`.`wu_idx` = :user
            LIMIT 1
            )T limit 1
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

        //$add .= " AND  agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = 5  ) ";
        //$add .= " AND  company.wc_idx = (select wc_idx from wi_company_user where wu_idx = 5  ) ";

        $sql = "
            SELECT
                `user`.`wu_idx` AS `user_idx`,
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
                `wi_user` AS `user`
                LEFT JOIN `wi_agent_user` AS `agent` ON agent.wu_idx = user.wu_idx
                LEFT JOIN `wi_company_user` AS `company` ON company.wu_idx = user.wu_idx
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `user`.`wu_idx` DESC
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

    /** //xptmxm
     * [lists description]
     * @param  array       $condi [description]
     * @param  int|integer $page  [description]
     * @param  int|integer $limit [description]
     * @return [type]             [description]
     */
    public function lists_privileage(array $condi = [],array $privileage = [], int $page = 0, int $limit = 15): array
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

        if (empty($privileage['type']) === false && empty($privileage['wu_idx']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'agent':
                    $add .= " AND  ( agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['wu_idx']." limit 1  ) 
                     OR (agent.wa_idx IS NULL AND user.wu_level = 1 AND company.wc_idx IS NULL  ) 
                     ) ";
                    break;
                case 'company':
                    $add .= " AND  user.wu_idx = ".$privileage['wu_idx']."   ";
                    break;

                case 'user':
                    $add .= " AND  user.wu_idx = ".$privileage['wu_idx']." ";
                    break;
            }
        }



        //search
        if (empty($privileage['type']) === false && empty($privileage['search']) === false) {
            $type = '';
            switch ($privileage['type']) {
                case 'agent':
                    $add .= " AND  ( agent.wa_idx = (select wa_idx from wi_agent_user where wu_idx = ".$privileage['search']." limit 1  ) 
                     OR (agent.wa_idx IS NULL AND user.wu_level = 1  ) 
                     ) ";
                    break;
                case 'company':
                    $add .= " AND  company.wc_idx = ".$privileage['search']."   ";
                    break;

                case 'user':
                    $add .= " AND  user.wu_idx = ".$privileage['search']." ";
                    break;
            }
        }


        $sql = "
            SELECT
                `user`.`wu_idx` AS `user_idx`,
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
                `wi_user` AS `user`
                LEFT JOIN `wi_agent_user` AS `agent` ON agent.wu_idx = user.wu_idx
                LEFT JOIN `wi_company_user` AS `company` ON company.wu_idx = user.wu_idx
            WHERE
                1 = 1
                " . $add . "
            ORDER BY
                `user`.`wu_idx` DESC
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
     * @param  int|integer $user [description]
     * @param  array       $bind [description]
     * @return [type]            [description]
     */
    public function modify(int $user = 0, array $bind = []): int
    {
        $set = "";
        if (empty($bind['passwd']) === false) {
            $set = " `wu_passwd` = :passwd, ";
        }
        $sql = "
            UPDATE
                `wi_user`
            SET
                " . $set . "
                `wu_name` = :name,
                `wu_name_last` = :name_last,
                `wu_tel` = :tel,
                `wu_access` = :access
            WHERE
                `wu_idx` = :user
            LIMIT 1
        ";
        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        if (empty($bind['passwd']) === false) {
            $stmt->bindParam(':passwd', $bind['passwd'], PDO::PARAM_STR);
        }
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':name_last', $bind['name_last'], PDO::PARAM_STR);
        $stmt->bindParam(':tel', $bind['tel'], PDO::PARAM_STR);
        $stmt->bindParam(':access', $bind['access'], PDO::PARAM_STR);
        $stmt->bindParam(':user', $user, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [passwd description]
     * @param  string|null $email  [description]
     * @param  string|null $passwd [description]
     * @return [type]              [description]
     */
    public function passwd(string $email = null, string $passwd = null): int
    {
        $sql = "
            UPDATE
                `wi_user`
            SET
                `wu_passwd` = :passwd
            WHERE
                `wu_email` = :email
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':passwd', $passwd, PDO::PARAM_STR);
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
                `wi_user`
                (
                    `wu_email`,
                    `wu_passwd`,
                    `wu_name`,
                    `wu_name_last`,
                    `wu_tel`,
                    `wu_level`,
                    `wu_access`,
                    `wu_comment`,
                    `wu_create_date`
                )
            VALUES
                (
                    :email,
                    :passwd,
                    :name,
                    :name_last,
                    :tel,
                    :level,
                    :access,
                    :comment,
                    NOW()
                )
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $bind['email'], PDO::PARAM_STR);
        $stmt->bindParam(':passwd', $bind['passwd'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $bind['name'], PDO::PARAM_STR);
        $stmt->bindParam(':name_last', $bind['name_last'], PDO::PARAM_STR);
        $stmt->bindParam(':tel', $bind['tel'], PDO::PARAM_STR);
        $stmt->bindParam(':level', $bind['level'], PDO::PARAM_INT);
        $stmt->bindParam(':access', $bind['access'], PDO::PARAM_STR);
        $stmt->bindParam(':comment', $bind['comment'], PDO::PARAM_STR);
        $stmt->execute();

        return $dbh->lastInsertId();
    }

    /**
     * [restore description]
     * @param  int|integer  $user     [description]
     * @param  bool|boolean $isRepair [description]
     * @return [type]                 [description]
     */
    public function restore(int $user = 0, bool $isRepair = true): int
    {
        $set = "`wu_delete_date` = NOW()";
        if ($isRepair === true) {
            $set = "`wu_delete_date` = '0000-00-00 00:00:00'";
        }

        $sql = "
            UPDATE
                `wi_user`
            SET
                " . $set . "
            WHERE
                `wu_idx` = :user
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * [verification description]
     * @param  string|null $email  [description]
     * @param  string      $access [description]
     * @return [type]              [description]
     */
    public function verification(string $email = null, string $access = 'N'): int
    {
        $sql = "
            UPDATE
                `wi_user`
            SET
                `wu_access` = :access
            WHERE
                `wu_email` = :email
            LIMIT 1
        ";

        $dbh  = $this->maria;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':access', $access, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount();
    }
}
