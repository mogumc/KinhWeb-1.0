// 网关路由初始化与注册
// @author MoGuQAQ
// @version 1.0.0

package router

import (
	"kinhweb/api"
	"kinhweb/config"

	"github.com/gin-gonic/gin"
)

func InitRouter() *gin.Engine {
	GinMode := "release"
	if config.Config.System.Debug {
		GinMode = "debug"
	}
	gin.SetMode(GinMode)
	router := gin.New()
	router.Use(gin.Recovery())
	register(router)
	return router
}

func register(router *gin.Engine) {
	//下载文件路由
	router.GET("/api/success", api.Success)
	router.GET("/api/failed", api.Failed)
}
