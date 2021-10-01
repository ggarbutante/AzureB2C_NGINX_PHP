package main

import (
	"bytes"
	"crypto/rsa"
	"crypto/x509"
	"encoding/base64"
	"encoding/json"
	"encoding/pem"
	"os"
	"fmt"
	"log"
	"math/big"
	"io/ioutil"
)

func check(e error) {
    if e != nil {
        panic(e)
    }
}

func main() {
	js, err := ioutil.ReadFile("/var/www/html/.jwk.txt")
	check(err)
	//fmt.Print(string(dat))

	jwk := map[string]string{}
	json.Unmarshal([]byte(js), &jwk)

	if jwk["kty"] != "RSA" {
		log.Fatal("invalid key type:", jwk["kty"])
	}

	// decode the base64 bytes for n
	nb, err := base64.RawURLEncoding.DecodeString(jwk["n"])
	if err != nil {
		log.Fatal(err)
	}

	e := 0
	// The default exponent is usually 65537, so just compare the
	// base64 for [1,0,1] or [0,1,0,1]
	if jwk["e"] == "AQAB" || jwk["e"] == "AAEAAQ" {
		e = 65537
	} else {
		// need to decode "e" as a big-endian int
		log.Fatal("need to deocde e:", jwk["e"])
	}

	pk := &rsa.PublicKey{
		N: new(big.Int).SetBytes(nb),
		E: e,
	}

	der, err := x509.MarshalPKIXPublicKey(pk)
	if err != nil {
		log.Fatal(err)
	}

	block := &pem.Block{
		Type:  "RSA PUBLIC KEY",
		Bytes: der,
	}

	var out bytes.Buffer
	pem.Encode(&out, block)
	//fmt.Println(out.String())

	pub_key, err := os.Create("/var/www/html/.pub.key")
	check(err)

	l_bytes, err := pub_key.WriteString(out.String())
	check(err)
	fmt.Printf("Wrote %d bytes\n", l_bytes)

	pub_key.Close()
}
