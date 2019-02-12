Abstract Integration
=====================

[wiki link](https://wiki.ewave.com/display/LEGO/Abstract+Integration)

###Description

Extension can be used as a basis of Integration Engine, Lib function, Integrations logger function, Integration exceptions function.
Every integration process should be created based on AI extension and follow architecture of this extension. 

###VERSION 1.0.0
1. Engine function.
2. Lib Function.
3. Exception function
4. Log Function 
5. Run Queue

###VERSION 1.1.0
1. Ability to enable/disable processes

###VERSION 1.2.0
1. Product Import Library
2. Category Import Library

###VERSION 1.2.1
1. Valitron Validator. Ability to use an optional argument for array validation

###VERSION 1.3.0
1. Abstract Integration. Mapper

###VERSION 1.4.0
1. Abstract Integration. Customer Library

###VERSION 1.5.0
1. As an admin I want to set up 'cron' running time for each integration

### VERSION 1.6.0 
1. Added stock import as separate process
2. Gift Card Account import

### VERSION 1.7.0
1. Reward Points Library

### VERSION 1.8.0
1. Compatibility with Magento 2.2
2. Email Logger added

### VERSION 1.9.0
1. Catalog Price Rules Library

### VERSION 1.10.0
1. Extend Enrichment File Format
Bootstrap::INIT_PARAM_FILESYSTEM_DRIVERS – file system driver. It should be 
['file' => '\Ewave\AI\Framework\Filesystem\Driver\File'].
The parameter you can specify right before loader creating or in .htaccess file by setEnv.

### VERSION 1.11.0
1. Removed old connector classes and interface.
2. Added new mapper class Ewave\AI\Model\Lib\Mapping\MapperTemplateFilter. [see US](https://ewave.tpondemand.com/entity/179593)
3. Added new Curl Http Client and Rest Api client. 

### VERSION 1.12.0
1. Stock sku selection
2.  Added date time to log files

### VERSION 1.13.0
1. Engine, Queue and Schedule refactoring, bugfixes.
2. Mapper template filter: improvements, bugfixes.
3. Stuck Order (for inventory imports): table, interface, classes. 

### VERSION 1.13.1
1. ProcessorAbstract: removed "implements ProcessorInterface" because of old modules

### VERSION 1.13.2
1. Logger type File: changed file name format.

### VERSION 1.13.3
1. Processor Abstract now has properties to detect how process was executed: $isCronRun, $isQueueRun, $isScheduleRun
2. Bugfixes. See changelog.

### VERSION 1.13.4
1. Logging changes. See changelog.
2. Bugfixes. See changelog.

### VERSION 1.13.5
1. ewave_integrations.xml validation: chain_proccessor_code, can_admin_run, cron_time, state now are not required.
2. Scheduled run and schedule refactoring.
3. Removed run option 'scheduled' for scheduled run.
4. Processor Abstract now has property $isAdminRun.
5. Run process from shell command: added 'hack' to avoid error 'Area code is not set' - set AREA_GLOBAL if not set.
6. Bugfixes. See changelog.

### VERSION 1.13.6
1. All settings were declared as global and now we get them using default scope (not website).
2. Cronjobs `ai_run_by_schedule` and `ai_run_queue` were moved to group `ewave_ai`

### VERSION 1.13.7
1. Logger. Type Db. Speed-Up.

### VERSION 1.13.8
1. Logger. Type Db. Added cron job for removing old entries. There is a setting in admin area. You can set X days. Or empty for disable cleanup.
2. Logger. Changed details "wrapper". Now it is a little shorter.
3. Logger. Type Db. Speed-Up.
4. Product Import. Fixed bug for product category position overriding to 1.

### VERSION 1.13.9
Logger. Type file. Backup. Now files are stored in YYYY-MM-DD directory. It simplifies (speed ups) files search and directories scan.

### VERSION 1.14.0
1. Logger. Removed column 'id' from table 'ewave_ai_logs_details'.
2. Logger. Column 'date' was renamed to 'create_at'.
3. Logger. added column 'updated_at'.
4. Logger. added column identifying_params to log entity to store process identifiers for search in grid.
5. Logger. Comment includes an error text from now (Engine adds it). Also it output uses multiple rows (nl2br).
6. Logger. Admin area grid. Watch Details -> See Details.

### VERSION 1.14.1
Logger. Type file. Backup. Now archive .tar.gz is created instead of .tar.

### VERSION 1.14.2
1. Bugfix. Do not create duplicate queue items.
2. Bugfix. Integration Engine catches Fatal errors too

### VERSION 1.14.3
Bugfix. Changed column id type from smallint to int.

### VERSION 1.15.0
1. Queue fail email: added settings for emails list and email template. added run options to the email.
2. Log email & Queue email. fixed bugs.

### VERSION 1.15.1
1. admin run. fixed bug. if process was in processing and it has "miltiple_run" = 0, we have seen "View State" instead of "Run" button. So, admin could not execute process while other processes with the same process code are in running.
2. mapper. mapper template filter. speed up for case when we use too much times the same mapper - multiple entities.
3. queue. cron. select from database: small speed-up.
4. queue. default interval between re-tries now is 900 sec (15min) instead of 5 sec.
5. queue. processing. added support for QueueDependsException.
6. queue. ewave_ai_queue_log table.removed column entity_id. added primary key for "log_id". removed unq idx for log_id + queue_id.
7. logs. grid. perfomance fix. do not join to queue_log table if there is no filter by queue_id.
8. logs. logging. fixed root case of infinite recursion in some cases of logging usage.
9. logs. logging. error handling. log full error text instead of stacktrace only ($e->__toString() instead of $e->getTraceAsString())

### VERSION 1.15.2
=============
1. queue. added setting `Generate Run On Error Every (for Pending Depends)`. for queue depends default interval is 900s, for other cases default interval from now is 300s.
2. queue. added setting `Number of Runs On Error (Per Integration Process)`. it will override setting `Number of Runs On Error` if specified for process.
3. queue. added method getActiveQueueItemIdByProcessCodeAndProcessData.