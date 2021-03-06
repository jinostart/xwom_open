<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = Yii::$app->name;
$this->registerCssFile('@web/css/login.css');
?>
<body class="login-bg">
<div class="login layui-anim layui-anim-up">
    <div class="message"><?=Html::encode($this->title)?></div>
    <div id="darkbannerwrap"></div>
    <form class="layui-form">
        <input id="public_key" type="hidden" value="<?=Yii::$app->params['public_key']?>">
        <input name="_csrfBackend" type="hidden" value="<?=\Yii::$app->request->csrfToken?>" />
        <input name="username" placeholder="用户名"  type="text" lay-verify="required" class="layui-input" >
        <hr class="hr15">
        <input name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input" id="password">
        <hr class="hr15">
        <input name="verifyCode" maxlength="6" lay-verify="required" lay-reqtext="请填写验证码！"  placeholder="验证码"  type="text" class="layui-input" style="width: 50%; display: inline-block" />
        <?php echo \yii\captcha\Captcha::widget(['name'=>'verifyCode','captchaAction'=>'captcha','imageOptions'=>['id'=>'captchaimg', 'title'=>'换一个', 'alt'=>'换一个', 'style'=>'cursor:pointer;'],'template'=>'{image}']); ?>
        <hr class="hr15">
        <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="button" >
        <hr class="hr20" >
    </form>
</div>
<script type="text/javascript" src="<?php echo Yii::$app->urlManager->baseUrl?>/js/md5.js"></script>
<script type="text/javascript" src="<?php echo Yii::$app->urlManager->baseUrl?>/js/jsencrypt.min.js"></script>
<script>
    layui.use(['form'], function(){
        var form = layui.form
        form.on('submit(login)', function(data){
            var encrypt = new JSEncrypt();
            var public_key =$("#public_key").val();
            encrypt.setPublicKey(public_key);
            var passwd = data.field.password;
            var encrypted = encrypt.encrypt(passwd + ',' + hex_md5(passwd));
            data.field.password = encrypted
            $.post("<?=Url::toRoute(['login'])?>",data.field,function(jsonData){
                if(jsonData.status == '403'){
                    layer.open({
                        type: 2,
                        offset: 't',
                        content:"<?=\yii\helpers\Url::toRoute('site/update-pwd') ?>?user_id="+jsonData.user_id,
                        area:['50%','50%'],
                        title:'您为初始账户，为保证安全需修改密码',
                    });
                }else{
                    var ic = (jsonData.status == true) ? icon.ICON_OK : icon.ICON_ERROR;
                    layer.msg(jsonData.msg,{icon:ic},function () {
                        (jsonData.status == true) ? window.location.href = jsonData.goBack :""
                    });
                }
            })
        });
    })
</script>
</body>
