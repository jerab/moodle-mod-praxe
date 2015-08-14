<?php
/**
 * Defines the version of newmodule
 *
 * This code fragment is called by moodle_needs_upgrading() and
 * /admin/index.php
 *
 * @package    mod
 * @subpackage praxe
 * @copyright  2012 Tomas Jerabek <t.jerab@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$module->version   = 2015081200;      // The current module version (Date: YYYYMMDDXX)
$module->requires  = 2011120503;	// Requires this Moodle version
$module->cron      = 0;               // Period for cron to check this module (secs)
$module->component = 'mod_praxe';         // To check on upgrade, that module sits in correct place

?>