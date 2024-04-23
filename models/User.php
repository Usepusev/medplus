<?php

namespace app\models;

use PhpParser\Node\Expr\BinaryOp\Equal;
use Yii;
use app\controllers\UserController;

use function PHPUnit\Framework\equalTo;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $fio
 * @property string $password
 * @property string $date_of_birth
 * @property string $tel
 * @property int $role_id
 *
 * @property Reception[] $receptions
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $passwordconfirm;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'password', 'passwordconfirm', 'date_of_birth', 'tel', 'role_id'], 'required'],
            [['date_of_birth'], 'safe'],
            [['role_id'], 'integer'],
            [['fio'], 'string', 'max' => 511],
            [['password','passwordconfirm', 'tel'], 'string', 'max' => 255],
            ['passwordconfirm', 'compare', 'compareAttribute' => 'password'],
            [['tel'], 'unique'],
            ['tel', 'match', 'pattern' => '/^(\+7|7|8)[0-9]{10}$/'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
            'password' => 'Пароль',
            'passwordconfirm' => 'Подтвердите пароль',
            'date_of_birth' => 'Дата рождения',
            'tel' => 'Телефон',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * Gets query for [[Receptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceptions()
    {
        return $this->hasMany(Reception::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public static function findByPhone($phone)
    {
        $user = static::findOne(['tel' => $phone]);
            return $user;
    }
    public function validatePassword($password)
    {
        return $password == $this->password;
    }
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return null;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function getInstance(): User  {
        return Yii::$app->user->identity;
    }

    public function isAdmin() {
        return $this->role_id == Role::ROLE_ADMIN;
    }
}
