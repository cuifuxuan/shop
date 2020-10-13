<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="tips am-margin-top-sm">
                                <div class="pre">
                                    <p class="">
                                        1. 订阅消息仅支持小程序 "<strong>服装/鞋/箱包</strong>" 类目，请登录 "<a
                                                href="https://mp.weixin.qq.com" target="_blank">小程序运营平台</a>"，左侧菜单栏 "设置"
                                        - "基本设置" - "服务类目" 中添加
                                    </p>
                                    <p class="am-padding-top-xs">2. 建议使用当前页面下方的 " <a href="#shuttle">一键添加</a>"
                                        按钮，自动完成订阅消息模板的添加</p>
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">订单消息通知</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 新订单提醒 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input" name="submsg[order][payment][template_id]"
                                           value="<?= $values['order']['payment']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[订单编号、下单时间、订单金额、商品名称]</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 订单发货通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           name="submsg[order][delivery][template_id]"
                                           value="<?= $values['order']['delivery']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[订单号、商品名称、收货人、收货地址、物流公司]</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 售后状态通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input" name="submsg[order][refund][template_id]"
                                           value="<?= $values['order']['refund']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[售后类型、状态、订单编号、申请时间、申请原因]</small>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">拼团进度通知</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 拼团进度通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           name="submsg[sharing][active_status][template_id]"
                                           value="<?= $values['sharing']['active_status']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[拼团商品、拼团价格、成团人数、拼团进度、温馨提示]</small>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">分销商消息通知</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 分销商入驻审核通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input" name="submsg[dealer][apply][template_id]"
                                           value="<?= $values['dealer']['apply']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[申请时间、审核状态、审核时间、备注信息]</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现成功通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           name="submsg[dealer][withdraw_01][template_id]"
                                           value="<?= $values['dealer']['withdraw_01']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[提现金额、打款方式、打款原因]</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 提现失败通知 </label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <input type="text" class="tpl-form-input"
                                           name="submsg[dealer][withdraw_02][template_id]"
                                           value="<?= $values['dealer']['withdraw_02']['template_id'] ?>">
                                    <div class="help-block">
                                        <small>关键词：[提现金额、申请时间、原因]</small>
                                    </div>
                                </div>
                            </div>
                            <div id="shuttle" class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit"
                                            class="j-submit am-btn am-btn-sm am-btn-secondary am-margin-right-sm">保存
                                    </button>
                                    <button type="button" class="j-shuttle am-btn am-btn-sm am-btn-success">一键添加
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

        // 一键配置
        $('.j-shuttle').on('click', function () {
            var url = "<?= url('wxapp.submsg/shuttle') ?>";
            layer.confirm('该操作将自动为您的小程序添加订阅消息<br>请先确保 "订阅消息" - "我的模板" 中没有记录<br>确定添加吗？', {
                title: '友情提示'
            }, function (index) {
                var load = layer.load();
                $.post(url, {}, function (result) {
                    result.code === 1 ? $.show_success(result.msg, result.url)
                        : $.show_error(result.msg);
                    layer.close(load);
                });
                layer.close(index);
            });
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
