
      function sign(obj) {
        let str = addStr(objKeySort(obj));

        str = encodeURIComponent(str);
        str = str.replace(/\'/g, "%27");
        str = str.replace(/\*/g, "%2A");
        str = str.replace(/@/g, "%40");
        str = str.replace(/\(/g, "%28");
        str = str.replace(/\)/g, "%29");
        str = APP_SECRET + str.toUpperCase() + APP_SECRET;
        str = hex_md5(str);
        return str;
      }

function objKeySort(obj) { //排序的函数
  var newkey = Object.keys(obj).sort();　
  //先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
  var newObj = {}; //创建一个新的对象，用于存放排好序的键值对
  for (var i = 0; i < newkey.length; i++) { //遍历newkey数组
    newObj[newkey[i]] = obj[newkey[i]]; //向新创建的对象中按照排好的顺序依次增加键值对
  }
  return newObj; //返回排好序的新对象
}

function addStr(obj) { //对象拼接成字符串
  let str = '';
  Object.keys(obj).forEach(function (key) {
    let item = '';
    if (typeof (obj[key]) == "string") {
      item = Trim(obj[key])
    } else {
      if(obj[key] != null){
        item = obj[key]
      }
    }
    str += key + item

  });
  // console.log("str",str);
  return str;

}

function Trim(str)  {   //去首尾空格
  return str.replace(/(^\s*)|(\s*$)/g, "");
}

function getCurrentTime() { //获取timestamp
  var keep = '';
  var date = new Date();
  var y = date.getFullYear();
  var m = date.getMonth() + 1;
  m = m < 10 ? '0' + m : m;
  var d = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
  var h = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
  var f = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
  var s = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
  var rand = Math.round(Math.random() * 899 + 100);
  keep = y + '' + m + '' + d + '' + h + '' + f + '' + s;
  return keep; //20160614134947
}
