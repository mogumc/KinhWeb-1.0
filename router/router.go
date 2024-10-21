// 网关路由初始化与注册
// @author MoGuQAQ
// @version 1.0.0

package router

import (
	"kinhweb/api"
	"kinhweb/config"
	"kinhweb/core"
	"kinhweb/global"

	"github.com/gin-gonic/gin"
)

func InitRouter() *gin.Engine {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	GinMode := "release"
	if config.Config.System.Debug {
		GinMode = "debug"
		global.Log.Warnf("当前运行在测试环境中!")
	}
	gin.SetMode(GinMode)
	router := gin.New()
	router.Use(gin.Logger(), gin.Recovery())
	Register(router)
	return router
}

func Register(router *gin.Engine) {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	theme := config.Config.System.Sys_theme
	if theme == "" {
		global.Log.Warnf("获取主题信息失败,将使用默认主题")
		theme = "default"
	}
	global.Log.Infof("使用主题: %s", theme)
	//注册模板文件
	router.LoadHTMLGlob("templates/" + theme + "/html/*")
	global.Log.Infof("获取模版目录成功")
	router.Static("/static", "templates/"+theme+"/static")
	global.Log.Infof("注册路由 /static 成功")
	global.Log.Infof("注册静态文件成功")
	//下载文件路由
	router.GET("/api/down", api.Down)
	global.Log.Infof("注册路由 /api/down 成功")
	//文件列表路由
	router.GET("/api/list", api.List)
	global.Log.Infof("注册路由 /api/list 成功")
	//主文件路由
	router.GET("/", api.Index)
	global.Log.Infof("注册路由 / 成功")
}
