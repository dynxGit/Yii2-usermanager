<?php

namespace dynx\components;

use Yii;

class DY
{
    public static function UM()
    {
        return Yii::$app->getModule('dynx');
    }
}
