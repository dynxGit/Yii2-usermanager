<?php

namespace dynx\models\behaviors;

use dynx\assets\PinAsset;
use yii\base\Behavior;
use dynx\Module;
use Yii;
use yii\bootstrap5\Html;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class UserPinBehavior extends Behavior
{

    /**
     * Generates new pin following module pinFormat 
     */
    public function generatePin()
    {
        $pin = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $numbers = "123456789";
        $mod = Module::getInstance();
        $format = $mod->pinFormat;
        foreach (explode("-", $format) as $idx => $f) {
            if ($idx > 0) $pin .= "-";
            $count = substr($f, 0, -1);
            $type = substr($f, -1);
            if (is_numeric($count)) {
                for ($i = 0; $i < $count; $i++) {
                    switch ($type) {
                        case "C":
                            $pin .=  substr($chars, rand(0, strlen($chars) - 1), 1);
                            break;
                        case "N":
                            $pin .= rand(0, 9);
                            break;
                    }
                }
            } else $pin .= $f;
        }
        return $this->owner->pin = $pin;
    }
    /**
     * Build HTML code to Show Pin in boxes (especially for e-mail)
     */

    public function GetPinHtmlCode()
    {
        $mod = Module::getInstance();
        $css = [
            'color_white' => "#fff",
            'color_dark' => "#1a8754",

            'div' => "text-align:center;margin:20px;height:50px;",
            'box' => "border-radius:5px;border:1px solid #ccc;width:50px;padding:10px 15px;font-weight:bold;font-size:24px;margin:0 5px;display:inline-block;",

        ];
        $text = "";
        $graybox = true;
        $pin = $this->owner->pin;
        for ($i = 0; $i < strlen($pin); $i++) {
            $value = substr($pin, $i, 1);
            if ($value == "-") {
                $graybox = false;
            } else {
                $color = $graybox ? "color:$css[color_white];border-color:$css[color_dark];background:$css[color_dark]" : "border-color:$css[color_dark];color:$css[color_dark];background_$css[color_white]";
                $text .= "<span style='$css[box];$color'>" . $value . "</span>";
            }
        }
        return "<div style='$css[div]'>$text</div>";
    }


    public function generatePinInputField()
    {
        $html = "<p class='text-danger'>" . Yii::t("dynx/form", "Account missing or activation overdued") . "</p>";
         $inputName = Html::getInputName($this->owner, 'pin');
        if ($this->owner->id) {

            $html = Html::activeHiddenInput($this->owner,'pin',['id'=>'pincode']);
            $html .=Html::error($this->owner,'pin');
            $module = Module::getInstance();
            $format = $module->pinFormat;
            $pin = $this->owner->pin;
            $graybox = true;
            $inputtype = substr($format, -1);

            for ($i = 0; $i < strlen($pin); $i++) {
                $value = substr($pin, $i, 1);
                $class = 'pinbox grey';
                if ($value == "-") {
                    $graybox = false;
                    $class = 'pinbox separator';
                }
                if (!$graybox && $class != "pinbox separator") {
                    $value = "<input type='".($inputtype=='N'?'number':'text')."' data-type='$inputtype' name='pin[]' required maxlength='1'>";
                    $class = 'pinbox input';
                }

                $html .= "<div class='$class'>$value</div>";
            }
           
        }

        PinAsset::register(Yii::$app->view);;
        return Html::tag('div', $html, ['id' => 'pinblock', 'data-input' => 'pincode']);
    }
}
