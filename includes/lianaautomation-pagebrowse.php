<?php
/**
 * LianaAutomation page browse handler
 *
 * PHP Version 7.4
 *
 * @category Components
 * @package  WordPress
 * @author   Liana Technologies <websites@lianatech.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0-standalone.html GPL-3.0-or-later
 * @link     https://www.lianatech.com
 */

/**
 * Page browse tracking function
 *
 * Page browse tracking functionality, requires liana_t cookie
 *
 * @return bool
 */
function Liana_Automation_send()
{
    // liana_t tracking cookie handling
    if (!isset($_COOKIE['liana_t'])) {
        // liana_t cookie not found, unable to track. Bailing out.
        return false;
    }
    $liana_t = $_COOKIE['liana_t'];

    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));

    /**
    * Retrieve Liana Options values (Array of All Options)
    */
    $lianaautomation_options = get_option('lianaautomation_options');

    if (empty($lianaautomation_options)) {
        error_log("lianaautomation_options was empty");
        return false;
    }

    // The user id, integer
    if (empty($lianaautomation_options['lianaautomation_user'])) {
        error_log("lianaautomation_options lianaautomation_user was empty");
        return false;
    }
    $user   = $lianaautomation_options['lianaautomation_user'];

    // Hexadecimal secret string
    if (empty($lianaautomation_options['lianaautomation_key'])) {
        error_log("lianaautomation_options lianaautomation_key was empty");
        return false;
    }
    $secret = $lianaautomation_options['lianaautomation_key'];

    // The base url for our API installation
    if (empty($lianaautomation_options['lianaautomation_url'])) {
        error_log("lianaautomation_options lianaautomation_url was empty");
        return false;
    }
    $url    = $lianaautomation_options['lianaautomation_url'];

    // The realm of our API installation, all caps alphanumeric string
    if (empty($lianaautomation_options['lianaautomation_realm'])) {
        error_log("lianaautomation_options lianaautomation_realm was empty");
        return false;
    }
    $realm  = $lianaautomation_options['lianaautomation_realm'];

    // The channel ID of our automation
    if (empty($lianaautomation_options['lianaautomation_channel'])) {
        error_log("lianaautomation_options lianaautomation_channel was empty");
        return false;
    }
    $channel  = $lianaautomation_options['lianaautomation_channel'];

    /**
    * General variables
    */
    $basePath    = 'rest';             // Base path of the api end points
    $contentType = 'application/json'; // Content will be send as json
    $method      = 'POST';             // Method is always POST

    /**
     * Send a API request to LianaAutomation
     *
     * This function will add the required headers and
     * calculates the signature for the authorization header
     *
     * @param string $path The path of the end point
     * @param array  $data The content body (data) of the request
     *
     * @return mixed
     */
    // Import Data
    $path = 'v1/import';
    $data = array(
                "channel" => $channel,
                "no_duplicates" => false,
                "data" => [
                    [
                        "identity" => [
                            "token" => $liana_t,
                        ],
                        "events" => [
                            [
                                "verb" => "pbr",
                                "items" => [
                                    "url" => $current_url,
                                ],
                            ]
                        ],
                    ],
                ]
            );

    // Encode our body content data
    $data = json_encode($data);
    // Get the current datetime in ISO 8601
    $date = date('c');
    // md5 hash our body content
    $contentMd5 = md5($data);
    // Create our signature
    $signatureContent = implode(
        "\n",
        [
            $method,
            $contentMd5,
            $contentType,
            $date,
            $data,
            "/{$basePath}/{$path}"
        ],
    );
    $signature = hash_hmac('sha256', $signatureContent, $secret);
    // Create the authorization header value
    $auth = "{$realm} {$user}:" . $signature;

    // Create our full stream context with all required headers
    $ctx = stream_context_create(
        [
        'http' => [
            'method' => $method,
            'header' => implode(
                "\r\n",
                [
                "Authorization: {$auth}",
                "Date: {$date}",
                "Content-md5: {$contentMd5}",
                "Content-Type: {$contentType}"
                ]
            ),
            'content' => $data
        ]
        ]
    );

    // Build full path, open a data stream, and decode the json response
    $fullPath = "{$url}/{$basePath}/{$path}";
    $fp = fopen($fullPath, 'rb', false, $ctx);
	// if LianaAutomation API settings is invalid or endpoint is not working properly, bail out
	if(!$fp) {
		return false;
	}
    $response = stream_get_contents($fp);
    $response = json_decode($response, true);
    //if (!empty($response)) {
    //    error_log(print_r($response, true));
    //}
}

add_action('wp_head', 'Liana_Automation_send', 10, 2);
