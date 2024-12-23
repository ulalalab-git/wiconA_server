<?php
namespace App\Handler\Responder;

/**
 *
 */
class Inform
{
    private $info = null;

    /**
     * [__construct description]
     * @param array $inform [description]
     */
    public function __construct(array $inform = [])
    {
        $this->info = $inform;
    }

    /**
     * [all description]
     * @return [type] [description]
     */
    public function all():  ? array
    {
        return $this->info;
    }

    /**
     * [get description]
     * @param  string|null $column  [description]
     * @param  string      $default [description]
     * @return [type]               [description]
     */
    public function get(string $column = null, string $default = null) :  ? string
    {
        return (isset($this->info[$column]) === true) ? $this->info[$column] : $default;
    }
}
