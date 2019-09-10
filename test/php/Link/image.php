<?php 

include_once("linkSql.php");
class image extends linkSql{
    //连接数据库
    public function __construct(){
        parent::__construct();
    }

    //数据库接收图片
    public function uploadImage($src, $userId, $categories){
        $date = date("Y-m-d H:i:s");
        $result = $this->query('insert into `image` (`src`, `user-id`, `time`) values ("'.$src.'","'.$userId.'","'.$date.'")');
        if(!$result){
            return 1;
        }
        $result = $this->query('select pid from `image` order by pid desc limit 1');
        if(!$result){
            return 1;
        }
        $message = $result->fetch_assoc();
        $pid = $message['pid'];
        $result = $this->query('insert into `'.$categories.'` (`pid`) values ('.$pid.')');
        return 0;
    }

    //数据库返回最新图片(限30张)
    public function getNewImage(){
        //获取30个图片
        $result = $this->query('select * from `image` order by pid desc limit 30');
        if(!$result){
            return 1;
        }
        return $result;
    }

    //数据库返回用户点击的种类图片
    public function getCategoriesImage($categories){
        //表小可以不用内连接
        $result = $this->query('select * from `image` where pid in (
            select pid from `'.$categories.'` order by id desc
        ) order by pid desc');
        if(!$result){
            return 1;
        }
        return $result;
    }

    //数据库最多随机返回50条图片数据
    public function getRandomImage($num){
        if($num > 50){
            $num = 50;
        }
        $result = $this->query('select * from `image` where pid >= (select floor(rand() * (select MAX(pid) from `image`))) order by pid limit '.$num.'');
        if(!$result){
            return 1;
        }
        return $result;
    }

    //返回被收藏数最多的50张图片
    public function getPopluarImage($num){
        if($num > 50){
            $num = 50;
        }
        $result = $this->query('select * from `image` order by `collectionCount` desc limit '.$num.'');
        if(!$result){
            return 1;
        }
        return $result;
    }
    
    //得到全部数据的长度
    public function getTotalCount(){
        $result = $this->query('select count(*) as total_count from `image`');
        if(!$result){
            return 1;
        }
        $row = $result->fetch_assoc();
        return $row['total_count'];
    }

    //返回用户对图片收藏状态
    public function getCollectStatus($uid, $pid){
        $result = $this->query('select * from `collection` where uid = "'.$uid.'" and pid = "'.$pid.'"');
        if(!$result){
            return 1;
        }
        else if($result->num_rows === 0){
            //没进行过收藏
            return 2;
        }
        //已经收藏
        return 0;
    }

    //返回用户id和点击收藏图片id
    public function getPidUid($src, $username){
        $result = $this->query('select pid, id from `image` i, `user` u where i.src = "'.$src.'" and u.username = "'.$username.'"');
        if(!$result){
            return 1;
        }
        //查找成功
        return $result;
    }

    //插入用户收藏某张图片记录
    public function insertCollectImage($uid, $pid){
        $result = $this->query('insert into `collection` (pid, uid) values ("'.$pid.'", "'.$uid.'")');
        if(!$result){
            return 1;
        }
        $result = $this->updateImageCollectionCount($pid, true);
        if($result === 1){
            return 2;
        }
        //插入成功
        return 0;
    }

    //删除用户收藏某张图片记录
    public function deleteCollectImage($uid, $pid){
        $result = $this->query('delete from `collection` where uid = "'.$uid.'" and pid = "'.$pid.'"');
        if(!$result){
            return 1;
        }
        $result = $this->updateImageCollectionCount($pid, false);
        if($result === 1){
            return 2;
        }
        //插入成功
        return 0;
    }

    //用户收藏/取消收藏行为对图片被收藏数进行更新
    /**
     * $pid: 图片id
     * $isAdd: 判断图片被收藏数减1还是加1
     */
    public function updateImageCollectionCount($pid, $isAdd){
        if($isAdd){
            $result = $this->query('update `image` set collectionCount = collectionCount + 1 where pid = "'.$pid.'"');
        }
        else{
            $result = $this->query('update `image` set collectionCount = collectionCount - 1 where pid = "'.$pid.'"');
        }
        if(!$result){
            return 1;
        }
        //更新图片被收藏数成功
        return 0;
    }

    //返回用户50张收藏的图片
    public function getAllCollectImage($username){
        $result = $this->query('select * from `image` i inner join `collection` c on i.pid = c.pid and c.uid in (select id from `user` where `username` = "'.$username.'") order by i.pid desc limit 50');
        if(!$result){
            return 1;
        }
        else if($result->num_rows === 0){
            //该用户没收藏过图片
            return 2;
        }
        //获取收藏图片成功
        return $result;
    }
}