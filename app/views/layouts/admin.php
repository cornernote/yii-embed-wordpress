<?php
/**
 * @var YiiEmbedController $this
 * @var string $content
 */

echo '<h1>' . $this->pageTitle . '</h1>';
if ($this->breadcrumbs) {
    echo TbHtml::breadcrumbs($this->getPageBreadcrumbs());
}
if ($this->menu) {
    $this->widget('bootstrap.widgets.TbNav', array(
        'type' => TbHtml::NAV_TYPE_TABS,
        'items' => $this->menu,
    ));
}

echo Yii::app()->user->multiFlash();
echo $content;