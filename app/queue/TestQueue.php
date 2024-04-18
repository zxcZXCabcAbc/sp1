<?php

namespace app\queue;

use think\queue\Job;

class TestQueue
{
    public function fire(Job $job, $data)
    {
        // 执行任务
        dump($data);
        $isJobDone = true;

        if ($isJobDone) {
            // 任务执行成功 删除任务
            $job->delete();
        } else {
            if ($job->attempts() > 3) {
                // 任务重试3次后 删除任务
                $job->delete();
            }
        }
        $job->delete();
    }
}