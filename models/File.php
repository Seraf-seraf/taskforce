<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property int $task_id
 * @property string $name
 * @property string $path
 * @property int $size
 *
 * @property Task $task
 */
class File extends ActiveRecord
{

    /**
     * @var \yii\web\UploadedFile|null
     */
    public $uploadedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uploadedFile'], 'file'],
            [['task_uid', 'name', 'path'], 'required'],
            [['path'], 'unique'],
            [['name', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'task_uid' => 'Task uID',
            'uploadedFile' => 'Загруженный файл',
            'name'     => 'Name',
            'path'     => 'Path',
            'size'     => 'Size',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['uid' => 'task_uid']);
    }

    public function upload()
    {
        $this->name = $this->uploadedFile->name;
        $this->path = '/uploads/' . uniqid() . '.' . $this->uploadedFile->getExtension();
        $this->size = $this->uploadedFile->size;

        if ($this->save()) {
            return $this->uploadedFile->saveAs('@webroot/' . $this->path);
        }

        return false;
    }
}
