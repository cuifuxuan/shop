<?php

namespace app\store\model\dealer;

use app\store\model\User as UserModel;
use app\store\model\Wxapp as WxappModel;
use app\common\model\dealer\User as dealerUserModel;
use app\common\model\dealer\Withdraw as WithdrawModel;
use app\common\service\Order as OrderService;
use app\common\service\Message as MessageService;
use app\common\enum\dealer\withdraw\PayType as PayTypeEnum;
use app\common\enum\dealer\withdraw\ApplyStatus as ApplyStatusEnum;
use app\common\library\wechat\WxPay;

/**
 * 分销商提现明细模型
 * Class Withdraw
 * @package app\store\model\dealer
 */
class Withdraw extends WithdrawModel
{
    /**
     * 获取器：申请时间
     * @param $value
     * @return false|string
     */
    public function getAuditTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i:s', $value) : 0;
    }

    /**
     * 获取器：打款方式
     * @param $value
     * @return mixed
     */
    public function getPayTypeAttr($value)
    {
        return ['text' => PayTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 获取分销商提现列表
     * @param null $userId
     * @param int $apply_status
     * @param int $pay_type
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($userId = null, $apply_status = -1, $pay_type = -1, $search = '')
    {
        // 构建查询规则
        $this->alias('withdraw')
            ->with(['user'])
            ->field('withdraw.*, dealer.real_name, dealer.mobile, user.nickName, user.avatarUrl')
            ->join('user', 'user.user_id = withdraw.user_id')
            ->join('dealer_user dealer', 'dealer.user_id = withdraw.user_id')
            ->order(['withdraw.create_time' => 'desc']);
        // 查询条件
        $userId > 0 && $this->where('withdraw.user_id', '=', $userId);
        !empty($search) && $this->where('dealer.real_name|dealer.mobile', 'like', "%$search%");
        $apply_status > 0 && $this->where('withdraw.apply_status', '=', $apply_status);
        $pay_type > 0 && $this->where('withdraw.pay_type', '=', $pay_type);
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    /**
     * 分销商提现审核
     * @param $data
     * @return bool
     */
    public function submit($data)
    {
        if (
            $data['apply_status'] == ApplyStatusEnum::AUDIT_REJECT
            && empty($data['reject_reason'])
        ) {
            $this->error = '请填写驳回原因';
            return false;
        }
        $this->transaction(function () use ($data) {
            // 更新申请记录
            $data['audit_time'] = time();
            $this->allowField(true)->save($data);
            // 提现驳回：解冻分销商资金
            if ($data['apply_status'] == ApplyStatusEnum::AUDIT_REJECT) {
                User::backFreezeMoney($this['user_id'], $this['money']);
            }
            // 发送消息通知
            MessageService::send('dealer.withdraw', [
                'withdraw' => $this,
                'user' => UserModel::detail($this['user_id']),
            ]);
        });
        return true;
    }

    /**
     * 确认已打款
     * @param bool $verifyUserFreezeMoney 验证已冻结佣金是否合法
     * @return bool|mixed
     * @throws \think\exception\DbException
     */
    public function money($verifyUserFreezeMoney = true)
    {
        // 验证已冻结佣金是否合法
        if ($verifyUserFreezeMoney && !$this->verifyUserFreezeMoney($this['user_id'], $this['money'])) {
            return false;
        }
        return $this->transaction(function () {
            // 更新申请状态
            $this->allowField(true)->save([
                'apply_status' => 40,
                'audit_time' => time(),
            ]);
            // 更新分销商累积提现佣金
            User::totalMoney($this['user_id'], $this['money']);
            // 记录分销商资金明细
            Capital::add([
                'user_id' => $this['user_id'],
                'flow_type' => 20,
                'money' => -$this['money'],
                'describe' => '申请提现',
            ]);
            return true;
        });
    }

    /**
     * 分销商提现：微信支付企业付款
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function wechatPay()
    {
        // 验证已冻结佣金是否合法
        if (!$this->verifyUserFreezeMoney($this['user_id'], $this['money'])) {
            return false;
        }
        // 微信用户信息
        $user = $this['user']['user'];
        // 生成付款订单号
        $orderNO = OrderService::createOrderNo();
        // 付款描述
        $desc = '分销商提现付款';
        // 微信支付api：企业付款到零钱
        $wxConfig = WxappModel::getWxappCache();
        $WxPay = new WxPay($wxConfig);
        // 请求付款api
        if ($WxPay->transfers($orderNO, $user['open_id'], $this['money'], $desc)) {
            // 确认已打款
            $this->money(false);
            return true;
        }
        return false;
    }

    /**
     * 验证已冻结佣金是否合法
     * @param $userId
     * @param $money
     * @return bool
     * @throws \think\exception\DbException
     */
    public function verifyUserFreezeMoney($userId, $money)
    {
        $dealerUserInfo = dealerUserModel::detail($userId);
        if ($dealerUserInfo['freeze_money'] < $money) {
            $this->error = '数据错误：已冻结的佣金不能小于提现的金额';
            return false;
        }
        return true;
    }

}
