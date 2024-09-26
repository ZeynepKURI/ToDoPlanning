<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Developer extends Model
{
    protected $table = "developer";

    protected $fillable = [
        'name',
        'level'
    ];

    public static function getPlan(array $developers = [])
    {
        $developerTasks = self::assignTaskToDeveloper($developers);
        foreach ($developerTasks as $key => $developer) {
            $developerTasks[$key]['weekly'] = self::weeklyGroup($developer['tasks']); 
        }
        return $developerTasks;
    }

    private static function assignTaskToDeveloper(array $developerList = [])
    { 
        $tasks = Task::orderBy('duration', 'desc')->get();
        if ($tasks->isEmpty()) {
            return "No tasks available"; // Eğer görev yoksa
        }

        $taskGroup = [];
        foreach ($tasks as $task) {
            $taskGroup[$task->level][] = ['id' => $task->id, 'name' => $task->name, 'duration' => $task->duration];
        }
        krsort($taskGroup);

        $developers = [];
        foreach ($developerList as $developer) {
            $developers[$developer['level']] = ['name' => $developer['name'], 'level' => $developer['level'], 'duration' => 0];
        }

        foreach ($taskGroup as $level => $tasks) {
            foreach ($tasks as $task) {
                if (!isset($developers[$level])) {
                    return "backlog"; // Yeterli geliştirici yoksa
                }
                $findingDeveloperLevel = self::findDeveloperLevel($developers, $level);
                $developers[$findingDeveloperLevel]['tasks'][] = array_merge($task, ['level' => $level]);
                $developers[$findingDeveloperLevel]['duration'] += $task['duration'];
            }
        }
        return $developers;
    }

    private static function findDeveloperLevel(array $developers, int $level)
    {
        $developer = $developers[$level] ?? null;
        if (!$developer) {
            return "No developer found at this level"; // Eğer geliştirici yoksa
        }

        ksort($developers);
        $index = array_search($level, array_keys($developers));
        $upperLevelDeveloper = array_slice($developers, $index + 1, 1, true);

        if (!isset($upperLevelDeveloper[$level + 1]['duration'])) {
            return $level;
        } elseif ($developer['duration'] <= $upperLevelDeveloper[$level + 1]['duration']) {
            return $level;
        } else {
            return self::findDeveloperLevel($developers, $level + 1);
        }
    }

    private static function weeklyGroup(array $tasks = [])
    {
        $weeklyTasks = [
            ['tasks' => [], 'duration' => 0],
        ];

        foreach ($tasks as $task) {
            $taskTime = $task['duration'];
            foreach ($weeklyTasks as $key => $week) {
                if ($week['duration'] == 45 && isset($weeklyTasks[$key + 1])) {
                    continue;
                }

                if ($week['duration'] < 45) {
                    if (($week['duration'] + $taskTime) > 45) {
                        $duration = 45 - $week['duration'];
                        $taskTime -= $duration;
                        $task['duration'] = $duration;
                        $weeklyTasks[$key]['tasks'][] = $task;
                        $weeklyTasks[$key]['duration'] += $duration;
                        $weeklyTasks[] = ['tasks' => [$task], 'duration' => $taskTime];
                        break;
                    } else {
                        $weeklyTasks[$key]['tasks'][] = $task;
                        $weeklyTasks[$key]['duration'] += $taskTime;
                        break;
                    }
                } else {
                    $weeklyTasks[] = ['tasks' => [$task], 'duration' => $taskTime];
                    break;
                }
            }
        }
        return $weeklyTasks;
    }
}
