<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>表单代码</title>
    <link rel="stylesheet" type="text/css" href="/yeeui/css/base.css">
    <link type="text/css" rel="stylesheet" href="/yeeui/css/yeeui.css"/>
    <link type="text/css" rel="stylesheet" href="/yeeui/icofont/css/icofont.css"/>
    <script src="/yeeui/js/jquery-1.12.3.min.js"></script>
    <script src="/yeeui/js/yee.js"></script>
</head>
<body>
<div class="yeeui-caption"><a href="{$this->getReferrer()}" class="s-back"><i class="icofont icofont-reply"></i></a>&nbsp;&nbsp;生成代码</div>
<div class="yeeui-content">
    <div class="yeeui-form">
        <form>
            <div class="form-panel">
                <div class="panel-title">
                    <i class="icofont icofont-pencil-alt-3"></i>
                    <h3>查看表单模型代码</h3>
                </div>
                <div class="panel-content">
                    <div class="form-group" id="row_userName">
                        <label class="form-label">代码：</label>
                        <div class="form-box">
                            {box name="code" type='textarea' style='width:100%; height:900px;' value=$code}
                        </div>
                    </div>
                </div>
                <div class="form-submit">
                    <label class="form-label"></label>
                    <div class="form-box">
                        <a href="{$this->getReferrer()}" class="form-btn back">返回</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>