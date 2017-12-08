<?php
//第一种:会白屏
//include "../php/redis.class.php";
//$redis = redisDB::getInstance();
//include '../php/mysqldb.php';
//$link = mysqlDb::getIntance("app");
//$id = isset($_GET["id"])?$_GET["id"]:1;
//
//$key = "info_video_{$id}";
//
//
////判断缓存中是否存在key
//if ($redis->stringExists($key)) {
//    echo $key;
//    echo "redis读取";
//    $result = $redis->stringGet($key);
//    $result = json_decode($result,true);
//} else {
//    //查询记录
//    echo $key;
//    echo "数据库读取";
//    $sql = "select * from comment where id ={$id}";
//    $result = $link->queryAll($sql);
//    $redis->stringSet($key, json_encode($result), 30);
//    



//第二种  不会白屏
//1.时间设置为永久，给redis做判断，更新时候先显示redis缓存内容，在更新redis



include "../php/redis.class.php";
$redis = redisDB::getInstance();
include '../php/mysqldb.php';
$link = mysqlDb::getIntance("app");
$id = isset($_GET["id"])?$_GET["id"]:1;

$key = "info_video_{$id}";


//判断缓存中是否存在key
if ($redis->stringExists($key)) {
    echo $key;
    echo "redis读取";
    $data = $redis->stringGet($key);
    $data = json_decode($data,true);
    $result = $data["data"];
    
    var_dump($result);
    //和当前时间对比大于缓存有效期时间则更新redis 但是显示的还是redis内容
    if(time()-$data["date"]>$data["time"]){
        //更新
        $sql = "select * from comment where id ={$id}";
        $result = $link->queryAll($sql);
        $redisDate = array(
            "data"=>$result,  //实际数据
            "time"=>60,    //缓存有效期
            "date"=>time(),   //当前储存数据的时间
        );
        $redis->stringSet($key, json_encode($redisDate));
    }
} else {
    //查询记录
    echo $key;
    echo "数据库读取";
    $sql = "select * from comment where id ={$id}";
    $result = $link->queryAll($sql);
    $redisDate = array(
        "data"=>$result,  //实际数据
        "time"=>60,    //缓存有效期
        "date"=>time(),   //当前储存数据的时间
    );
    $redis->stringSet($key, json_encode($redisDate),3600*24*30);
    var_dump($result);
}







//var_dump($result);
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <title>详情</title>
        <script src="../js/mui.min.js"></script>
        <link href="../css/mui.min.css" rel="stylesheet" />
        <script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">
        <style type="text/css">
            .show {
                margin-left: 3%;
                font-size: 20px;

            }
            #redbox span{
                background:rgba(255,255,255,.4);
                display: inline-block;
                width: 100%;
                height: 30px;
                text-align: center;
                line-height: 30px;
                color: grey;

            }
            #redbox span:hover{
                background: black;
                color: white;
            }
        </style>
    </head>

    <body>
        <!--右侧图标-->
        <div style="display:none;width: 100px;height: 120px; border-radius:5px;position: absolute;right:0px;top:0px; z-index: 999;" id="redbox">
            <span class="fa fa-id-badge">拉黑</span>
            <span class="fa fa-bell-slash">屏蔽</span>
            <span class="fa fa-hand-paper-o" >举报</span>
            <span class="fa fa-user-o">联系我们</span>
        </div>

        <div id="content" >
            <div>
                <video width="100%" height="210" controls  poster="../images/timg.jpg">
                    <source src="../images/26.mp4" type="video/mp4 "> Your browser does not support the video tag.
                </video>
            </div>

            <!--介绍-->
            <div>
                <ul class="mui-table-view">
                    <li class="show">
                        <a class="">
                            <span class="fa fa-volume-down" style="margin-right: 10px;"></span>
                            <span style="font-size: 15px;">55555次播放</span>
                        </a>
                    </li>
                    <li class="mui-table-view-cell">
                        <a class="">
                            <span class="fa fa-user-o" style="margin-right: 10px;"></span>
                            <span style="color: burlywood;">胡小乐asdfsdafsd:</span>
                            <span style="color: gainsboro;">我是大好人</span>
                        </a>
                    </li>
                    <li class="mui-table-view-cell">
                        <a class="">
                            <span class="fa fa-heart-o">&nbsp;&nbsp;:</span>
                            <span style="margin-right: 30%;color: red;">99⁰C</span>
                            <span class="fa fa-comment-o" >&nbsp;&nbsp;:</span>
                            <span class="" style="color: darkslategray;">&nbsp;&nbsp;5555条</span>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="mui-content mui-scroll-wrapper" style="margin-top: 350px;" id="shuaxin">
                <div class="mui-scroll">
                    <ul class="mui-table-view">
                        <?php foreach ($result as $k => $v) { ?>
                            <li class="mui-table-view-cell mui-media ">
                                <a href="javascript:; ">
                                    <?php
                                    $sql = "select * from user where id='{$v["video_id"]}'";
                                    $list = $link->queryAll($sql);
                                    foreach ($list as $s => $z) {
                                        $list = $z["username"];
                                        $listImage = $z["image"];
                                    }
                                    ?>
                                    <img class="mui-media-object mui-pull-left " src="<?php echo $listImage ?>">
                                    <div class="mui-media-body">
                                        <?php echo $list ?>
                                        <span class="mui-icon mui-pull-right" style="font-size: 10px;padding: 4px; "><span class="fa fa-hourglass-o"><?php echo $v["add_time"] ?></span></span>
                                        <p class="mui-ellipsis"><?php echo $v["content"] ?> </p>

                                    </div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <footer class="mui-bar mui-bar-footer">
            <div class="input_text " id="click_before">
                <span class="mui-icon mui-icon-chatbubble " style="margin-right: 3%;width: 5%; "></span>
                <input type="text " style="width: 80%;height: 30px; " placeholder="请评论 " />
                <span class="fa fa-fighter-jet " aria-hidden="true " style="margin-left: 3%;width: 5%; "></span>
            </div>
            <div class="input_text " style="display: none;" id="click_after">
                <span class="mui-icon mui-icon-chatbubble " style="margin-right: 3%;width: 5%; "></span>
                <input type="text " style="width: 80%;height: 30px; border: 1px solid gray; " placeholder="请输入评论内容 " />
                <span class="fa fa-rocket " aria-hidden="true " style="margin-left: 3%;width: 5%; "></span>
            </div>

        </footer>
    </body>

</html>
<script type="text/javascript ">
    mui.init({

    //		pullRefresh: {
    //			container: "#shuaxin", //下拉刷新容器标识，querySelector能定位的css选择器均可，比如：id、.class等
    //			down: {
    //				height: 50, //可选,默认50.触发下拉刷新拖动距离,
    //				auto: true, //可选,默认false.首次加载自动下拉刷新一次
    //				contentdown: "下拉可以刷新", //可选，在下拉可刷新状态时，下拉刷新控件上显示的标题内容
    //				contentover: "释放立即刷新", //可选，在释放可刷新状态时，下拉刷新控件上显示的标题内容
    //				contentrefresh: "正在刷新...", //可选，正在刷新状态时，下拉刷新控件上显示的标题内容
    //				callback: function() //必选，刷新函数，根据具体业务来编写，比如通过ajax从服务器获取新数据；
    //				settimeout({
    //					function(){
    //						mui('#shuaxin').pullRefresh().endPulldown();
    //					},1000;
    //				})
    //			}
    //		}
    });
    mui(".mui-scroll-wrapper").scroll({
    deceleration: 0.0005
    });
    mui.plusReady(function() {
    //输入框
    document.getElementById("click_before").addEventListener("tap", function() {
    $("#click_before").css("display", "none");
    $("#click_after").css("display", "block");

    })
    document.getElementById("content").addEventListener("tap", function() {
    $("#click_before").css("display", "block");
    $("#click_after").css("display", "none");
    })

    })
</script>