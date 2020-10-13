<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="renderer" content="webkit"/>
    <link rel="stylesheet" href="assets/common/css/amazeui.min.css"/>
    <link rel="stylesheet" href="assets/store/css/app.css?v=<?= $version ?>"/>
    <script src="assets/common/js/jquery.min.js"></script>
    <title>门店列表</title>
</head>
<body class="select-data">
<!-- 工具栏 -->
<div class="page_toolbar am-u-sm-12 am-margin-bottom-xs am-cf">
    <form class="toolbar-form" action="">
        <input type="hidden" name="s" value="/<?= $request->pathinfo() ?>">
        <div class="am-u-sm-12 am-u-md-3">
            <div class="am-form-group">
                <?php if (checkPrivilege('shop/add')): ?>
                    <div class="am-btn-group am-btn-group-xs">
                        <a class="am-btn am-btn-default am-btn-success"
                           href="<?= url('shop/add') ?>">
                            <span class="am-icon-plus"></span> 新增
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="am-u-sm-12 am-u-md-9">
            <div class="am fr">
                <div class="am-form-group am-fl">
                    <?php $isCheck = $request->get('is_check'); ?>
                    <select name="is_check"
                            data-am-selected="{btnSize: 'sm', placeholder: '自提核销'}">
                        <option value=""></option>
                        <option value="-1" <?= $isCheck === '-1' ? 'selected' : '' ?>>全部</option>
                        <option value="1" <?= $isCheck === '1' ? 'selected' : '' ?>>支持</option>
                        <option value="0"<?= $isCheck === '0' ? 'selected' : '' ?>>不支持</option>
                    </select>
                </div>
                <div class="am-form-group am-fl">
                    <div class="am-input-group am-input-group-sm tpl-form-border-form">
                        <input type="text" class="am-form-field" name="search"
                               placeholder="请输入门店名称/联系人/电话"
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
    <table width="100%" class="am-table am-table-compact am-table-striped tpl-table-black am-text-nowrap">
        <thead>
        <tr>
            <th>
                <label class="am-checkbox">
                    <input data-am-ucheck data-check="all" type="checkbox">
                </label>
            </th>
            <th>门店ID</th>
            <th>门店logo</th>
            <th>门店名称</th>
            <th>自提核销</th>
            <th>门店状态</th>
            <th>创建时间</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$list->isEmpty()): foreach ($list as $item): ?>
            <tr>
                <td class="am-text-middle">
                    <label class="am-checkbox">
                        <input data-am-ucheck data-check="item" data-params='<?= json_encode([
                            'shop_id' => (string)$item['shop_id'],
                            'shop_name' => $item['shop_name'],
                            'logo_image' => $item['logo']['file_path'],
                            'phone' => $item['phone'],
                            'region' => $item['region'],
                            'address' => $item['address'],
                        ], JSON_UNESCAPED_SLASHES) ?>' type="checkbox">
                    </label>
                </td>
                <td class="am-text-middle"><?= $item['shop_id'] ?></td>
                <td class="am-text-middle">
                    <a href="<?= $item['logo']['file_path'] ?>" title="点击查看大图" target="_blank">
                        <img src="<?= $item['logo']['file_path'] ?>" width="72" height="72" alt="">
                    </a>
                </td>
                <td class="am-text-middle"><?= $item['shop_name'] ?></td>
                <td class="am-text-middle">
                    <span class="am-badge am-badge-<?= $item['is_check'] ? 'success' : 'warning' ?>">
                       <?= $item['is_check'] ? '支持' : '不支持' ?>
                   </span>
                </td>
                <td class="am-text-middle">
                    <span class="am-badge am-badge-<?= $item['status'] ? 'success' : 'warning' ?>">
                       <?= $item['status'] ? '启用' : '禁用' ?>
                   </span>
                </td>
                <td class="am-text-middle"><?= $item['create_time'] ?></td>
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

<script src="assets/common/js/amazeui.min.js"></script>
<script>

    /**
     * 获取已选择的数据
     * @returns {Array}
     */
    function getSelectedData() {
        var data = [];
        $('input[data-check=item]:checked').each(function () {
            data.push($(this).data('params'));
        });
        return data;
    }

    $(function () {

        // 全选框元素
        var $checkAll = $('input[data-check=all]')
            , $checkItem = $('input[data-check=item]')
            , itemCount = $checkItem.length;

        // 复选框: 全选和反选
        $checkAll.change(function () {
            $checkItem.prop('checked', this.checked);
        });

        // 复选框: 子元素
        $checkItem.change(function () {
            if (!this.checked) {
                $checkAll.prop('checked', false);
            } else {
                var checkedItemNum = $checkItem.filter(':checked').length;
                checkedItemNum === itemCount && $checkAll.prop('checked', true);
            }
        });

    });
</script>
</body>
</html>
