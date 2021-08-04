这个仓库是PocketMine Alpha_1.13.2 (Minecraft PE Alpha_0.8.1) 的改进版，增加了个人的优化以及汉化。

由于原仓库使用LGPL协议，所以修改后的代码必须公布，并使用LGPL许可证。

### Windows教程
提示：本教程应用的是电脑端。很多教程都使用手机端，但是我建议使用电脑端，因为电脑端可以不占手机内存，方便内网穿透。

提示：下文中（包括上文），内网穿透即为端口映射。

使用Sakura Frp进行内网穿透（不是广告），可以使用逍遥模拟器模拟MCPE在电脑上的运行（不是广告x2）

1. 安装git for windows，链接在此：https://github.com/git-for-windows/git/releases/download/v2.32.0.windows.2/Git-2.32.0.2-64-bit.exe

![](https://cdn.luogu.com.cn/upload/image_hosting/adw5xt9g.png)
![](https://cdn.luogu.com.cn/upload/image_hosting/fhq0ezns.png)
![](https://cdn.luogu.com.cn/upload/image_hosting/hkhf0v00.png)

2. 要建立文件夹，在你要建立的文件夹的上一个文件夹点击左上角文件->以管理员身份打开Powershell

![](http://tiebapic.baidu.com/forum/w%3D580/sign=7bb23b91683e6709be0045f70bc69fb8/712ff4fdfc0392450abc26b99094a4c27c1e2559.jpg)

运行命令
```git clone https://github.com/LiuTianyouOnLuogu/PocketMine-LiuTianyou-Version.git PocketMine-MP```

然后你就得到了PocketMine-MP文件夹。

3. 注意了，下面这一步特别重要（最好别先使用现成的PHP）：

下载PHP5.6：https://windows.php.net/downloads/releases/archives/php-5.6.9-Win32-VC11-x64.zip

解压出来之后重命名为php，在PocketMine文件夹里新建bin文件夹，移动过去。

下载pthreads扩展（注意，一定要确认是否线程安全，否则会“找不到指定的模块”）：https://windows.php.net/downloads/pecl/releases/pthreads/1.0.0/php_pthreads-1.0.0-5.3-ts-vc9-x86.zip

下载yaml扩展：https://windows.php.net/downloads/pecl/releases/yaml/1.3.2/php_yaml-1.3.2-5.6-ts-vc11-x64.zip

在你的扩展文件夹内，会有四个DLL文件（每个文件夹有两个其余的是源码，没啥用）：

yaml文件夹有yaml.dll php_yaml.dll

threads文件夹有threads_VC2.dll php_threads.dll

把不带php_前缀复制到C:\Windows和php文件夹内，带php_前缀的复制到php\ext\文件夹内（那个文件夹都是带php_前缀的）

4. 运行

运行start.bat，按照安装向导指示。

### Linux & Mac

运行src/build/compile.sh和installer.sh，进行自动安装。

### 内网穿透

1. 下面说我们的重点：内网穿透。

大部分教程都是使用路由器穿透的，然而大部分人（中国移动出来挨打）没有固定的公网IP，甚至连公网IP都没有。我向大家推荐一个网站：https://www.natfrp.com/，这个网站完全免费（穿透Web需要实名认证收费）。

注册一个账号，按照图片中的配置穿透隧道（TCP和UDP，有人说不需要TCP，但是为了保险还是一起穿透吧）

![](https://cdn.luogu.com.cn/upload/image_hosting/ntyh5g9c.png)
![](https://cdn.luogu.com.cn/upload/image_hosting/1av0nep2.png)

2. 你现在应该有了两个隧道（忽略“在线”）：

![](https://cdn.luogu.com.cn/upload/image_hosting/at92shxr.png)

记住最开头的ID，还有你的访问密钥：
![](https://cdn.luogu.com.cn/upload/image_hosting/rru1dvsc.png)

下载frpc软件：https://getfrp.sh/d/frpc_windows_amd64.exe

放在PocketMine文件夹内，在start.cmd（或是start_without_mintty.cmd）内添加：

```frpc.exe -f 访问密钥:ID1,ID2```

示例：```frpc.exe -f 114514abcd:1145141,1145142```

3.如图，如果没有域名，就可以输入节点的域名（IP也行）和外网端口。
![](https://cdn.luogu.com.cn/upload/image_hosting/87qn8293.png)

如果你有域名，就请CNAME解析，使用你的域名和外部端口。
![](https://cdn.luogu.com.cn/upload/image_hosting/0wt54jgq.png)

域名解析大概是这样：
![](https://cdn.luogu.com.cn/upload/image_hosting/43z2ut50.png)

这里有一个深坑啊，解析之后别立马尝试，要等待1-5分钟，否则域名解析不生效也不行

### 本次提交的更新日志

### 有关版本号的约定

版本号固定为Alpha_1.3.12(Hack Version Alpha_1.x.x)，适用于MCPE Alpha_0.8.1（也许0.9.x也能用吧）

目前的版本代号以半条命的人物命名，例：戈登·弗里曼（Gordon Freeman）

### 招募

欢迎fork本仓库然后提交合并请求，为这个开源仓库做贡献。

本人实在没时间+看不懂代码，所以会很少更新。