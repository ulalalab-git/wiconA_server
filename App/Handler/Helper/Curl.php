<?php
namespace App\Handler\Helper;

/**
 * Parses the response from a Curl request into an object containing
 * the response body and an associative array of headers
 *
 * @package curl
 * @author Sean Huber <shuber@huberry.com>
 *
 * @author dearl
 * @link https://pear.php.net/manual/en/standards.header.php
 **/
class Curl
{
    public $body = '';

    /**
     * The file to read and write cookies to for requests
     *
     * @var string
     **/
    public $cookie_file;

    /**
     * Determines whether or not requests should follow redirects
     *
     * @var boolean
     **/
    public $follow_redirects = true;

    public $headers = [];

    /**
     * An associative array of CURLOPT options to send along with requests
     *
     * @var array
     **/
    public $options = [];

    /**
     * The referer header to send along with requests
     *
     * @var string
     **/
    public $referer;

    /**
     * The user agent to send along with requests
     *
     * @var string
     **/
    public $user_agent;

    /**
     * Stores an error string for the last request if one occurred
     *
     * @var string
     * @access protected
     **/
    protected $error = '';

    /**
     * Stores resource handle for the current CURL request
     *
     * @var resource
     * @access protected
     **/
    protected $request;

    /**
     * Initializes a Curl object
     *
     * Sets the $cookie_file to "curl_cookie.txt" in the current directory
     * Also sets the $user_agent to $_SERVER['HTTP_USER_AGENT'] if it exists, 'Curl/PHP '.PHP_VERSION.' (http://github.com/shuber/curl)' otherwise
     **/
    public function __construct()
    {
        // $this->cookie_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'curl_cookie.txt';
        $this->user_agent  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Curl/PHP ' . PHP_VERSION . ' (http://github.com/shuber/curl)';
    }

    /**
     * Makes an HTTP DELETE request to the specified $url with an optional array or string of $vars
     *
     * Returns a CurlResponse object if the request was successful, false otherwise
     *
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse object
     **/
    public function delete($url, $vars = [])
    {
        return $this->request('DELETE', $url, $vars);
    }

    /**
     * Returns the error string of the current request if one occurred
     *
     * @return string
     **/
    public function error()
    {
        return $this->error;
    }

    /**
     * Makes an HTTP GET request to the specified $url with an optional array or string of $vars
     *
     * Returns a CurlResponse object if the request was successful, false otherwise
     *
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse
     **/
    public function get($url, $vars = [])
    {
        if ( ! empty($vars)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
        }
        return $this->request('GET', $url);
    }

    /**
     * Makes an HTTP HEAD request to the specified $url with an optional array or string of $vars
     *
     * Returns a CurlResponse object if the request was successful, false otherwise
     *
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse
     **/
    public function head($url, $vars = [])
    {
        return $this->request('HEAD', $url, $vars);
    }

    /**
     * Makes an HTTP POST request to the specified $url with an optional array or string of $vars
     *
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse|boolean
     **/
    public function post($url, $vars = [])
    {
        return $this->request('POST', $url, $vars);
    }

    /**
     * Makes an HTTP PUT request to the specified $url with an optional array or string of $vars
     *
     * Returns a CurlResponse object if the request was successful, false otherwise
     *
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse|boolean
     **/
    public function put($url, $vars = [])
    {
        return $this->request('PUT', $url, $vars);
    }

    /**
     * Makes an HTTP request of the specified $method to a $url with an optional array or string of $vars
     *
     * Returns a CurlResponse object if the request was successful, false otherwise
     *
     * @param string $method
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse|boolean
     **/
    public function request($method, $url, $vars = [])
    {
        $this->error   = '';
        $this->request = curl_init();
        if (is_array($vars)) {
            $vars = http_build_query($vars, '', '&');
        }

        $this->set_request_method($method);
        $this->set_request_options($url, $vars);
        $this->set_request_headers();

        $response = curl_exec($this->request);

        if ($response) {
            $response = $this->parser($response);
        } else {
            $this->error = curl_errno($this->request) . ' - ' . curl_error($this->request);
        }

        curl_close($this->request);

        return $response;
    }

    /**
     * [parser description]
     * @param  string|null $response [description]
     * @return [type]                [description]
     */
    protected function parser(string $response = null)
    {
        # Headers regex
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

        # Extract headers from response
        preg_match_all($pattern, $response, $matches);
        $headers_string = array_pop($matches[0]);
        $headers        = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

        # Remove headers from the response body
        $this->body = str_replace($headers_string, '', $response);

        # Extract the version and status from the first header
        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->headers['Http-Version'] = $matches[1];
        $this->headers['Status-Code']  = $matches[2];
        $this->headers['Status']       = $matches[2] . ' ' . $matches[3];

        # Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
    }

    /**
     * Formats and adds custom headers to the current request
     *
     * @return void
     * @access protected
     **/
    protected function set_request_headers()
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($this->request, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Set the associated CURL options for a request method
     *
     * @param string $method
     * @return void
     * @access protected
     **/
    protected function set_request_method($method)
    {
        switch (strtoupper($method)) {
            case 'HEAD':
                curl_setopt($this->request, CURLOPT_NOBODY, true);
                break;
            case 'GET':
                curl_setopt($this->request, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($this->request, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    /**
     * Sets the CURLOPT options for the current request
     *
     * @param string $url
     * @param string $vars
     * @return void
     * @access protected
     **/
    protected function set_request_options($url, $vars)
    {
        curl_setopt($this->request, CURLOPT_URL, $url);
        if ( ! empty($vars)) {
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $vars);
        }

        # Set some default CURL options
        curl_setopt($this->request, CURLOPT_HEADER, true);
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->request, CURLOPT_USERAGENT, $this->user_agent);
        /*
        if ($this->cookie_file) {
            curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie_file);
            curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie_file);
        }
        */
        if ($this->follow_redirects) {
            curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);
        }

        if ($this->referer) {
            curl_setopt($this->request, CURLOPT_REFERER, $this->referer);
        }
        curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, false);

        # Set any custom CURL options
        foreach ($this->options as $option => $value) {
            curl_setopt($this->request, constant('CURLOPT_' . str_replace('CURLOPT_', '', strtoupper($option))), $value);
        }
    }
}
