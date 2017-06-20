<?php
use mdm\admin\components\MenuHelper;
use dmstr\widgets\Menu;
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <?=
            Menu::widget([
                'options' => ['class' => 'sidebar-menu'],
                'items' => MenuHelper::getAssignedMenu(Yii::$app->user->id)
            ]);
        ?>
    </section>
</aside>