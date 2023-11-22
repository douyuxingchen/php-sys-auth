<?php

namespace Douyuxingchen\PhpSysAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionInterface;

class BaseModel extends Model
{
    protected $connection = 'custom_connection'; // 设置默认连接

    public function getConnectionName()
    {
        return $this->connection;
    }

//    public function __construct(ConnectionInterface $connection)
//    {
//        parent::__construct();
//        $this->connectionInstance = $connection;
//    }

    public function __construct($connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }
}