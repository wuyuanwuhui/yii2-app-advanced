<?php
use backend\assets\AppAsset;

/* @var $this yii\web\View */

$this->registerJsFile('@web/statics/my-assets/my.js', ['depends'=>'backend\assets\AppAsset']);

$this->registerJsFile('@web/statics/js/jquery-ui-1.9.2.custom.min.js', ['depends'=>'backend\assets\AppAsset']);
//$this->registerJsFile('@web/statics/js/sliders.js', ['depends'=>'backend\assets\AppAsset']);


?>

<section class="wrapper site-min-height">
    <div class="row">
        <h1>This is test page</h1>
        <p>Yes we can start now ...</p>
        <div class="col-lg-12">
            <a class="btn btn-warning" data-toggle="modal" href="#myModal5">
                Large
            </a>
            <!-- vertical center large Modal  start -->
            <div class="modal fade modal-dialog-center" id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Modal Tittle</h4>
                            </div>
                            <div class="modal-body">

                                Body goes here...

                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                <button class="btn btn-warning" type="button"> Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- vertical center large Modal end -->

            <div class="alert alert-block alert-danger fade in">
                <button data-dismiss="alert" class="close close-sm" type="button">
                    <i class="fa fa-times"></i>
                </button>
                <strong>Oh snap!</strong> Change a few things up and try submitting again.
            </div>



        </div>
    </div>
</section>
