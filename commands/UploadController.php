<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\File;
use app\models\UserSettings;
use yii\helpers\FileHelper;

class UploadController extends Controller
{
    /**
     * Очистка папки @web/uploads от файлов, которых нет в бд
     * 
     * @return int Exit code
     */
    public function actionClear()
    {
        $files_tasks = File::find()->select('path')->asArray()->column();
        $files_avatars = UserSettings::find()->select('avatar')->asArray()->column();
        $all_files = array_merge($files_tasks, $files_avatars);
        
        $files_directory = FileHelper::findFiles('web/uploads/');

        foreach ($files_directory as $path) {
            $path = substr(FileHelper::normalizePath($path, '/'), 3); // в бд записаны пути без папки web
            if (!in_array($path, $all_files)) {
                FileHelper::unlink('web' . $path);
                $this->stdout("Файл web$path удален" . PHP_EOL);
            }
        }

        return ExitCode::OK;
    }
}
