<?php

namespace Bootphp\Request\Client;

use Bootphp\BootphpException;

/**
 * [Request_Client_External] Curl driver performs external requests using the
 * php-curl extention. This is the default driver for all external requests.
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 * @uses       [PHP cURL](http://php.net/manual/en/book.curl.php)
 */
class Curl extends External
{
    /**
     * Sends the HTTP message [Request] to a remote server and processes the response.
     *
     * @param   Request     $request    Request to send
     * @param   Response    $request    Response to send
     * @return  Response
     */
    public function _send_message(Request $request, Response $response)
    {
        // Response headers
        $response_headers = [];

        $options = [];

        // Set the request method
        switch ($request->method()) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = $request->method();
                break;
        }

        // Set the request body. This is perfectly legal in CURL even if using a
        // request other than POST. PUT does support this method and DOES NOT
        // require writing data to disk before putting it, if reading the PHP
        // docs you may have got that impression.
        // This will also add a Content-Type: application/x-www-form-urlencoded
        // header unless you override it.
        if ($body = $request->body()) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        // Process headers
        if ($headers = $request->headers()) {
            $http_headers = [];

            foreach ($headers as $key => $value) {
                $http_headers[] = $key . ': ' . $value;
            }

            $options[CURLOPT_HTTPHEADER] = $http_headers;
        }

        // Process cookies
        if ($cookies = $request->cookie()) {
            $options[CURLOPT_COOKIE] = http_build_query($cookies, null, '; ');
        }

        // Get any exisiting response headers
        $response_header = $response->headers();

        // Implement the standard parsing parameters
        $options[CURLOPT_HEADERFUNCTION] = [$response_header, 'parse_header_string'];
        $this->_options[CURLOPT_RETURNTRANSFER] = true;
        $this->_options[CURLOPT_HEADER] = false;

        // Apply any additional options set to
        $options += $this->_options;

        $uri = $request->uri();

        if ($query = $request->query()) {
            $uri .= '?' . http_build_query($query, null, '&');
        }

        // Open a new remote connection
        $curl = curl_init($uri);

        // Set connection options
        if (!curl_setopt_array($curl, $options)) {
            throw new BootphpException('Failed to set CURL options, check CURL documentation: http://php.net/curl_setopt_array');
        }

        // Get the response body
        $body = curl_exec($curl);

        // Get the response information
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($body === false) {
            $error = curl_error($curl);
        }

        // Close the connection
        curl_close($curl);

        if (isset($error)) {
            throw new BootphpException('Error fetching remote ' . $request->url() . ' [ status ' . $code . ' ] ' . $error);
        }

        $response->status($code)
            ->body($body);

        return $response;
    }

}
