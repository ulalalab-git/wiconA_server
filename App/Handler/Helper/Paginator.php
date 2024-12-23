<?php
namespace App\Handler\Helper;

/**
 *
 */
class Paginator
{
    /**
     * [generator description]
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public function generator($config = []): array
    {
        $page     = empty($config['page']) ? 1 : $config['page'];
        $limit    = empty($config['limit']) ? 15 : $config['limit'];
        $block    = empty($config['block']) ? 15 : $config['block'];
        $totalRow = (intval($config['total']) < 0) ? 0 : $config['total'];

        $totalPage  = ceil($totalRow / $limit);
        $totalBlock = ceil($totalPage / $block);

        $page = ($totalPage < $page) ? intval($totalPage) : $page;
        $page = ($page <= 1) ? 1 : $page;

        $offset = ($limit * $page) - $limit;
        // $page       = ($offset > $totalRow) ? abs(ceil($limit / $block)) : $page ;

        $nowBlock = floor($block / 2);

        $checkNum  = 1;
        $startPage = ($page - $nowBlock) + $checkNum;
        $startPage = ($totalPage - $startPage < $block) ? $totalPage - $block + $checkNum : $startPage;
        $startPage = ($startPage <= 1) ? 1 : $startPage;

        $lastPage = $block;
        $lastPage = ($page >= $nowBlock) ? $page + $nowBlock : $lastPage;
        $lastPage = ($lastPage >= $totalPage) ? $totalPage : $lastPage;

        /*
        $nowBlock   = ceil($page / $block);

        $startPage  = ($nowBlock * $block) - ($block - 1);
        $startPage  = ($startPage <= 1) ? 1 : $startPage;

        $lastPage   = ($nowBlock * $block);
        $lastPage   = ($lastPage >= $totalPage) ? $totalPage : $lastPage;
         */

        $nextPage = $page + 1;
        // $nextPage   = ($nowBlock * $block) + 1;
        $nextPage = ($nextPage >= $totalPage) ? $totalPage : $nextPage;

        $prevPage = $page - 1;
        // $prevPage   = ($nowBlock * $block) - $block;
        $prevPage = ($prevPage <= 0) ? 1 : $prevPage;

        return [
            'page'       => intval($page),
            'limit'      => intval($limit),
            'block'      => intval($block),
            'offset'     => intval($offset),
            'totalRow'   => intval($totalRow),
            'totalPage'  => intval($totalPage),
            'totalBlock' => intval($totalBlock),
            'nowBlock'   => intval($nowBlock),
            'startNum'   => intval($startPage),
            'lastNum'    => intval($lastPage),
            'startPage'  => 1,
            'lastPage'   => intval($totalPage),
            'prevPage'   => intval($prevPage),
            'nextPage'   => intval($nextPage),
        ];
    }
}
