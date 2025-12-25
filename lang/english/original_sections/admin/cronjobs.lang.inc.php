<?php
/* --------------------------------------------------------------
	cronjobs.lang.inc.php 2018-09-10
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2018 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_language_text_section_content_array = [
    'cronjob_setup_description'       => 'Create a new cronjob at your hosting provider with this cronjob URL. For information on if and how to create cronjobs please refer to your hosting provider. Configure your new cronjob with a time interval of 1 minute. By calling the cronjob URL every minute, all active scheduled tasks are checked against their configured time interval and eventually will be executed.',
    'copy_to_clipboard'               => 'Copy to clipboard',
    'copy_success'                    => 'URL copied to clipboard successfully!',
    'last_executed'                   => 'Last executed',
    'log_modal_last_messages'         => 'Last Messages',
    'miscellaneous_cronjob_error'     => 'Background tasks will not be executed.',
    'no_jobs'                         => 'No scheduled tasks found',
    'save_configuration_error_title'  => 'Configuration could not be saved',
    'save_configuration_error_text'   => 'Unfortunately the configuration could not be saved properly',
    'status_inactive'                 => 'inactive',
    'status_inactive_description'     => 'This task is currently not active',
    'status_active'                   => 'active',
    'status_active_description'       => 'This task is executed properly',
    'status_warning_description'      => 'This task could not be executed last time. Check the logs for more details',
    'status_error_description'        => 'This task does not work properly. Please look in the logs for more details on possible errors',
    'status_not_executed_description' => 'This task has not been executed yet',
    'sub_title_miscellaneous'         => 'Miscellaneous',
    'sub_title_tasks'                 => 'Tasks',
    'title'                           => 'Scheduled Tasks',
    'title_configuration_modal'       => 'Configure scheduled task',
    'title_log_modal'                 => 'Task-Log:',
    'error_log_not_found'             => 'Log files for cronjob task %s not found',
    'unexpected_error'                => 'An unexpected error occurred',
];