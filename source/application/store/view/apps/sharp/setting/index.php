<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl"> 整点秒杀设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">未支付订单 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-u-sm-6 x-pl-0">
                                        <input type="number" class="tpl-form-input" name="basic[order][order_close]"
                                               value="<?= $values['order']['order_close'] ?>" required>
                                    </div>
                                    <label class="am-u-sm-6 am-form-label am-text-left">分钟后自动关闭</label>
                                    <div class="help-block am-u-sm-12">
                                        <small>秒杀订单下单未付款，n分钟后自动关闭，设置0则不自动关闭</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group am-padding-top">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 是否支持分销 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <label class="am-radio-inline">
                                        <input type="radio" name="basic[is_dealer]" value="1"
                                            <?= $values['is_dealer'] == 1 ? 'checked' : '' ?>
                                               data-am-ucheck required>
                                        开启
                                    </label>
                                    <label class="am-radio-inline">
                                        <input type="radio" name="basic[is_dealer]" value="0"
                                               data-am-ucheck <?= $values['is_dealer'] == 0 ? 'checked' : '' ?>>
                                        关闭
                                    </label>
                                    <div class="help-block am-padding-top-xs">
                                        <small>注：整点秒杀订单是否参与分销</small>
                                    </div>
                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-sm am-btn-secondary">提交
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
