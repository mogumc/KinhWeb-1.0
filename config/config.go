// 配置文件读取
// @author MoGuQAQ
// @version 1.0.0

package config

import (
	"io/ioutil"

	"gopkg.in/yaml.v3"
)

type config struct {
	System system `yaml:"system"`
	Theme  theme  `yaml:"theme"`
	Logger logger `yaml:"logger"`
	User   user   `yaml:"user"`
}

type system struct {
	Bind_port int    `yaml:"bind_port"`
	Bind_host string `yaml:"bind_host"`
	Sys_theme string `yaml:"theme"`
	Sys_title string `yaml:"title"`
	Sys_foot  string `yaml:"foot"`
	Debug     bool   `yaml:"debug"`
}

type theme struct {
	Theme_color string `yaml:"color"`
}

type logger struct {
	Level        string `yaml:"level"`
	Prefix       string `yaml:"prefix"`
	Showline     bool   `yaml:"show_line"`
	LogInConsole bool   `yaml:"log_in_console"`
}

type user struct {
	Bduss   string `yaml:"bduss"`
	Is_VIP  int    `yaml:"is_vip"`
	AccLink string `yaml:"acclink"`
}

var Config *config

func init() {
	yamlFile, err := ioutil.ReadFile("./config.yaml")
	if err != nil {
		return
	}
	yaml.Unmarshal(yamlFile, &Config)
}
