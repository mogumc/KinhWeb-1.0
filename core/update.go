// 更新验证文件
// @author MoGuQAQ
// @version 1.0.0

package core

import (
	"kinhweb/config"
	"kinhweb/global"
)

func GetUpadate() {
	// ToDo 检查更新
}

func Verify() {
	//请务必每次更新时覆盖此文件便于完成版本更新
	if global.Log == nil {
		global.Log = InitLogger()
	}
	version := config.Config.System.Sys_version
	this_file_version := "1.0.0"
	if version != this_file_version {
		global.Log.Warnf("检测到版本不一致")
		config.Config.System.Sys_version = this_file_version
		config.UpdateYaml(config.Config)
		global.Log.Infof("版本 %s -> %s", version, this_file_version)
	} else {
		global.Log.Infof("版本一致")
	}
}
