这个仓库是PocketMine Alpha_1.13.2 (Minecraft PE Alpha_0.8.1) 的改进版，增加了个人的优化以及汉化。
由于原仓库使用LGPL协议，所以修改后的代码必须公布，并使用LGPL许可证。

### Windows教程
提示：本教程应用的是电脑端。很多教程都使用手机端，但是我建议使用电脑端，因为电脑端可以不占手机内存，方便内网穿透。
提示：下文中（包括上文），内网穿透即为端口映射。
使用Sakura Frp进行内网穿透（不是广告），可以使用逍遥模拟器模拟MCPE在电脑上的运行（不是广告x2）

1. 安装git for windows，链接在此：https://github.com/git-for-windows/git/releases/download/v2.32.0.windows.2/Git-2.32.0.2-64-bit.exe
![](http://tiebapic.baidu.com/forum/w%3D580/sign=6fe0c455c239b6004dce0fbfd9513526/d7d93bd7912397dddb6a5fb24e82b2b7d1a28753.jpg)
![](http://tiebapic.baidu.com/forum/w%3D580/sign=7d4c77b3f2dde711e7d243fe97eecef4/88c81af5e0fe9925141faee623a85edf8cb17153.jpg)
![](http://tiebapic.baidu.com/forum/w%3D580/sign=07919e07970a19d8cb03840d03fb82c9/63299086c9177f3eac273bbb67cf3bc79e3d5653.jpg)

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

4. 下面说怎么配置MinTTY：
首先，MinTTY需要Cygwin环境（不要直接下载），可以看这个：https://blog.csdn.net/lvsehaiyang1993/article/details/81027399
注意：配置镜像时可以使用mirror.aliyun.com，速度显著增快。
不用安装多余的包，Cygwin自带MinTTY。
然后从C:\cygwin64\bin（默认的）文件夹内找到MinTTY.exe和cygwin1.dll，复制（不是剪切）到PocketMine的bin文件夹内，此时，你的bin文件夹内应该是这样的：
![](http://tiebapic.baidu.com/forum/w%3D580/sign=4481c801fe50352ab16125006342fb1a/13a59a345982b2b7aed0fe7226adcbef77099b1f.jpg)
PocketMine.ico可以使用这个网站：https://www.aconvert.com/cn/icon/png-to-ico/
选择128*128，链接输入http://www.pocketmine.net/favicon.png，生成重命名即可。
如果你有MinTTY，运行start.cmd，否则运行start_without_mintty.cmd，按照指引选择zh，配置文件即可（可以参考其他教程）
**安装mintty是可选的，如果你不需要，你可以不用下载Cygwin和制作图标**

### Linux教程
1. 安装PHP5.6，配置yaml和pthreads扩展（线程安全）

2. 直接运行php文件

### 内网穿透
1. 下面说我们的重点：内网穿透。
大部分教程都是使用路由器穿透的，然而大部分人（中国移动出来挨打）没有固定的公网IP，甚至连公网IP都没有。我向大家推荐一个网站：https://www.natfrp.com/，这个网站完全免费（穿透Web需要实名认证收费）。
注册一个账号，按照图片中的配置穿透隧道（TCP和UDP，有人说不需要TCP，但是为了保险还是一起穿透吧）
![](http://tiebapic.baidu.com/forum/w%3D580/sign=12c3d7abc32a283443a636036bb4c92e/74bd27385343fbf2c08b6195a77eca8067388fc7.jpg)
![](http://tiebapic.baidu.com/forum/w%3D580/sign=6f53c455c239b6004dce0fbfd9513526/d7d93bd7912397dddbd95fb24e82b2b7d2a287c2.jpg)

2. 你现在应该有了两个隧道（忽略“在线”）：
![](http://tiebapic.baidu.com/forum/w%3D580/sign=423ff1bd4cb5c9ea62f303ebe538b622/00f0a413b07eca80139cfe51862397dda34483c9.jpg)
记住最开头的ID，还有你的访问密钥：
![](http://tiebapic.baidu.com/forum/w%3D580/sign=deb9817695025aafd3327ec3cbecab8d/05a474a98226cffc11bd7804ae014a90f403ead7.jpg)
下载frpc软件：https://getfrp.sh/d/frpc_windows_amd64.exe
放在PocketMine文件夹内，在start.cmd（或是start_without_mintty.cmd）内添加：
```frpc.exe -f 访问密钥:ID1,ID2```
示例：```frpc.exe -f 114514abcd:1145141,1145142```

3.如图，如果没有域名，就可以输入节点的域名（IP也行）和外网端口。
![](http://tiebapic.baidu.com/forum/w%3D580/sign=8e8d219d19f41bd5da53e8fc61db81a0/871bd1cd7b899e51fc20fca255a7d933c9950d38.jpg)
如果你有域名，就请CNAME解析，使用你的域名和外部端口。
![](http://tiebapic.baidu.com/forum/w%3D580/sign=2688fd720cd5ad6eaaf964e2b1ca39a3/542c4c899e510fb3fbb7b121ce33c895d0430c38.jpg)
域名解析大概是这样：
![](http://tiebapic.baidu.com/forum/w%3D580/sign=18e529325ded2e73fce98624b700a16d/1be07b086e061d9524e4f18e6cf40ad160d9cacc.jpg)
这里有一个深坑啊，解析之后别立马尝试，要等待1-5分钟，否则域名解析不生效也不行