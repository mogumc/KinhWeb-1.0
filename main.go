// 启动文件
// @author MoGuQAQ
// @version 1.0.0

package main

import (
	"fmt"
	"kinhweb/config"
	"kinhweb/core"
	"kinhweb/global"
	"kinhweb/router"
)

func main() {
	global.Log = core.InitLogger()
	global.Log.Infof("程序开始运行...")
	global.Log.Infof("验证版本信息...")
	core.Verify()
	global.Log.Infof("初始化网关")
	router := router.InitRouter()
	global.Log.Infof("初始化前端文件")
	global.Log.Infof("注册API路由")
	address := fmt.Sprintf("%s:%d", config.Config.System.Bind_host, config.Config.System.Bind_port)
	global.Log.Infof("网关启动成功 运行在: %s", address)
	router.Run(address)
}
