<?php
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">汉典</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <?php
                $url = Yii::$app->urlManager->createUrl('appointment/getcount');
                $script = <<< JS
                    $(document).ready(function() {
                        setInterval(getCount,60000);
                    });

                    function getCount(){
                        $.get("$url",function (data,status) {
                                if(data.count > 0){
                                    $('#PengdingCount').text(data.count);
                                }
                            });

                        //$('#undo').text(second);
                    }
JS;
                $this->registerJs($script);
                ?>
                <li class="dropdown notifications-menu">
                    <a href="<?php echo Yii::$app->urlManager->createUrl('appointment/index'); ?>"
                       class="dropdown-toggle">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning" id="PengdingCount"></span>
                    </a>
                </li>


                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= Yii::$app->user->identity->username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle"
                                 alt="User Image"/>

                            <p>
                                <?= Yii::$app->user->identity->username; ?>
                                <!--                                <small>Member since Nov. 2012</small>-->
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!--                        <li class="user-body">-->
                        <!--                            <div class="col-xs-4 text-center">-->
                        <!--                                <a href="#">Followers</a>-->
                        <!--                            </div>-->
                        <!--                            <div class="col-xs-4 text-center">-->
                        <!--                                <a href="#">Sales</a>-->
                        <!--                            </div>-->
                        <!--                            <div class="col-xs-4 text-center">-->
                        <!--                                <a href="#">Friends</a>-->
                        <!--                            </div>-->
                        <!--                        </li>-->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <!--                            <div class="pull-left">-->
                            <!--                                <a href="#" class="btn btn-default btn-flat">Profile</a>-->
                            <!--                            </div>-->
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <!--                <li>-->
                <!--                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>-->
                <!--                </li>-->
            </ul>
        </div>
    </nav>
</header>
