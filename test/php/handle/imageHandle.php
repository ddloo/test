<?php

//接收图片
function uploadImg(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }
    if(!isset($_POST['name']) || !isset($_POST['userId']) || !isset($_POST['categories'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }
    // image/gif,image/jpeg,image/jpg,image/png
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["img"]["name"]);
    $extension = end($temp);
    //上传图片
    // die($_POST['name']);
    if(in_array($extension, $allowedExts)){
        //图片格式符合
            //用户个人图片集文件路径
            $path = mb_convert_encoding("../uploadPic/".$_POST['name'], "UTF-8");
            //创建用户个人图片集文件
            if(!file_exists($path)){
                mkdir($path, 0777, true);
            }
            if (file_exists($path."/".$_FILES["img"]["name"]))
            {
                $response = [
                    'code' => '0001',
                    'msg' => '图片已上传过一次啦~（＞人＜）'
                ];
                die(json_encode($response));
            }
            else
            {
                move_uploaded_file($_FILES["img"]["tmp_name"], $path."/".$_FILES["img"]["name"]);
                $src = $path."/".$_FILES["img"]["name"];
            }
       }
    else{
        //图片格式不符合
        $response = [
            'code' => '0002',
            'msg' => '图片格式不对哦,只能上传后缀名为 .gif, .jpeg, .jpg, .png'
        ];
        die(json_encode($response));
    }
    include_once(app::$base."/Link/image.php");

    $image = new image();
    
    $result = $image->uploadImage($src, $_POST['userId'], $_POST['categories']);
    if($result === 1){
        //插入失败
        $response = [
            'code' => '0003',
            'msg' => '上传图片失败了,请再试一次╯︿╰'
        ];
        die(json_encode($response));
    }
    else if($result === 0){
        $response = [
            'code' => '0000',
            'msg' => '上传成功'
        ];
        die(json_encode($response));
    }
}

//加载最新图片
function getNewImg(){
    $response = [];
    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getNewImage();
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0003',
            'msg' => '获取最新图片失败╯︿╰'
        ];
        die(json_encode($response));
    }
    //查找成功
    $imageList = [];
    while($image = $result->fetch_assoc()){
        $src = $image['src'];
        $userId = $image['user-id'];
        $time = $image['time'];
        array_push($imageList, [
            "src" => $src,
            "userId" => $userId,
            "time" => $time
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取最新图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//加载各种类的图片
function getCategoriesImg(){
    $response = [];
    if(!isset($_POST['categories'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }
    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getCategoriesImage($_POST['categories']);
    // die($result);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0003',
            'msg' => '获取最新图片失败╯︿╰'
        ];
        die(json_encode($response));
    }
    //查找成功
    $imageList = [];
    while($image = $result->fetch_assoc()){
        $src = $image['src'];
        $userId = $image['user-id'];
        $time = $image['time'];
        array_push($imageList, [
            "src" => $src,
            "userId" => $userId,
            "time" => $time
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取最新图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//加载随机图片
function getRandomImg(){
    $response = [];

    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getTotalCount();
    if($result === 1){
        //getTotalCount查找失败
        $response = [
            'code' => '0001',
            'msg' => '得到图片总数失败╯︿╰'
        ];
        die(json_encode($response));
    }
    else if($result === 0){
        //没有图片存在
        $response = [
            'code' => '0002',
            'msg' => '没有任何图片╯︿╰'
        ];
        die(json_encode($response));
    }
    //得到随机图片操作
    $result = $image->getRandomImage($result);
    if($result === 1){
        //getRandomImage查找失败
        $response = [
            'code' => '0003',
            'msg' => '随机图片查找失败╯︿╰'
        ];
        die(json_encode($response));
    }
    //查找成功
    $imageList = [];
    while($image = $result->fetch_assoc()){
        $src = $image['src'];
        $userId = $image['user-id'];
        $time = $image['time'];
        array_push($imageList, [
            "src" => $src,
            "userId" => $userId,
            "time" => $time
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取随机图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//加载热门图片
function getPopularImg(){
    $response = [];

    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getTotalCount();
    if($result === 1){
        //getTotalCount查找失败
        $response = [
            'code' => '0001',
            'msg' => '得到图片总数失败╯︿╰'
        ];
        die(json_encode($response));
    }
    else if($result === 0){
        //没有图片存在
        $response = [
            'code' => '0002',
            'msg' => '没有任何图片╯︿╰'
        ];
        die(json_encode($response));
    }
    //得到热门图片操作
    $result = $image->getPopluarImage($result);
    if($result === 1){
        //getPopluarImage查找失败
        $response = [
            'code' => '0003',
            'msg' => '热门图片查找失败╯︿╰'
        ];
        die(json_encode($response));
    }
    $imageList = [];
    while($image = $result->fetch_assoc()){
        $src = $image['src'];
        $userId = $image['user-id'];
        $time = $image['time'];
        array_push($imageList, [
            "src" => $src,
            "userId" => $userId,
            "time" => $time
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取热门图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//获取用户个人上传的图片集
function getUserUploadImg(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }

    $path = mb_convert_encoding("../uploadPic/".$_COOKIE['username']."/", "UTF-8");
    $filesName = scandir($path, 0);
    //scandir还会额外读取出".."与"."这两个字符
    if(sizeof($filesName) === 0){
        //该用户没有上传过图片
        $response = [
            "code" => "0001",
            "msg" => "你还没有上传过图片哦"
        ];
        die(json_encode($response));
    }
    // die($path.$filesName[1]);
    //返回用户全部图片
    $imageList = [];
    for($i = 2; $i < sizeof($filesName); $i++){
        $src = $path.$filesName[$i];
        array_push($imageList, [
            "src" => $src
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取用户上传的图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//获取用户收藏的图片集
function getUserCollectImg(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }
    if(!isset($_POST['username'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }

    include_once(app::$base."/Link/image.php");

    $image = new image();
    $result = $image->getAllCollectImage($_POST['username']);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0001',
            'msg' => '获取图片失败(っ °Д °;)っ'
        ];
        die(json_encode($response));
    }
    else if($result === 2){
        //该用户没收藏过图片
        $response = [
            'code' => '0002',
            'msg' => '你还没收藏过图片'
        ];
        die(json_encode($response));
    }
    $imageList = [];
    while($image = $result->fetch_assoc()){
        $src = $image['src'];
        $userId = $image['user-id'];
        $time = $image['time'];
        array_push($imageList, [
            "src" => $src,
            "userId" => $userId,
            "time" => $time
        ]);
    }
    $response = [
        "code" => "0000",
        "msg" => "成功获取热门图片",
        "data" => $imageList
    ];
    die(json_encode($response));
}

//获取该图片是否已经收藏的状态
function isCollect(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }

    if(!isset($_POST['src'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }

    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getPidUid($_POST['src'], $_COOKIE['username']);
    // die($result->fetch_assoc()['uid']);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0001',
            'msg' => '获取pid,uid失败╯︿╰'
        ];
        die(json_encode($response));
    }
    $message = $result->fetch_assoc();
    $uid = $message['id'];
    $pid = $message['pid'];
    $result = $image->getCollectStatus($uid, $pid);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0001',
            'msg' => '获取失败╯︿╰'
        ];
        die(json_encode($response));
    }
    else if($result === 2){
        //该图片还未收藏
        $response = [
            'code' => '0002',
            'msg' => '还没收藏过'
        ];
        die(json_encode($response));
    }
    else{
        $response = [
            'code' => '0000',
            'msg' => '已收藏'
        ];
        die(json_encode($response));
    }
}

//用户收藏图片
function userCollectImg(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }

    if(!isset($_POST['src'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }
    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getPidUid($_POST['src'], $_COOKIE['username']);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0001',
            'msg' => '获取pid,uid失败╯︿╰'
        ];
        die(json_encode($response));
    }
    $message = $result->fetch_assoc();
    $uid = $message['id'];
    $pid = $message['pid'];
    //检查是否已经收藏了
    $result = $image->getCollectStatus($uid, $pid);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0003',
            'msg' => '获取失败╯︿╰'
        ];
        die(json_encode($response));
    }
    if($result === 0){
        //已经收藏了
        $response = [
            'code' => '0004',
            'msg' => '已经收藏过啦(●ˇ∀ˇ●)'
        ];
        die(json_encode($response));
    }
    //插入收藏记录
    $result = $image->insertCollectImage($uid, $pid);
    if($result === 1){
        //收藏记录插入失败
        $response = [
            "code" => "0002",
            "msg" => "收藏失败(；′⌒`)"
        ];
        die(json_encode($response));
    }
    else if($result === 2){
        /**
         * 如果插入成功但是收藏数没加一,则手动调用updateImageCollectionCount函数加一
         * todo......
         */
        //更新图片被收藏数失败
        $response = [
            "code" => "0005",
            "msg" => "取消收藏失败(；′⌒`)"
        ];
        die(json_encode($response));
    }
    $response = [
        "code" => "0000",
        "msg" => "收藏成功啦( •̀ ω •́ )✧"
    ];
    die(json_encode($response));
}

//用户取消收藏图片
function userRemoveCollectImg(){
    $response = [];
    include_once(app::$base."/common/isAuth.php");
    //是否已经登录
    if(!isAuth()){
        $response = [
            "code" => "2000",
            "msg" => "您还没登录呢"
        ];
        die(json_encode($response));
    }

    if(!isset($_POST['src'])){
        $response = [
            'code' => '1000',
            'msg' => '发生了某些不可告人的错误呢┭┮﹏┭┮'
        ];
        die(json_encode($response));
    }
    include_once(app::$base."/Link/image.php");

    $image = new image();

    $result = $image->getPidUid($_POST['src'], $_COOKIE['username']);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0001',
            'msg' => '获取pid,uid失败╯︿╰'
        ];
        die(json_encode($response));
    }
    $message = $result->fetch_assoc();
    $uid = $message['id'];
    $pid = $message['pid'];
    //检查是否已经收藏了
    $result = $image->getCollectStatus($uid, $pid);
    if($result === 1){
        //查找失败
        $response = [
            'code' => '0003',
            'msg' => '获取失败╯︿╰'
        ];
        die(json_encode($response));
    }
    if($result === 2){
        //用户还没收藏过这张图片
        $response = [
            'code' => '0004',
            'msg' => '你还没收藏过啦(ノへ￣、)'
        ];
        die(json_encode($response));
    }
    //删除收藏记录
    $result = $image->deleteCollectImage($uid, $pid);
    if($result === 1){
        //收藏记录删除失败
        $response = [
            "code" => "0002",
            "msg" => "取消收藏失败(；′⌒`)"
        ];
        die(json_encode($response));
    }
    else if($result === 2){
        //更新图片被收藏数失败
        $response = [
            "code" => "0005",
            "msg" => "取消收藏失败(；′⌒`)"
        ];
        die(json_encode($response));
    }
    $response = [
        "code" => "0000",
        "msg" => "取消收藏啦(●'◡'●)"
    ];
    die(json_encode($response));
}