<?php

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

/*
 * CMBC统一处理SDK
 */

class CMBC_SDK_COMMON {

    /**
     * 签名处理
     * @param type $signAlg 签名算法（SM3withSM2等）
     * @param type $base64SourceData 原64位数据（提交订单数据等）
     * @param type $base64P12Data 签名证书（使用商户私钥证书）
     * @param type $p12Password 签名证书密码（商户私钥证书密码）
     * @return type SDK返回签名JSON串 业务参数为Base64SignatureData，返回签名64位字符串
     */
    function P1SignMessage($signAlg, $base64SourceData, $base64P12Data, $p12Password) {
        return lajp_call("cfca.sadk.api.SignatureKit::P1SignMessage", $signAlg, $base64SourceData, $base64P12Data, $p12Password);
    }

    /**
     * 验证签名处理
     * @param type $signAlg 签名算法（SM3withSM2等）
     * @param type $base64SourceData 原64位数据（加密报文数据）
     * @param type $base64X509CertData 验证签名使用证书（使用平台公钥证书）
     * @param type $base64P1SignatureData P1签名64位字符串（sign值）
     * @return type SDK返回签名JSON串 业务参数：Result为True时执行
     */
    function P1VerifyMessage($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData) {
        return lajp_call("cfca.sadk.api.SignatureKit::P1VerifyMessage", $signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData);
    }

    /**
     * 加密业务报文（订单数据和签名数据）处理
     * @param type $signAlg 签名算法
     * @param type $base64SourceData 原64位数据（业务报文数据）
     * @param type $base64CertData 加密签名使用证书（使用平台公钥证书）
     * @return type SDK返回加密JSON串 业务参数Base64EnvelopeMessage 加密后报文数据
     */
    function envelopeMessage($signAlg, $base64SourceData, $base64CertData) {
        return lajp_call("cfca.sadk.api.EnvelopeKit::envelopeMessage", $base64SourceData, $signAlg, $base64CertData);
    }

    /**
     * 解密业务报文（平台返回数据）处理
     * @param type $signAlg 签名算法
     * @param type $base64EnvelopeData 原64位加密数据（业务报文数据）
     * @param type $base64P12Data 解密签名使用证书（使用商户私钥解密）
     * @param type $p12Password 解密签名证书的密码（商户私钥密码）
     * @return type SDK放回解密字符串 业务参数Base64SourceString 平台返回json字符串
     */
    function openEvelopedMessage($signAlg, $base64EnvelopeData, $base64P12Data, $p12Password) {
        return lajp_call("cfca.sadk.api.EnvelopeKit::openEvelopedMessage", $base64EnvelopeData, $signAlg, $base64P12Data, $p12Password);
    }

}
