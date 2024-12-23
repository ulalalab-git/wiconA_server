<?php
namespace Middleware\Stack;

use Slim\Flash\Messages as SlimMessages;

/**
 *
 */
class Messages extends SlimMessages
{
    public function __construct()
    {
        parent::__construct($_SESSION, 'wimx');
        # debug_print_backtrace();
    }

    /**
     * [messenger description]
     * @return [type] [description]
     */
    public function messenger()
    {
        $this->storage[$this->storageKey] = $this->fromPrevious;
    }
}
