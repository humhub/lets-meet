<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentTagActiveQuery;
use humhub\modules\content\models\ContentTag;
use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\ui\form\widgets\BasePicker;
use humhub\modules\ui\view\components\View;
use yii\helpers\Html;


class TimeSlotPicker extends BasePicker
{
    public $itemClass = MeetingTimeSlot::class;

    public $minInput = 1;

    public function init()
    {
        $js = <<<JS
window.timeSlotPickerBeforeInit = function(options) {
    options.createTag = function (params) {
        const inputValue = params.term.trim();

        // Single digit: Convert to x:00 if x is in range 1-24
        const singleDigitRegex = /^([1-9]|1[0-9]|2[0-4])$/;

        // Full time: Validate hh:mm (hh: 1-24, mm: 00-59)
        const fullTimeRegex = /^([1-9]|1[0-9]|2[0-4]):([0-5][0-9])$/;

        if (singleDigitRegex.test(inputValue)) {
            // Convert single digit to x:00
            const hour = parseInt(inputValue, 10);
            return {
                id: `\${hour}:00`,
                text: `\${hour}:00`
            };
        } else if (fullTimeRegex.test(inputValue)) {
            // Validate full time format
            const [hour, minute] = inputValue.split(':');
            return {
                id: `\${hour}:\${minute}`,
                text: `\${hour}:\${minute}`
            };
        }

        // Invalid input: Ignore
        return null;
    }
    
    return options;
}
JS;

        $this->view->registerJs($js);

        return parent::init();
    }

    protected function getItemText($item)
    {
        return $item;
    }

    protected function getItemKey($item)
    {
        return $item;
    }

    protected function getItemImage($item)
    {
        return null;
    }

    protected function getData()
    {
        $result = parent::getData();
        unset($result['picker-url']);
        $result['before-init-callback'] = 'timeSlotPickerBeforeInit';
        $result['placeholder'] = $result['placeholder-more'] = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));

        return $result;
    }


    protected function getUrl()
    {
        return null;
    }

    protected function getSelectedOptions()
    {
        $this->selection = Html::getAttributeValue($this->model, $this->attribute) ?: [];

        $result = [];
        foreach ($this->selection as $item) {
            if (!$item) {
                continue;
            }

            $result[$this->getItemKey($item)] = $this->buildItemOption($item);
        }
        return $result;
    }
}
