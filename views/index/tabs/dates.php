<?php



/**
 * @var \yii\widgets\ActiveForm $form
 * @var \humhub\modules\letsMeet\models\forms\DatesForm $model
 * @var \yii\web\View $this
 */

?>

<?php foreach ($model->dates as $day) : ?>
    <?= $this->render('date_row', ['form' => $form, 'model' => $model, 'day' => $day]) ?>
<?php endforeach; ?>
