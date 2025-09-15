<div align="center">
    <img src="./logo.png" width="200">
    <h1></h1>
</div>


<div align="center">
  <a href="https://github.com/mogumc/KinhWeb/releases"><img alt="Release" src="https://img.shields.io/github/v/release/mogumc/KinhWeb?logo=visualstudio&style=flat-square&color=1E9BFA"></a>
    <img src="https://komarev.com/ghpvc/?username=mogumc&label=Views&color=orange&style=flat" alt="访问量统计" />
  <h3>Go+Gin 高性能百度网盘仓库</h3>
</div>

<br/>

## 安装使用  
目前支持Linux与Windows部署,其他平台请自行编译运行.  
你可以复制本项目下的``_config.yaml``文件重命名为``config.yaml``  
在``config.yaml``文件下填写``user``部分信息.  

```yaml
user:
  #百度账号BDUSS,建议使用网页版Cookie
  bduss:
  #对下载无明显影响,可以不改
  is_vip: 0
  #奇妙小链接,不清楚就留空
  acclink:
```

如果您想修改标题界面,可以修改如下内容.您也可以自行开发主题放于``templates``目录下.
```yaml
system:
    bind_port: 8080
    bind_host: 0.0.0.0
    version: 1.0.0
    #使用的主题
    theme: weui
    #网站标题
    title: KinhWeb
    #网站页脚
    foot: ""
    debug: false
```

如果需要公开使用,建议使用Linux系统并使用``screen``挂载本程序,使用``Nginx``作为前端反向代理.
## 功能
### V1.0.0版本
    1. 支持文件列表查看
    2. 支持文件下载
    3. 支持自定义主题

## FaQ
如果有任何使用问题请在群组[@KinhWeb](https://t.me/kinhweb)提出,或者在本仓库下提交Issue.  
关注频道[@KinhWebPD](https://t.me/kinhwebpd)获取最新更新信息.  

## License
GPL-2.0 license
