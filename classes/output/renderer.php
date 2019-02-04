<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edwiser Server Monitor renderer.
 *
 * @package   block_edwiser_server_monitor
 * @copyright 2019 WisdmLabs <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

namespace block_edwiser_server_monitor\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/edwiser_server_monitor/classes/output/live_status.php');
require_once($CFG->dirroot . '/blocks/edwiser_server_monitor/classes/output/last_24_hours_usage.php');
require_once($CFG->dirroot . '/blocks/edwiser_server_monitor/classes/output/recommendation.php');
require_once($CFG->dirroot . '/blocks/edwiser_server_monitor/classes/output/contactus.php');

use plugin_renderer_base;
use renderable;

/**
 * Block edwiser_server_monitor renderer.
 */
class renderer extends plugin_renderer_base {

    /**
     * Render live status container
     *
     * @param renderable $livestatus live status renderable object
     *
     * @return string
     */
    public function render_live_status(renderable $livestatus) {
        return $this->render_from_template('block_edwiser_server_monitor/live_status', $livestatus->export_for_template($this));
    }

    /**
     * Render last 24 hours usage
     *
     * @param renderable $last24hoursusage last 24 hours usage renderable object
     *
     * @return string
     */
    public function render_last_24_hours_usage(renderable $last24hoursusage) {
        return $this->render_from_template('block_edwiser_server_monitor/last_24_hours_usage', $last24hoursusage->export_for_template($this));
    }

    /**
     * Render recommendation of plugins
     *
     * @param renderable $recommendation recommendation renderable object
     *
     * @return string
     */
    public function render_recommendation(renderable $recommendation) {
        return $this->render_from_template('block_edwiser_server_monitor/recommendation', $recommendation->export_for_template($this));
    }

    /**
     * Render contactus of plugins
     *
     * @param renderable $contactus contactus renderable object
     *
     * @return string
     */
    public function render_contactus(renderable $contactus) {
        return $this->render_from_template('block_edwiser_server_monitor/contactus', $contactus->export_for_template($this));
    }
}
