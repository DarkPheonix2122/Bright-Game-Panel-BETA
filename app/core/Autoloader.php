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
 * Class Autoloader
 * Loads components on runtime
 */
class Autoloader
{
    /**
     * Load minimal requirements for the framework
     *
     * @return void
     */
    public static function load() {

        // BrightGamePanel Functions
        require( CORE_DIR	. '/inc/func.inc.php');

        // Defaults
        require( CORE_DIR	. '/defaults/Core_Defaults.php' );

        // Applications
        require( CORE_DIR	. '/application/Core_Application_Interface.php' );
        require( CORE_DIR	. '/application/Core_Abstract_Application.php' );

        // Services
        require( CORE_DIR   . '/services/Core_Service_Interface.php' );

        // Authentication
        require( CORE_DIR	. '/services/auth/Core_Auth_Service_Interface.php' );
        require( CORE_DIR	. '/services/auth/Core_Abstract_Auth_Service.php' );

        // Database Handler
        require( CORE_DIR	. '/services/database/Core_Database_Service.php' );

        // Base Module Classes
        require( CORE_DIR   . '/module/Core_Module_Shared_Interface.php');
        require( CORE_DIR   . '/module/Core_Module_Interface.php');
        require( CORE_DIR   . '/module/Core_Controller_Interface.php');
        require( CORE_DIR	. '/module/Core_Abstract_Module.php' );
        require( CORE_DIR	. '/module/Core_Abstract_Controller.php' );

        spl_autoload_register('Autoloader::loader');
    }

    /**
     * Runtime dependency resolution and injection
     *
     * @param string $class Class name to load
     * @return void
     */
    public static function loader($class)
    {
        // CORE

        if (strpos($class, 'Core_') === 0) {

            switch ($class) {
                // Application Package
                case 'Core_Wizard_Application':
                case 'Core_API_Application':
                case 'Core_GUI_Application':
                    require( CORE_DIR	. '/application/' . $class . '.php' );
                    return;
                // Authentication Package
                case 'Core_Auth_Service_Anonymous':
                case 'Core_Auth_Service_JWT':
                case 'Core_Auth_Service_API':
                case 'Core_Auth_Service_Session':
                    require( CORE_DIR	. '/services/auth/' . $class . '.php' );
                    return;
                // GUI
                case 'Core_Page_Builder':
                case 'Core_Javascript_Builder':
                    require( CORE_DIR	. '/gui/' . $class . '.php' );
                    return;
                // Language Package
                case 'Core_Language_Service':
                    require( CORE_DIR 	. '/services/language/' . $class . '.php' );
                    require( LIBS_DIR	. '/php-gettext/gettext.inc.php' );
                    return;
                // Module
                case 'Core_Page_Interface':
                case 'Core_Abstract_Page':
                    require( CORE_DIR   . '/module/' . $class . '.php');
                    return;
                default:
                    // Unknown injection
                    return;
            }
        }

        // LIBS

        switch ($class) {
            case 'DocBlock':
                require( LIBS_DIR	. '/docblockparser/doc_block.php' );
                return;
            case 'Flight':
                require( LIBS_DIR	. '/flight/Flight.php' );
                return;
            case 'JWT':
            case 'BeforeValidException':
            case 'ExpiredException':
            case 'SignatureInvalidException':
                require( LIBS_DIR   . '/jwt/' . $class . '.php');
                return;
            case 'Logger':
                require( LIBS_DIR	. '/log4php/Logger.php' );
                return;
            case 'PhpRbac\Rbac':
                require( LIBS_DIR	. '/phprbac2.0/autoload.php' );
                return;
            case 'Crypt_AES':
                require( LIBS_DIR	. '/phpseclib/AES.php' );
                return;
            case 'Crypt_RSA':
                require( LIBS_DIR	. '/phpseclib/RSA.php' );
                return;
            case 'File_ANSI':
                require( LIBS_DIR	. '/phpseclib/ANSI.php' );
                return;
            case 'Securimage':
                require( LIBS_DIR	. '/securimage/securimage.php' );
                return;
            case 'Validator':
                require( LIBS_DIR	. '/valitron/Validator.php' );
                return;
        }

        // MODULE PAGE
        if (preg_match('/([a-zA-Z0-9]*)_([a-zA-Z0-9]*)_([a-zA-Z0-9]*)/', strtolower($class), $matches) === 1) {

            list($class_tolower, $module, $page, $suffix) = $matches;
            $page_file = MODS_DIR . '/' . $module . '/' . $module . '.' . $page .  '.page.class.php';
            if (file_exists($page_file)) {
                require( $page_file );
                return;
            }
        }

        // MODULE

        $module = strtolower($class);
        $module_class_file = MODS_DIR . '/' . $module . '/' . $class . '.class.php';
        $controller_class_file = MODS_DIR . '/' . $module . '/' . $class . '.controller.class.php';
        $default_page_class_file = MODS_DIR . '/' . $module . '/' . $module . '.page.class.php';

        if (file_exists($module_class_file) && file_exists($controller_class_file)) {

            require( $controller_class_file );
            require( $module_class_file );

            if (file_exists($default_page_class_file)) {
                // Default page
                require( $default_page_class_file );
            }
            return;
        }
    }
}
