// 主文件路由
// @author MoGuQAQ
// @version 1.0.0

package api

import (
	"kinhweb/config"
	"kinhweb/core"
	"kinhweb/global"
	"net/http"

	"github.com/gin-gonic/gin"
)

func Index(c *gin.Context) {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	title := config.Config.System.Sys_title
	if title == "" {
		global.Log.Warnf("获取标题信息失败,将使用默认标题")
		title = "KinhWeb"
	}
	foot := config.Config.System.Sys_foot
	if foot == "" {
		global.Log.Warnf("获取 尾部信息失败,默认留空")
		foot = ""
	}
	version := config.Config.System.Sys_version
	if version == "" {
		global.Log.Fatalf("获取版本信息异常!请检查程序完整性!")
	}
	c.HTML(http.StatusOK, "index.html", gin.H{
		"Title":   title,
		"Foot":    foot,
		"Version": version,
	})

}
