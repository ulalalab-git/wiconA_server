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
    public function count(int $agent = 0, array $condi = []): int
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
                `wi_agent_user` AS `agent_user`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `agent_user`.`wu_idx`)
            WHERE
                1 = 1
                AND `agent_user`.`wa_idx` = :agent
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
            'wu_idx',
        ];
        $upsert = [];
        foreach ($users as $user) {
            $upsert[] = [
                'wa_idx' => $agent,
                'wu_idx' => $user,
            ];
        }

        $onDulpe = [];
        foreach ($columns as $column) {
            $onDulpe[] = " `" . $column . "` = VALUES(`" . $column . "`)";
        }
        $after = " ON DUPLICATE KEY UPDATE " . implode(', ', $onDulpe);

        $dbh = $this->maria;
        $sql = "INSERT INTO `wi_agent_user` (`" . implode('`,`', $columns) . "`) VALUES ";
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
                `wi_agent_user` AS `agent_user`
                LEFT JOIN `wi_user` AS `user` ON (`user`.`wu_idx` = `agent_user`.`wu_idx`)
            WHERE
                1 = 1
                AND `agent_user`.`wa_idx` = :agent
                " . $add . "
            ORDER BY
                `user`.`wu_idx` DESC
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
                `wi_agent_user`
            WHERE
                `wa_idx` = :agent
                AND `wu_idx` IN ( " . $add . " )
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
