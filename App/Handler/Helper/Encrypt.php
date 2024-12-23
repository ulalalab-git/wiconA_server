<?php
namespace App\Handler\Helper;

/**
 *
 */
class Encrypt
{
    /**
     * [decodebase64 description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function decodebase64($data)
    {
        return base64_decode(
            str_pad(
                strtr($data, '-_', '+/'),
                strlen($data) % 4,
                '=',
                STR_PAD_RIGHT
            )
        );
    }

    /**
     * [decrypt description]
     * @param  [type] $data     [description]
     * @param  [type] $key      [description]
     * @param  [type] $iv       [description]
     * @param  array  $settings [description]
     * @return [type]           [description]
     */
    public function decrypt($data, $key, $iv, $settings = [])
    {
        if ($data === '' || ! extension_loaded('mcrypt')) {
            return $data;
        }

        //Merge settings with defaults
        $defaults = [
            'algorithm' => MCRYPT_RIJNDAEL_256,
            'mode'      => MCRYPT_MODE_CBC,
        ];
        $settings = array_merge($defaults, $settings);

        //Get module
        $module = mcrypt_module_open($settings['algorithm'], '', $settings['mode'], '');

        //Validate IV
        $ivSize = mcrypt_enc_get_iv_size($module);
        if (strlen($iv) > $ivSize) {
            $iv = substr($iv, 0, $ivSize);
        }

        //Validate key
        $keySize = mcrypt_enc_get_key_size($module);
        if (strlen($key) > $keySize) {
            $key = substr($key, 0, $keySize);
        }

        //Decrypt value
        mcrypt_generic_init($module, $key, $iv);
        $decryptedData = @mdecrypt_generic($module, $data);
        $res           = rtrim($decryptedData, "\0");
        mcrypt_generic_deinit($module);

        return $res;
    }

    /**
     * [decryptEx description]
     * @param  [type] $data   [description]
     * @param  [type] $secret [description]
     * @return [type]         [description]
     */
    public function decryptEx($data, $secret)
    {
        $key = md5(utf8_encode($secret), true);
        $key .= substr($key, 0, 8);
        $data  = base64_decode($data);
        $data  = mcrypt_decrypt('tripledes', $key, $data, 'ecb');
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len   = strlen($data);
        $pad   = ord($data[$len - 1]);

        return substr($data, 0, strlen($data) - $pad);
    }

    /**
     * [encodebase64 description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function encodebase64($data)
    {
        return str_replace(
            ['+', '/'],
            ['-', '_'],
            rtrim(base64_encode($data), "=")
        );
    }

    /**
     * [encrypt description]
     * @param  [type] $data     [description]
     * @param  [type] $key      [description]
     * @param  [type] $iv       [description]
     * @param  array  $settings [description]
     * @return [type]           [description]
     */
    public function encrypt($data, $key, $iv, $settings = [])
    {
        if ($data === '' || ! extension_loaded('mcrypt')) {
            return $data;
        }

        //Merge settings with defaults
        $defaults = [
            'algorithm' => MCRYPT_RIJNDAEL_256,
            'mode'      => MCRYPT_MODE_CBC,
        ];
        $settings = array_merge($defaults, $settings);

        //Get module
        $module = mcrypt_module_open($settings['algorithm'], '', $settings['mode'], '');

        //Validate IV
        $ivSize = mcrypt_enc_get_iv_size($module);
        if (strlen($iv) > $ivSize) {
            $iv = substr($iv, 0, $ivSize);
        }

        //Validate key
        $keySize = mcrypt_enc_get_key_size($module);
        if (strlen($key) > $keySize) {
            $key = substr($key, 0, $keySize);
        }

        //Encrypt value
        mcrypt_generic_init($module, $key, $iv);
        $res = @mcrypt_generic($module, $data);
        mcrypt_generic_deinit($module);

        return $res;
    }

    /**
     * [encryptEx description]
     * @param  [type] $data   [description]
     * @param  [type] $secret [description]
     * @return [type]         [description]
     */
    public function encryptEx($data, $secret)
    {
        $key = md5(utf8_encode($secret), true);
        $key .= substr($key, 0, 8);
        $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
        $len       = strlen($data);
        $pad       = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);
        $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');

        return base64_encode($encData);
    }
}
