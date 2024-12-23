<?php
namespace App\Handler\Helper;

class IpRange
{
    public function filter($ip_list, $ip = null)
    {
        if ( ! $ip) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $long_ip = ip2long($ip);
        foreach ($ip_list as $filter_ip) {
            $range = explode('-', $filter_ip);
            if ( ! $range[1]) // single address type
            {
                $star_pos = strpos($filter_ip, '*');
                if ($star_pos !== false) // wild card exist
                {
                    if (strncmp($filter_ip, $ip, $star_pos) === 0) {
                        return true;
                    }

                } elseif (strcmp($filter_ip, $ip) === 0) {
                    return true;
                }
            } elseif (ip2long($range[0]) <= $long_ip && ip2long($range[1]) >= $long_ip) {
                return true;
            }
        }
        return false;
    }

    public function netMatch($network, $ip)
    {
        // https://github.com/mlocati/ip-lib
        $network = trim($network);
        $ip      = trim($ip);
        $d       = strpos($network, '-');

        if (preg_match("/^\*$/", $network)) {
            $network = str_replace('*', '^.+', $network);
        }
        if ( ! preg_match("/\^\.\+|\.\*/", $network)) {
            if ($d === false) {
                $ip_arr = explode('/', $network);

                if ( ! preg_match("/@\d*\.\d*\.\d*\.\d*@/", $ip_arr[0], $matches)) {
                    $ip_arr[0] .= '.0'; // Alternate form 194.1.4/24

                }

                $network_long = ip2long($ip_arr[0]);
                $x            = ip2long($ip_arr[1]);
                $mask         = long2ip($x) === $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
                $ip_long      = ip2long($ip);

                return ($ip_long & $mask) === ($network_long & $mask);
            } else {
                $from = ip2long(trim(substr($network, 0, $d)));
                $to   = ip2long(trim(substr($network, $d + 1)));
                $ip   = ip2long($ip);

                return ($ip >= $from && $ip <= $to);
            }
        } else {
            return preg_match("/$network/", $ip);
        }
    }

    public function validate($ip_list = [])
    {
        /* 사용가능한 표현
        192.168.2.10 - 4자리의 정확한 ip주소
        192.168.*.* - 와일드카드(*)가 사용된 4자리의 ip주소, a클래스에는 와일드카드 사용불가,
        와일드카드 이후의 아이피주소 허용(단, filter()를 쓸 경우 와일드카드 이후 주소는 무시됨
        192.168.1.1-192.168.1.10 - '-'로 구분된 정확한 4자리의 ip주소 2개
         */
        $regex = "/^
                (?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)
                (?:
                    (?:
                        (?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}
                        (?:-(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){1}
                        (?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}
                    )
                    |
                    (?:
                        (?:\.(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)|\*)){3}
                    )
                )
            $/";
        $regex = str_replace(["\r\n", "\n", "\r", "\t", " "], '', $regex);

        foreach ($ip_list as $i => $ip) {
            preg_match($regex, $ip, $matches);
            if ( ! count($matches)) {
                return false;
            }

        }

        return true;
    }
}
