<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



/**
 * Application Wrapper
 */

final class BGP_Bootstrap
{
    /**
     * BGP_Application main
     *
     * @param $module
     * @param $page
     * @param $id
     * @param $api_version
     * @return int
     */
    public static function start($module, $page, $id, $api_version = BGP_API_VERSION)
    {
        // Check API version

        if ($api_version != BGP_API_VERSION) {

            // Trigger error when the requested API version
            // is not compatible with the current API version
            // 301 MOVED PERMANENTLY
            return 301;
        }

        // Read HTTP Headers
        $http_headers = array_change_key_case(apache_request_headers(), CASE_UPPER);

        if (!isset($http_headers['CONTENT-TYPE']) ||
            (isset($http_headers['CONTENT-TYPE']) && $http_headers['CONTENT-TYPE'] == "text/html")) {

            // GUI
            $app = new BGP_GUI_Application($module,
                $page,
                $id,
                $api_version,
                "text/html");
        } else {

            // RestAPI
            $app = new BGP_API_Application($module,
                $page,
                $id,
                $api_version,
                $http_headers['CONTENT-TYPE']);
        }

        // Execute
        return $app->execute();
    }
}