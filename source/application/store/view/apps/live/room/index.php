<?php

use app\common\enum\live\LiveStatus as LiveStatusEnum;

?>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <div class="widget-head am-cf">
                    <div class="widget-title am-cf"> 小程序直播间列表</div>
                </div>
                <div class="widget-body am-fr">
                    <div class="tips am-margin-bottom am-u-sm-12">
                        <div class="pre">
                            <p class="am-padding-bottom-xs"> 小程序直播操作说明：</p>
                            <p>1. 登录 <a href="https://mp.weixin.qq.com/" target="_blank">微信小程序运营平台</a>，点击左侧菜单栏 “直播”，点击
                                “创建直播间” 按钮。</p>
                            <p>2. 点击本页面中的 "同步直播间" 按钮，将直播间列表导入商城系统中。</p>
                        </div>
                    </div>
                    <!-- 工具栏 -->
                    <div class="page_toolbar am-margin-bottom am-cf">
                        <form class="toolbar-form" action="">
                            <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
                            <div class="am-u-sm-12 am-u-md-3">
                                <div class="am-form-group">
                                    <div class="am-btn-toolbar">
                                        <?php if (checkPrivilege('apps.live.room/refresh')): ?>
                                            <div class="am-btn-group am-btn-group-xs">
                                                <a class="j-refresh am-btn am-btn-default am-btn-success am-radius"
                                                   href="javascript:void(0);">
                                                    <span class="am-icon-refresh"></span> 同步直播间
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-9">
                                <div class="am fr">
                                    <div class="am-form-group am-fl">
                                        <div class="am-input-group am-input-group-sm tpl-form-border-form">
                                            <input type="text" class="am-form-field" name="search"
                                                   placeholder="请输入直播间名称/主播昵称"
                                                   value="<?= $request->get('search') ?>">
                                            <div class="am-input-group-btn">
                                                <button class="am-btn am-btn-default am-icon-search"
                                                        type="submit"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="am-scrollable-horizontal am-u-sm-12">
                        <table width="100%" class="am-table am-table-compact am-table-striped
                         tpl-table-black am-text-nowrap">
                            <thead>
                            <tr>
                                <th>直播间ID</th>
                                <th>直播间名称</th>
                                <th>主播昵称</th>
                                <th>直播时间</th>
                                <th>直播状态</th>
                                <th>置顶</th>
                                <th>更新时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
                                <tr>
                                    <td class="am-text-middle"><?= $item['room_id'] ?></td>
                                    <td class="am-text-middle"><?= $item['room_name'] ?></td>
                                    <td class="am-text-middle"><?= $item['anchor_name'] ?></td>
                                    <td class="am-text-middle">
                                        <p class="">
                                            <span class="title">开始：</span>
                                            <span class="value"><?= $item['start_time'] ?></span>
                                        </p>
                                        <p class="">
                                            <span class="title">结束：</span>
                                            <span class="value"><?= $item['end_time'] ?></span>
                                        </p>
                                    </td>
                                    <td class="am-text-middle">
                                        <?= LiveStatusEnum::data()[$item['live_status']]['name'] ?>
                                    </td>
                                    <td class="am-text-middle">
                                        <span class="j-setTop x-cur-p am-badge am-badge-<?= $item['is_top'] ? 'success' : 'warning' ?>"
                                              data-id="<?= $item['id'] ?>" data-istop="<?= $item['is_top'] ?>">
                                               <?= $item['is_top'] ? '是' : '否' ?></span>
                                    </td>
                                    <td class="am-text-middle"><?= $item['update_time'] ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="am-text-center">暂无记录</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="am-u-lg-12 am-cf">
                        <div class="am-fr"><?= $list->render() ?> </div>
                        <div class="am-fr pagination-total am-margin-right">
                            <div class="am-vertical-align-middle">总记录：<?= $list->total() ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        /**
         * 同步直播间
         */
        $('.j-refresh').on('click', function () {
            var url = "<?= url('apps.live.room/refresh') ?>";
            var load = layer.load();
            $.post(url, {}, function (result) {
                result.code === 1 ? $.show_success(result.msg, result.url)
                    : $.show_error(result.msg);
                layer.close(load);
            });
        });

        // 商品状态
        $('.j-setTop').click(function () {
            var data = $(this).data();
            layer.confirm('确定要' + (parseInt(data['istop']) === 1 ? '取消' : '') + '置顶该房间吗？'
                , {title: '友情提示'}
                , function (index) {
                    var url = "<?= url('apps.live.room/settop') ?>";
                    $.post(url
                        , {id: data['id'], is_top: Number(!(parseInt(data['istop']) === 1))}
                        , function (result) {
                            result['code'] === 1 ? $.show_success(result['msg'], result['url'])
                                : $.show_error(result['msg']);
                        });
                    layer.close(index);
                });
        });

    });
</script>

