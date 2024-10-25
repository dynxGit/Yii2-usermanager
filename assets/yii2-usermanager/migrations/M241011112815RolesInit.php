<?php

namespace dynx\migrations;

use yii\db\Migration;

/**
 * Class M241011112815RolesInit
 */
class M241011112815RolesInit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = \Yii::$app->authManager;
        $this->alterColumn($auth->assignmentTable,'user_id','int(10) unsigned');
        $this->dropForeignKey('fk_auth_assigment_user',$auth->assignmentTable);
        $this->addForeignKey('fk_auth_assigment_user',$auth->assignmentTable,'user_id','user','id','cascade','cascade');
        $roles = [
            '0_SYS_ADM' => "Sytem administrator",
            '1_SITE_ADM' => "Site administrator",
            '2_EDITOR' => "Site editor",

        ];
        foreach ($roles as $name => $description) {
            $role = $auth->createRole($name);
            $role->description = $description;
            $auth->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();
    }
}
