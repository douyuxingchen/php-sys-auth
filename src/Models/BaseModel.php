<?php

namespace Douyuxingchen\PhpSysAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;

class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $connection = Config::get('sys_auth.mysql_connect_name');
        $this->setConnection($connection);
    }
}