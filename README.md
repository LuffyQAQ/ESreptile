# EasySwoole3.4.x实现简单的爬虫爬取网上的图片

## 之前写的版本已更新为最新版本，并且queryList也已经更新为正常使用的版本

### 利用EasySwoole 自己实现的协程redis以及redis连接池，实现自定义进程，达到队列消费，爬取数据的目的。并利用QueryList强大而优雅的CSS选择器来做采集，大大降低了PHP做采集的门槛而EasySwoole 是一款基于Swoole Server 开发的常驻内存型的分布式PHP框架，专为API而生，摆脱传统PHP运行模式在进程唤起和文件加载上带来的性能损失。 EasySwoole 高度封装了 Swoole Server 而依旧维持 Swoole Server 原有特性，支持同时混合监听HTTP、自定义TCP、UDP协议，让开发者以最低的学习成本和精力编写出多进程，可异步，高可用的应用服务


- 下载clone 到本地
- 执行 php easyswoole server start 即可看到效果
- 只需要几分钟就可把全部图片爬取完成




### 本代码仅供学习参考，切勿用于非法用途，如有问题，请到easyswoole 官方QQ群联系我，搜索 “北溟有鱼QAQ” 即可
