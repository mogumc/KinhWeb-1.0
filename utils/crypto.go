// 加密类
// @author MoGuQAQ
// @version 1.0.0

package utils

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"crypto/rc4"
)

func Enrc4(key []byte, str []byte) []byte {
	result := make([]byte, len(str))
	cipher1, _ := rc4.NewCipher(key)
	cipher1.XORKeyStream(result, str)
	return result
}

func Derc4(key []byte, str []byte) []byte {
	result := make([]byte, len(str))
	cipher2, _ := rc4.NewCipher(key)
	cipher2.XORKeyStream(result, str)
	return result
}

func pkcs7Padding(data []byte, blockSize int) []byte {
	padding := blockSize - len(data)%blockSize
	padText := bytes.Repeat([]byte{byte(padding)}, padding)
	return append(data, padText...)
}

func AesEncrypt(data []byte, key []byte, iv []byte) ([]byte, error) {
	block, err := aes.NewCipher(key)
	if err != nil {
		return nil, err
	}
	blockSize := block.BlockSize()
	encryptBytes := pkcs7Padding(data, blockSize)
	crypted := make([]byte, len(encryptBytes))
	blockMode := cipher.NewCBCEncrypter(block, iv)
	blockMode.CryptBlocks(crypted, encryptBytes)
	return crypted, nil
}
