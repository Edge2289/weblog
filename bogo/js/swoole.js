
const opend = localStorage.getItem('user_qq');
const token = localStorage.getItem('access_token');
// 初始化 服务类
// var wbSock = new WebSocket("ws://192.168.118.129:7326?opend="+opend);
const wbSock = new WebSocket("ws://192.168.0.15:7326?opend="+opend);
wbSock.onclose = function(wvent){
	console.log('close');
}
window.onbeforeunload = function () {
       wbSock.close();
}
wbSock.onerror = function(event){
	console.log("服务器连接异常");
}

// 请求组


