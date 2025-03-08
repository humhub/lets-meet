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
use Yii;
use yii\helpers\Html;

class TimeSlotPicker extends BasePicker
{
    public $itemClass = MeetingTimeSlot::class;

    public $minInput = 1;

    public function init()
    {
        $this->view->registerJsVar('isMeridiem', Yii::$app->formatter->isShowMeridiem());

        $js = <<<JS
window.timeSlotPickerBeforeInit = function(options) {
    options.createTag = function (params) {
        const isMeridiem = !!window.isMeridiem;
        const inputValue = params.term.trim().toLowerCase();
        
        const fullTimeRegex = /^([0-9]|[01][0-9]|2[0-3]):([0-5][0-9])$/;
        const fullPartialTimeRegex = /^(0?[0-9]|1[0-9]|2[0-3])(:([0-5]?([0-9])?)?)?$/;
        const twelveHourRegex = /^(0?[1-9]|1[0-2]):([0-5][0-9])\s?(a|am|p|pm)?$/i;
        const twelveHourPartialTimeRegex = /^(0?[1-9]|1[0-2])(:([0-5]?([0-9])?)?)?\s?(a|am|p|pm)?$/i;

        if (isMeridiem) {
            if (twelveHourRegex.test(inputValue)) {
                const match = inputValue.match(twelveHourRegex);
                const hour = match[1];
                const minute = match[2];
                const period = match[3] ? (match[3].toLowerCase().startsWith('a') ? 'AM' : 'PM') : 'AM';

                return {
                    id: `\${hour}:\${minute} \${period}`,
                    text: `\${hour}:\${minute} \${period}`
                };
            } else if (twelveHourPartialTimeRegex.test(inputValue)) {
                const match = inputValue.match(twelveHourPartialTimeRegex);
                let hour = match[1].padStart(2, '0');
                let minute = match[3] || '00';
                const period = match[5] ? (match[5].toLowerCase().startsWith('a') ? 'AM' : 'PM') : 'AM';
                
                if (minute.length === 1) minute += '0';
                
                return {
                    id: `\${hour}:\${minute} \${period}`.trim(),
                    text: `\${hour}:\${minute} \${period}`.trim()
                };
            }
        } else {
            if (fullTimeRegex.test(inputValue)) {
                const [hour, minute] = inputValue.split(':');
                return {
                    id: `\${hour.padStart(2, '0')}:\${minute}`,
                    text: `\${hour.padStart(2, '0')}:\${minute}`
                };
            } else if (fullPartialTimeRegex.test(inputValue)) {
                const match = inputValue.match(fullPartialTimeRegex);
                const hour = match[1].padStart(2, '0');
                let minute = match[3] || '00';
                
                if (minute.length === 1) minute += '0';

                return {
                    id: `\${hour}:\${minute}`,
                    text: `\${hour}:\${minute}`
                };
            }
        }

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
