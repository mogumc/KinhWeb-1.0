// 百度类
// @author MoGuQAQ
// @version 1.0.0

package utils

import (
	"crypto/sha1"
	"encoding/hex"
	"encoding/json"
	"kinhweb/core"
	"kinhweb/global"
	"strconv"
	"strings"
	"time"
)

func Getndut() string {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	var data = "time=" + strconv.FormatInt(time.Now().Unix(), 10) + ";ua=other"
	var key = []byte("01hltm9JcnEfqy5t")
	var iv = []byte("Fsadviz5BSekw310")
	var datas = []byte(data)
	var ndut_fmt = "-1"
	var ndut, err = AesEncrypt(datas, key, iv)
	if err != nil {
		global.Log.Errorf(err.Error())
	} else {
		ndut_fmt = strings.ToUpper(hex.EncodeToString(ndut))
	}
	return ndut_fmt
}

func Getrand(bduss string) string {
	if global.Log == nil {
		global.Log = core.InitLogger()
	}
	Url := "https://wenku.baidu.com/customer/interface/vipinfo"
	result := Get(Url, "netdisk;11.0.0", "BDUSS="+bduss)
	var JsonData map[string]interface{}
	Time := strconv.FormatInt(time.Now().Unix(), 10)
	if json.Unmarshal([]byte(result), &JsonData) == nil {
		uid := JsonData["data"].(map[string]interface{})["uid"].(float64)
		UserIDString := strconv.FormatInt(int64(uid), 10)
		global.Log.Infof("UID->%d", UserIDString)
		DevUIDSha1Byte := sha1.Sum([]byte(bduss))
		DevUID := hex.EncodeToString(DevUIDSha1Byte[:])
		BDUSSSha1Byte := sha1.Sum([]byte(bduss))
		BDUSSSha1 := hex.EncodeToString(BDUSSSha1Byte[:])
		RandByte := sha1.Sum([]byte(BDUSSSha1 + UserIDString + "ebrcUYiuxaZv2XGu7KIYKxUrqfnOfpDF" + Time + DevUID))
		Rand := hex.EncodeToString(RandByte[:])
		return "rand=" + Rand + "&rand2=" + Rand + "&devuid=" + DevUID + "&time=" + Time
	} else {
		return "rand=0000&rand2=0000&devuid=114514&time=" + Time
	}
}
