<?php

return [
    'navigation' => [
        'label' => 'Horizon',
    ],

    'pages' => [
        'dashboard' => [
            'title' => 'Dashboard',
            'navigation_label' => 'Dashboard',
        ],
        'jobs' => [
            'title' => 'Recent Jobs',
            'navigation_label' => 'Recent Jobs',
            'pending' => 'Pending',
            'completed' => 'Completed',
            'silenced' => 'Silenced',
        ],
        'failed_jobs' => [
            'title' => 'Failed Jobs',
            'navigation_label' => 'Failed Jobs',
        ],
        'batches' => [
            'title' => 'Batches',
            'navigation_label' => 'Batches',
        ],
        'monitoring' => [
            'title' => 'Monitoring',
            'navigation_label' => 'Monitoring',
        ],
        'metrics' => [
            'title' => 'Metrics',
            'navigation_label' => 'Metrics',
            'jobs' => 'Jobs',
            'queues' => 'Queues',
        ],
    ],

    'widgets' => [
        'stats' => [
            'jobs_per_minute' => 'Jobs Per Minute',
            'jobs_past_hour' => 'Jobs Past :period',
            'failed_jobs_past' => 'Failed Jobs Past :period',
            'status' => 'Status',
            'total_processes' => 'Total Processes',
            'max_wait_time' => 'Max Wait Time',
            'max_runtime' => 'Max Runtime',
            'max_throughput' => 'Max Throughput',
        ],
        'workload' => [
            'title' => 'Current Workload',
            'queue' => 'Queue',
            'jobs' => 'Jobs',
            'processes' => 'Processes',
            'wait' => 'Wait',
        ],
        'workers' => [
            'title' => 'Workers',
            'supervisor' => 'Supervisor',
            'connection' => 'Connection',
            'queues' => 'Queues',
            'processes' => 'Processes',
            'balancing' => 'Balancing',
        ],
    ],

    'status' => [
        'running' => 'Active',
        'paused' => 'Paused',
        'inactive' => 'Inactive',
    ],

    'actions' => [
        'retry' => 'Retry',
        'retry_all' => 'Retry All',
        'view' => 'View',
        'delete' => 'Delete',
        'start_monitoring' => 'Start Monitoring',
        'stop_monitoring' => 'Stop Monitoring',
    ],

    'columns' => [
        'job' => 'Job',
        'queue' => 'Queue',
        'runtime' => 'Runtime',
        'status' => 'Status',
        'failed_at' => 'Failed At',
        'completed_at' => 'Completed At',
        'reserved_at' => 'Reserved At',
        'attempts' => 'Attempts',
        'tags' => 'Tags',
        'tag' => 'Tag',
        'count' => 'Count',
        'name' => 'Name',
        'throughput' => 'Throughput',
        'progress' => 'Progress',
        'created_at' => 'Created At',
        'pending_jobs' => 'Pending Jobs',
        'failed_jobs' => 'Failed Jobs',
    ],

    'messages' => [
        'no_jobs' => 'No jobs found.',
        'no_failed_jobs' => 'There aren\'t any failed jobs.',
        'no_batches' => 'There aren\'t any batches.',
        'no_monitored_tags' => 'No tags are being monitored.',
        'no_metrics' => 'No metrics available.',
        'job_retried' => 'Job has been queued for retry.',
        'batch_retried' => 'Batch has been queued for retry.',
        'tag_monitoring_started' => 'Tag monitoring has been started.',
        'tag_monitoring_stopped' => 'Tag monitoring has been stopped.',
    ],
];
