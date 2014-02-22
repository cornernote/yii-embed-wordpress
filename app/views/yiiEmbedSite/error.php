<?php
/**
 * @var YiiEmbedSiteController $this
 * @var string $code
 * @var string $message
 */

echo '<h2>' . Yii::t('app', 'Error') . ' ' . $code . '</h2>';
echo '<div class="error">' . CHtml::encode($message) . '</div>';
