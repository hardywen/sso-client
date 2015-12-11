<?php

namespace Hardywen\SSOClient\Model;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_sso_server';

    protected $table = 'images';

}
