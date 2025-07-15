// 网盘文件下载地址返回
// @author MoGuQAQ
// @version 1.0.0

package api

import (
	"encoding/base64"
	"encoding/json"
	"kinhweb/config"
	"kinhweb/core"
	"kinhweb/global"
	"kinhweb/result"
	"kinhweb/utils"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
)

func Down(c *gin.Context) {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	fid := c.Query("fid")
	mode := c.Query("m")
	if fid == "" {
		result.Failed(c, -1, "Param Error")
		return
	}

	acclink := config.Config.User.AccLink
	apipath := config.Config.User.ApiPath
	if apipath == "" {
		config.Config.User.ApiPath = "https://pan.baidu.com"
		config.UpdateYaml(config.Config)
		apipath = config.Config.User.ApiPath
	}
	BDUSS := config.Config.User.Bduss
	if BDUSS == "" {
		global.Log.Warnf("未填写BDUSS!")
		result.Failed(c, 501, "未配置后端账户")
		return
	}

	intfid, err := strconv.Atoi(fid)
	if err != nil {
		global.Log.Warnf("请求 Fid->%s 不是一个有效的参数", fid)
		result.Failed(c, -1, "Param Error")
		return
	}
	var data interface{}
	if acclink == "" {
		global.Log.Infof("当前处于本地解析模式")
		url := apipath + "/api/filemetas?dlink=1&clienttype=17&rt=third&vip=2&fsids=[%22" + fid + "%22]"
		res := utils.Get(url, "netdisk;Mo", "BDUSS="+BDUSS+";PANPSC=;BAIDUID=1;ndut_fmt="+utils.Getndut())
		var JsonData map[string]interface{}
		if json.Unmarshal([]byte(res), &JsonData) == nil {
			errno := JsonData["errno"].(float64)
			global.Log.Infof("请求 Fid->%d 返回状态码 errno->%d", intfid, int(errno))
			if errno != 0 {
				result.Failed(c, int(errno), "获取下载地址失败")
			} else {
				info, ok := JsonData["info"].([]interface{})
				if !ok || len(info) == 0 {
					global.Log.Errorf("info 为空")
					result.Failed(c, int(errno), "获取下载地址失败")
					return
				}
				odlink, ok := JsonData["info"].([]interface{})[0].(map[string]interface{})["dlink"].(string)
				if !ok {
					global.Log.Errorf("百度返回数据异常")
					result.Failed(c, int(errno), "获取下载地址失败")
					return
				}
				dl := strings.Replace(strings.Replace(odlink, "d.pcs.baidu.com", "218.93.204.36/b/d.pcs.baidu.com", -1), "https", "http", -1) + "&clienttype=17&channel=0&version=7.22.0.8&" + utils.Getrand(BDUSS)
				headResult := utils.Head(dl, c.Request.Header.Get("User-Agent"), "")
				dlink, ok := headResult["Location"]
				if !ok {
					dl = odlink + "&clienttype=17&channel=0&version=7.22.0.8&" + utils.Getrand(BDUSS)
					headResult := utils.Head(dl, c.Request.Header.Get("User-Agent"), "")
					dlink, ok = headResult["Location"]
					if !ok {
						result.Failed(c, 99, "获取下载地址失败")
						return
					}
				}
				if len(dlink) < 1 || dlink[0] == "" {
					result.Failed(c, int(errno), "获取下载地址失败")
					return
				}
				if mode == ".baidu.com" {
					c.Redirect(302, dlink[0])
					return
				}
				data = gin.H{"fid": intfid, "dlink": dlink[0]}
				result.Success(c, data)
			}
		} else {
			global.Log.Errorf("解析Json失败 Url->%s", url)
			result.Failed(c, 500, "获取失败")
		}
	} else {
		global.Log.Infof("当前处于远程解析模式")
		res := utils.Get(acclink, "netdisk;Mo", "")
		var JsonData map[string]interface{}
		if json.Unmarshal([]byte(res), &JsonData) == nil {
			code := JsonData["code"].(string)
			intcode, err := strconv.Atoi(code)
			if err != nil {
				global.Log.Warnf("加速链接返回了无效数据")
				result.Failed(c, -1, "无效的加速链接")
				return
			}
			if code != "0" {
				global.Log.Errorf("加速链接无效 Url->%s", acclink)
				result.Failed(c, intcode, "无效的加速链接")
			} else {
				pdata := "bduss=" + BDUSS + "&fid=" + fid + "&ua=" + base64.StdEncoding.EncodeToString([]byte(c.Request.Header.Get("User-Agent")))
				res = utils.Post(acclink, "KinhWeb", "", pdata)
				var JsonData map[string]interface{}
				if json.Unmarshal([]byte(res), &JsonData) == nil {
					errno := JsonData["errno"].(float64)
					if err != nil {
						global.Log.Warnf("请求的加速链接返回了无效数据")
						result.Failed(c, -1, "无效的加速链接")
						return
					}
					if errno != 0 {
						global.Log.Errorf("获取下载地址失败")
						result.Failed(c, int(errno), "获取下载地址失败")
						return
					}
					dlink := JsonData["dlink"].(string)
					if dlink == "" {
						global.Log.Errorf("获取下载地址失败")
						result.Failed(c, int(errno), "获取下载地址失败")
						return
					}
					global.Log.Infof("获取到地址 %s", dlink)
					if mode == ".baidu.com" {
						c.Redirect(302, dlink)
						return
					}
					data = gin.H{"fid": intfid, "dlink": dlink}
					result.Success(c, data)
				} else {
					global.Log.Errorf("解析Json失败 Url->%s", acclink)
					result.Failed(c, 500, "获取失败")
				}
			}
		} else {
			global.Log.Errorf("解析Json失败 Url->%s", acclink)
			result.Failed(c, 500, "获取失败")
		}
	}
}
