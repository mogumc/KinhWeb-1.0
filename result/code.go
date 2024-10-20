// 状态码与状态信息处理
// @author MoGuQAQ
// @version 1.0.0

package result

type Codes struct {
	Message map[uint]string
	Sucess  uint
	Failed  uint
}

var ApiCode = &Codes{
	Sucess: 200,
	Failed: 501,
}

func Init() {
	ApiCode.Message = map[uint]string{
		ApiCode.Sucess: "请求成功",
		ApiCode.Failed: "网关错误",
	}
}

func (c *Codes) GetMessage(code uint) string {
	message, ok := c.Message[code]
	if !ok {
		return ""
	}
	return message
}
