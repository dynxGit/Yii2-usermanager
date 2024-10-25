<?php

namespace dynx\migrations;

use yii\db\Migration;

use dynx\Module;
use dynx\models\User;

/**
 * Class M241010230634UserAddSua
 */
class M241010230634UserAddSua extends Migration
{
	public function safeUp()
	{
		$mod = Module::getInstance();
		if ($mod->SUAemail) {

		/* Add SUA role */
		$auth = \Yii::$app->authManager;
		$auth->removeAll();
			$admin = $auth->createRole('0_GUEST');
			$admin->description = 'Guest';
			$auth->add($admin);
		$admin = $auth->createRole('SUA');
        $admin->description = 'SuperUser';
        $auth->add($admin);

			$user = new User();
			$user->scenario = 'create';
			$user->status = User::STATUS_PENDING;
			$user->password = "Nee8tahH";
			$user->encryptPassword("password", []);
			$user->roles = ['SUA'];
			$user->name = 'SUA';
			$user->email = isset($mod->SUAemail) ? $mod->SUAemail : 'sua@dynx.hu';
			$user->save();

			/* Add SUA role */
	//		$auth->assign($admin, $user->id);
		} else {
			echo  "SUA email is not defined in Module config!\n";
			return false;
		}
	}

	public function safeDown()
	{
		$mod = Module::getInstance();
		$suaEmail = isset($mod->SUAemail) ? $mod->SUAemail : 'sua@dynx.hu';
		$user = User::findByEmail($suaEmail);
		if ($user) {
			$user->delete();
		}

		/* REMOVE roles */
		$auth = \Yii::$app->authManager;
		$admin = $auth->getRole('SUA');
		if ($admin)
		$auth->remove($admin);
	}

}
