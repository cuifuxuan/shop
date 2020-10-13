<?php
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;

class GetBsyWeight extends Command
{
    protected $url = 'https://www.bsycrc.com/bishengyuan/api/data/user/weight/list';
    protected $token = '998c42e23c9c7935a0ce6da8e900b274';

    protected function configure(){
        $this->setName('GetBsyWeight')->setDescription("计划任务 GetBsyWeight");
    }

    //调用GetBsyWeight 这个类时,会自动运行execute方法
    protected function execute(Input $input, Output $output){
        $output->writeln('Date Crontab job start...');
        /*** 这里写计划任务列表集 START ***/
        for ($i=1;$i<100000;$i++){
            $this->getweightinfo($i);//获取体重信息
        }


        /*** 这里写计划任务列表集 END ***/
        $output->writeln('Date Crontab job end...');
    }

    //获取体重信息
    public function getweightinfo($no)
    {
        $data = [
            //'endTime'=>strtotime('2020-10-01')*1000,
            'pageNo'=>$no,
            'pageSize'=>20,
            'startTime'=>strtotime('2020-01-01')*1000,
            'token'=>$this -> token,
        ];
        $res = curl($this->url, $data);
        if($res){
            $info = json_decode($res, true);
            foreach($info['data'] as  $k => $v){
                $user_id = '';
                $bsy_user_id = '';
                foreach($v as  $k_v => $v_v) {
                    if ($k_v == 'user') {
                        $bsy_user_id = $this->getbsyuserinfo($v_v);
                        $user_id = $this->getuserinfo($v_v);
                        var_dump($user_id);
                    } else {
                        foreach ($v_v as $key => $val) {
                            if($user_id){
                                $val['user_id'] = $user_id;
                            }else{
                                $val['user_id'] = '';
                            }
                            if($bsy_user_id){
                                $val['bsy_user_id'] = $bsy_user_id;
                            }else{
                                $val['bsy_user_id'] = '';
                            }
                            $this->insweightinfo($val);
                        }
                    }
                }
            }

        }

        echo '这里写你要实现的逻辑代码1111111111111111111';
    }


    //会员信息查询及添加
    public function getbsyuserinfo($user)
    {
        if(!empty($user['account'])){
            $ins_data = [
                'img' => $user['imageUrl'],
                'mobile' => $user['account'],
                'sex' => $user['sex'],
                'name' => $user['name'],
                'height' => $user['height'],
                'age' => $user['age'],
                'create_time' => $user['createTime'],
                'add_time' => date('Y-m-d H:i:s'),
            ];
            $user = DB::name('bsy_user')->where(['mobile' => $user['account']])->find();
            if($user){
                return $user['id'];
            }else{
                $res = DB::name('bsy_user')->insertGetId($ins_data);
                return $res;
            }
        }
    }


    //会员信息查询及添加
    public function getuserinfo($user)
    {
        return false;
        /*if(!empty($user['account'])){
            $user = DB::name('user')->where(['mobile' => $user['account']])->find();
            if($user){
                return $user['id'];
            }else{
                return false;
            }
        }*/
    }

    //会员信息查询及添加
    public function insweightinfo($data)
    {
        $ins_data = [
            'user_id' => $data['user_id'],
            'bsy_user_id' => $data['bsy_user_id'],
            'age' => $data['age'],
            'sex' => $data['sex'],
            'height' => $data['height'],
            'createDate' => $data['createDate'],
            'weightKg' => $data['weightKg'],
            'fat' => $data['fat'],
            'fatKg' => $data['fatKg'],
            'muscle' => $data['muscle'],
            'visceralFat' => $data['visceralFat'],
            'metabolize' => $data['metabolize'],
            'waterContent' => $data['waterContent'],
            'bones' => $data['bones'],
            'bmi' => $data['bmi'],
            'protein' => $data['protein'],
            'noFatWeight' => $data['noFatWeight'],
            'obsLevel' => $data['obsLevel'],
            'subFat' => $data['subFat'],
            'bodyAge' => $data['bodyAge'],
            'bodyScore' => $data['bodyScore'],
            'bodyType' => $data['bodyType'],
            'standardWeight' => $data['standardWeight'],
            'impedance' => $data['impedance'],
            'add_time' => date('Y-m-d H:i:s'),
        ];
        var_dump($ins_data);
        $info = DB::name('bsy_weight')->where(['bsy_user_id' => $data['bsy_user_id'],'createDate' => $data['createDate']])->find();
        if($info){
            return $info['id'];
        }else{
            $res = DB::name('bsy_weight')->insertGetId($ins_data);
            return $res;
        }
    }

}