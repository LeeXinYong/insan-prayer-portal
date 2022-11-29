<?php

return [
    // Page Title
    "page_title" => [
        "index" => "Failed Job Log",
    ],

    "table_header" => [
        "job" => "Job",
        "queue" => "Queue",
        "failed_at" => "Failed At",
        "exception" => "Exception",
        "action" => "Action",
        "stack_trace" => "Stack Trace",
        "payload" => "Payload",
    ],

    "button" => [
        "retry" => "Retry",
        "view_more" => "View More",
        "view_less" => "View Less",
        "clear_all" => "Delete All",
        "retry_all" => "Retry All",
    ],

    "message" => [
        "are_you_sure_to_retry_job" => "Are you sure to retry this job?",
        "are_you_sure_to_retry_all_job" => "Are you sure to retry all jobs?",
        "job_pushed_back_onto_queue" => "The failed job [:id] has been pushed back onto the queue!",
        "are_you_sure_to_delete_all_jobs" => "Are you sure to delete all jobs?",
        "all_jobs_successfully_deleted" => "All jobs successfully deleted."
    ],
];
