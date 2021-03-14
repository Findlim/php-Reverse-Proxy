基本就是  thinkphp 的伪静态规则

iis6.x   下使用 httpd.ini

iis7.x   下使用web.config

apache下使用 .htaccess  
(编辑.htaccess文件，把 RewriteBase /wnjx1 修改为你苹果CMS万能镜像系统所在目录)

nginx 下使用 nginx.conf
(使用vps或者服务器的可以在你的主机的conf里 用 include xxxxx.conf   也就是包含下伪静态规则文件
如果用的是虚拟主机版的nginx 就找你的主机商给你添加规则就行，你把规则发给他。)
