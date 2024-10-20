// 请求类
// @author MoGuQAQ
// @version 1.0.0

package utils

import (
	"bytes"
	"crypto/tls"
	"io"
	"log"
	"net/http"
	"strings"
	"time"
)

var client = &http.Client{
	CheckRedirect: func(req *http.Request, via []*http.Request) error {
		return http.ErrUseLastResponse
	},
	Timeout: 15 * time.Second,
	Transport: &http.Transport{
		TLSClientConfig: &tls.Config{
			InsecureSkipVerify: true,
		},
	},
}

func Get(url string, ua string, cookie string) string {
	req, _ := http.NewRequest("GET", url, nil)
	req.Header.Set("User-Agent", ua)
	req.Header.Set("Cookie", cookie)
	resp, err := client.Do(req)
	body := new(bytes.Buffer)
	if err != nil {
		log.Println(err)
	} else {
		defer resp.Body.Close()
		io.Copy(body, resp.Body)
	}
	result := body.String()
	return result
}

func Head(url string, ua string, cookie string) http.Header {
	req, _ := http.NewRequest("GET", url, nil)
	req.Header.Set("User-Agent", ua)
	req.Header.Set("Cookie", cookie)
	resp, err := client.Do(req)
	if err != nil {
		log.Println(err)
	} else {
		defer resp.Body.Close()
	}
	body := resp.Header
	return body
}

func Post(url string, ua string, cookie string, data string) string {
	req, _ := http.NewRequest("POST", url, strings.NewReader(data))
	req.Header.Set("User-Agent", ua)
	req.Header.Set("Cookie", cookie)
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	resp, err := client.Do(req)
	body := new(bytes.Buffer)
	if err != nil {
		log.Println(err)
	} else {
		defer resp.Body.Close()
		io.Copy(body, resp.Body)
	}
	result := body.String()
	return result
}

func HeadPost(url string, ua string, cookie string, data string) http.Header {
	req, _ := http.NewRequest("POST", url, strings.NewReader(data))
	req.Header.Set("User-Agent", ua)
	req.Header.Set("Cookie", cookie)
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	resp, err := client.Do(req)
	if err != nil {
		log.Println(err)
	} else {
		defer resp.Body.Close()
	}
	body := resp.Header
	return body
}
