<?php
?>
<div class="row">
    <div class="col-lg-12">
        <h6>ตัวอย่างก่อนพิมพ์</h6>
    </div>
</div>
<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
        <iframe id="iFramePdf" src="<?=\Yii::$app->getUrlManager()->baseUrl?>/uploads/company1/sliptax/slip_tax.pdf" style="display:nonex;"></iframe>
    </div>
    <div class="col-lg-4"></div>
</div>
<?php
$js=<<<JS
$(function(){
   myPrint(); 
});
function myPrint(){
        var getMyFrame = document.getElementById('iFramePdf');
        getMyFrame.focus();
        getMyFrame.contentWindow.print();
}
JS;
$this->registerJs($js,static::POS_END);
?>