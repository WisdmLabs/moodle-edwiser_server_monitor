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
 * Local class of edwiser_server_monitor_usage_warning
 *
 * @package   block_edwiser_server_monitor
 * @copyright 2019 WisdmLabs <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Yogesh Shirsath
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/edwiser_server_monitor/lib.php');
/**
 * This class implements services for block_edwiser_server_monitor
 */
class block_edwiser_server_monitor_usage_warning {

    /**
     * Check if server usage is less/more than threshold limit
     *
     * @param stdClass $config  configuration of block
     * @param float    $cpu     cpu usage
     * @param float    $memory  memory usage
     * @param float    $storage storage usage
     */
    public function __construct($config, $cpu, $memory, $storage) {
        $this->config = $config;
        $this->cpu = $cpu;
        $this->memory = $memory;
        $this->storage = $storage;
        $this->lastmail = get_config('block_edwiser_server_monitor', 'lastthresholdmail');
        $this->today = strtotime(date('d-m-Y', time()));
        $this->check_usage_thresholds();
    }

    /**
     * Check wheather current usage need to sent via email
     *
     * @param float  $lastusage    last usage warned to user
     * @param float  $currentusage current usage
     * @param string $threshold    low|high usage
     *
     * @return boolean
     */
    private function warn_usage($lastusage, $currentusage, $threshold) {
        $status = false;
        if ($this->lastmail === false) {
            $status = true;
        } else if ($this->lastmail < $this->today) {
            $status = true;
        } else if ($threshold == 'low') {
            $status = $lastusage === false || $lastusage > $currentusage;
        } else if ($threshold == 'high') {
            $status = $lastusage === false || $lastusage < $currentusage;
        }
        return $status;
    }

    /**
     * Get email content if usage need to be sent
     *
     * @param string $type      cpu|memory|storage
     * @param float  $usage     usage of system
     * @param float  $expected  expected usage value
     * @param string $threshold low|high usage
     *
     * @return [type]
     */
    private function get_usage_threshold_email_content($type, $usage, $expected, $threshold) {
        $lastusage = get_config('block_edwiser_server_monitor', $type.$threshold);
        if ($this->warn_usage($lastusage, $usage, $threshold)) {
            set_config($type.$threshold, $usage, 'block_edwiser_server_monitor');
            return array(
                get_string($type.'usage', 'block_edwiser_server_monitor'),
                get_string($threshold, 'block_edwiser_server_monitor'),
                $expected,
                $usage
            );
        }
        return "";
    }

    /**
     * Check if server usage is less/more than threshold limit
     *
     * @return void
     */
    private function check_usage_thresholds() {
        if ($this->config->enablethreshold != 1) {
            return;
        }
        $thresholds = [];

        if ($this->cpu < $this->config->cpulowerlimit) {
            if ($content = $this->get_usage_threshold_email_content('cpu', $this->cpu, $this->config->cpulowerlimit, 'low')) {
                $thresholds[] = $content;
            }
        }
        if ($this->cpu > $this->config->cpuhigherlimit) {
            if ($content = $this->get_usage_threshold_email_content('cpu', $this->cpu, $this->config->cpuhigherlimit, 'high')) {
                $thresholds[] = $content;
            }
        }
        if ($this->memory < $this->config->memorylowerlimit) {
            if ($content = $this->get_usage_threshold_email_content('memory', $this->memory, $this->config->memorylowerlimit, 'low')) {
                $thresholds[] = $content;
            }
        }
        if ($this->memory > $this->config->memoryhigherlimit) {
            if ($content = $this->get_usage_threshold_email_content('memory', $this->memory, $this->config->memoryhigherlimit, 'high')) {
                $thresholds[] = $content;
            }
        }
        if ($this->storage < $this->config->storagelowerlimit) {
            if ($content = $this->get_usage_threshold_email_content('storage', $this->storage, $this->config->storagelowerlimit, 'low')) {
                $thresholds[] = $content;
            }
        }
        if ($this->storage > $this->config->storagehigherlimit) {
            if ($content = $this->get_usage_threshold_email_content('storage', $this->storage, $this->config->storagehigherlimit, 'high')) {
                $thresholds[] = $content;
            }
        }
        if (count($thresholds) == 0) {
            return;
        }
        set_config('lastthresholdmail', $this->today, 'block_edwiser_server_monitor');
        $data = new stdClass;
        $data->header = array(
            get_string('header-type', 'block_edwiser_server_monitor'),
            get_string('hader-threshold', 'block_edwiser_server_monitor'),
            get_string('header-expected', 'block_edwiser_server_monitor'),
            get_string('header-current', 'block_edwiser_server_monitor')
        );
        $data->warnings = $thresholds;
        global $PAGE, $COURSE;
        $PAGE->set_context(context_system::instance());
        $email = $PAGE->get_renderer('block_edwiser_server_monitor')->render_from_template('block_edwiser_server_monitor/usage_warning_email', $data);
        $admin = get_admin();
        edwiser_server_monitor_send_email($admin, $admin, get_string('usageemailsubject', 'block_edwiser_server_monitor', $COURSE->fullname), $email);
    }
}
