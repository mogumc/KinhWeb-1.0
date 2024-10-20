// 测试接口

package api

import (
	"kinhweb/result"

	"github.com/gin-gonic/gin"
)

// @router /api/success
func Success(c *gin.Context) {
	result.Success(c, 200)
}

// @router /api/failed
func Failed(c *gin.Context) {
	result.Failed(c, int(result.ApiCode.Failed), result.ApiCode.GetMessage(result.ApiCode.Failed))
}
