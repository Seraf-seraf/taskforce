<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property int $task_id
 * @property string $name
 * @property string $path
 * @property int $size
 */
class File extends ActiveRecord
{

    /**
     * @var \yii\web\UploadedFile|null
     */
    public UploadedFile|null $uploadedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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
    public function attributeLabels(): array
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
    public function getTask(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Task::class, ['uid' => 'task_uid']);
    }

    public function upload(): bool
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
