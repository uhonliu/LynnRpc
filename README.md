LynnRpc
===================================

使用Phalcon + Hprose + Kafka搭建高可用性RPC服务。
———————————————————————————————————

支持同步调用及数据异步处理, 
可用于复杂项目数据层与业务层分离，
分离后项目可以通过API更好的进行跟踪、升级、维护及管理。

使用方式:
———————————————————————————————————

环境依赖:需要PHP的Phalcon+swoole+redis+kafka相关扩展。
部署方式:服务端服务开启 'php ./producer/server.php', 消费者服务开启 'php ./consumer/cli.php write'。
调用方式:参照client进行调用。
发布方式:通过 './listController' 自动发布对外暴露接口。