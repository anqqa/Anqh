<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana process control file, loaded by the front controller.
 *
 * @package    Anqh
 * @author     Antti Qvickström
 * @copyright  (c) 2009 Antti Qvickström
 * @license    http://www.opensource.org/licenses/mit-license.php MIT license
 */

// Kohana benchmarks are prefixed to prevent collisions
define('SYSTEM_BENCHMARK', 'system_benchmark');

// Load benchmarking support
require SYSPATH . 'core/Benchmark' . EXT;

// Start total_execution
Benchmark::start(SYSTEM_BENCHMARK . '_total_execution');

// Start kohana_loading
Benchmark::start(SYSTEM_BENCHMARK . '_kohana_loading');

// Load core files
require SYSPATH . 'core/Event' . EXT;
final class Event extends Event_Core {}

require SYSPATH . 'core/Kohana' . EXT;
require MODPATH . 'anqh/libraries/MY_Kohana' . EXT;

require SYSPATH . 'core/Kohana_Exception' . EXT;
require MODPATH . 'anqh/libraries/MY_Kohana_Exception' . EXT;

require SYSPATH . 'core/Kohana_Config' . EXT;
require SYSPATH . 'libraries/drivers/Config' . EXT;
require SYSPATH . 'libraries/drivers/Config/Array' . EXT;
final class Kohana_Config extends Kohana_Config_Core {}

// Prepare the environment
Kohana::setup();

// End kohana_loading
Benchmark::stop(SYSTEM_BENCHMARK . '_kohana_loading');

// Start system_initialization
Benchmark::start(SYSTEM_BENCHMARK . '_system_initialization');

// Prepare the system
Event::run('system.ready');

// Determine routing
Event::run('system.routing');

// End system_initialization
Benchmark::stop(SYSTEM_BENCHMARK . '_system_initialization');

// Make the magic happen!
Event::run('system.execute');
