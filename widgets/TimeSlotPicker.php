<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\ui\form\widgets\BasePicker;
use humhub\helpers\Html;
use Yii;

class TimeSlotPicker extends BasePicker
{
    public $itemClass = MeetingTimeSlot::class;

    public $minInput = 1;

    /**
     * @inheritdoc
     */
    public function getSource(): ?array
    {
        // Each typed time is offered as the item, normalized by the `letsMeet.time` source (humhub.lets-meet.js)
        return ['source' => 'letsMeet.time', 'params' => ['meridiem' => Yii::$app->formatter->isShowMeridiem()]];
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
