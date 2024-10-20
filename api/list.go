// 文件列表路由
// @author MoGuQAQ
// @version 1.0.0

package api

import (
	"encoding/json"
	"kinhweb/config"
	"kinhweb/core"
	"kinhweb/global"
	"kinhweb/result"
	"kinhweb/utils"
	"net/url"

	"github.com/gin-gonic/gin"
)

func List(c *gin.Context) {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	dir := c.Query("dir")
	if dir == "" {
		result.Failed(c, -1, "Param Error")
	} else {
		url := "http://110.242.69.43/api/list?order=time&dir=" + url.QueryEscape(dir)
		BDUSS := config.Config.User.Bduss
		if BDUSS == "" {
			global.Log.Warnf("未填写BDUSS!")
			result.Failed(c, 501, "未配置后端账户")
		} else {
			res := utils.Get(url, "netdisk;Mo", "BDUSS="+BDUSS)
			var JsonData map[string]interface{}
			if json.Unmarshal([]byte(res), &JsonData) == nil {
				errno := JsonData["errno"].(float64)
				global.Log.Infof("请求 Path->%s 返回状态码 errno->%d", dir, int(errno))
				var data interface{}
				if errno == 0 {
					var lists = JsonData["list"].([]interface{})
					data = gin.H{"dir": dir, "list": lists}
					result.Success(c, data)
				} else {
					data = gin.H{"dir": dir, "list": nil}
					result.Failed(c, int(errno), "文件列表为空")
				}
			} else {
				global.Log.Errorf("解析Json失败 Url->%s", url)
				result.Failed(c, 500, "获取失败")
			}
		}
	}

}
