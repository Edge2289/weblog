
const opend = localStorage.getItem('user_qq');
const token = localStorage.getItem('access_token');
// 初始化 服务类
const wbSock = new WebSocket("ws://192.168.118.129:7326?opend="+opend);
wbSock.onclose = function(wvent){
    $("#addHtml").html($("#addHtml").html()+"<br> 服务端未启动服务");
}
window.onbeforeunload = function () {
       wbSock.close();
}
wbSock.onerror = function(event){
    $("#addHtml").html($("#addHtml").html()+"<br> 服务器连接异常!!!");
}

// 请求组


