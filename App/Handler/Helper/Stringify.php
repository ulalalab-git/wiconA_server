<?php
namespace App\Handler\Helper;

/**
 *
 */
class Stringify
{
    /**
     * [cleanFileName description]
     * @param  string|null $filename [description]
     * @return [type]                [description]
     */
    public function cleanFileName(string $filename = null):  ? string
    {
        return preg_replace("/\.(php|pht|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);
    }

    /**
     * [cleanImageCheck description]
     * @param  string|null $filename [description]
     * @return [type]                [description]
     */
    public function cleanImageCheck(string $filename = null) :  ? bool
    {
        $valid = ['gif', 'jpg', 'jpeg', 'png'];
        // ! preg_match("/\.({gif|jpg|jpeg|png})$/i", $filename);
        $info = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($info, $valid);
    }

    /**
     * [cleanMakeFileName description]
     * @param  string|null $filename [description]
     * @return [type]                [description]
     */
    public function cleanMakeFileName(string $filename = null) :  ? string
    {
        list($usec, $sec) = explode(" ", microtime());
        $usec             = ((float) $usec + (float) $sec);

        $file_path = pathinfo($filename);
        $ext       = $file_path['extension'];
        $replace   = sha1($_SERVER['REMOTE_ADDR'] . $usec);
        if (empty($ext) === false) {
            $replace .= '.' . $ext;
        }
        // @mkdir('/file/'.$name, 0666);
        // @chmod('/file/'.$name, 0666);
        return abs(ip2long($_SERVER['REMOTE_ADDR'])) . '_' . substr($this->shuffle(), 0, 8) . '_' . $replace;
    }

    /**
     * [cleanSafefilename description]
     * @param  string|null $filename [description]
     * @return [type]                [description]
     */
    public function cleanSafefilename(string $filename = null) :  ? string
    {
        $pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
        return preg_replace($pattern, '', $filename);
    }

    /**
     * [cleanXssTags description]
     * @param  string|null $string [description]
     * @return [type]              [description]
     */
    public function cleanXssTags(string $string = null) :  ? string
    {
        return preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $string);
    }

    /**
     * [clearString description]
     * @param  string|null $value [description]
     * @return [type]             [description]
     */
    public function clearString(string $value = null) :  ? string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true);
    }

    /**
     * [create description]
     * @param  string|null $data [description]
     * @param  string      $salt [description]
     * @param  string      $algo [description]
     * @return [type]            [description]
     */
    public function create(string $data = null, string $salt = 'cipher.secret_key', string $algo = 'sha1') :  ? string
    {
        // md5, sha1 - http://php.net/manual/en/function.hash-algos.php
        if (empty($data) === true) {
            return false;
        }

        $context = hash_init($algo, HASH_HMAC, $salt);

        hash_update($context, $data);
        return hash_final($context);
    }

    /**
     * [currentUrl description]
     * @return [type] [description]
     */
    public function currentUrl()
    {
        return preg_replace("`\/[^/]*\.php$`i", "", $_SERVER['PHP_SELF']) . $_SERVER['REQUEST_URI'];
    }

    /**
     * [hashCode description]
     * @param  string|null $s [description]
     * @return [type]         [description]
     */
    public function hashCode(string $s = null) :  ? string
    {
        $h   = 0;
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $h = $this->overflow32(31 * $h + ord($s[$i]));
        }

        return $h;
    }

    /**
     * [hexToStr description]
     * @param  string|null $hex [description]
     * @return [type]           [description]
     */
    public function hexToStr(string $hex = null) : string
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            if ($hex[$i] === ' ') {
                continue;
            }

            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    /**
     * [msgPack description]
     * @param  string $data [description]
     * @param  string $key1 [description]
     * @param  string $key2 [description]
     * @return [type]       [description]
     */
    public function msgPack($data = '', $key1 = '', $key2 = '')
    {
        $data = json_encode($data);

        $data = strrev($data);
        $data = strtr($data, $key1, $key2);

        $data = gzdeflate($data);

        return $data;
    }

    /**
     * [msgUnpack description]
     * @param  string $data [description]
     * @param  string $key1 [description]
     * @param  string $key2 [description]
     * @return [type]       [description]
     */
    public function msgUnpack($data = '', $key1 = '', $key2 = '')
    {
        $data = gzinflate($data);

        $data = strtr($data, $key2, $key1);
        $data = strrev($data);

        $data = json_decode($data, true);

        return $data;
    }

    /**
     * [overflow32 description]
     * @param  int|integer $v [description]
     * @return [type]         [description]
     */
    public function overflow32(int $v = 0): int
    {
        $v = $v % 4294967296;
        if ($v > 2147483647) {
            return $v - 4294967296;
        } elseif ($v < -2147483648) {
            return $v + 4294967296;
        }

        return $v;
    }

    /**
     * [password description]
     * @param  string|null $passwd [description]
     * @return [type]              [description]
     */
    public function password(string $passwd = null): string
    {
        return password_hash($passwd, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * [rands description]
     * @param  int|integer $len [description]
     * @return [type]           [description]
     */
    public function rands(int $len = 4): string
    {
        $len = abs(intval($len));
        $len = min(10, max($len, 1));

        return rand(pow(10, $len - 1), (pow(10, $len) - 1));
    }

    /**
     * [removeStrip description]
     * @param  string|null $value [description]
     * @return [type]             [description]
     */
    public function removeStrip(string $value = null):  ? string
    {
        $search = [
            '@<script[^>]*?>.*?</script>@si',                                      // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si',                                               // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU',                                       // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@',                                           // Strip multi-line comments including CDATA
            '@(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))@', // Strip cout Comment
            '@<!--(.|\s)*?-->@',                                                   // Strip cout Comment
        ];

        return preg_replace($search, '', $value);
    }

    /**
     * [strToHex description]
     * @param  string|null $string [description]
     * @return [type]              [description]
     */
    public function strToHex(string $string = null) : string
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    /**
     * [stringHashCode description]
     * @param  string|null $string [description]
     * @return [type]              [description]
     */
    public function stringHashCode(string $string = null):  ? string
    {
        /*
        $hash = 0;
        $len  = strlen($string);
        if ($len === 0) {
        return $hash;
        }

        for ($i = 0; $i < $len; $i++) {
        $h = $hash << 5;
        $h -= $hash;
        $h += ord($string[$i]);
        $hash = $h;
        $hash &= 0xFFFFFFFF;
        }
        return $hash;
         */
        $n = 0;
        for ($c = 0; $c < strlen($string); $c++) {
            $n += ord($string[$c]);
        }
        return $n;
    }

    /**
     * [shuffle description]
     * @param  int|integer  $limit   [description]
     * @param  int|integer  $split   [description]
     * @param  bool|boolean $isUpper [description]
     * @return [type]                [description]
     */
    public function shuffle(int $limit = 20, int $split = 0, bool $isUpper = false) : string
    {
        $shuffle = implode('', array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9)));
        $shuffle = str_shuffle($shuffle);
        if ($split > 0) {
            $shuffle = implode('-', str_split($shuffle, $split));
        }
        $shuffle = substr($shuffle, 0, $limit);
        if ($isUpper === true) {
            $shuffle = strtoupper($shuffle);
        }
        return $shuffle;
    }

    /**
     * [typeToString description]
     * @param  string|null $value [description]
     * @return [type]             [description]
     */
    public function typeToString(string $value = null): string
    {
        if (is_object($value) === true) {
            return strtolower(get_class($value));
        }

        return strtolower(gettype($value));
    }
}
