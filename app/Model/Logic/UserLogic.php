<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Model\Dao\UserDao;
use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class UserLogic
 * @package App\Model\Logic
 * @Bean()
 */
class UserLogic
{
    /**
     * @Inject()
     * @var UserDao
     */
    protected $userDao;

    public function findUserInfoById(int $userId)
    {
        return $this->userDao->findUserInfoById($userId);
    }

    public function register(string $email, string $password)
    {
        $userInfo = $this->findUserInfoByEmail($email);
        if ($userInfo) {
            throw new \Exception('',ApiCode::USER_EMAIL_ALREADY_USE);
        }
        return $this->insertUser(
            [
                'email' => $email,
                'username' => User::DEFAULT_USERNAME.$email,
                'password' => password_hash($password,CRYPT_BLOWFISH),
                'sign'  => '',
                'status' => User::STATUS_OFFLINE,
                'avatar' => User::DEFAULT_AVATAR,
            ]
        );

    }

    public function login(string $email, string $password){
        $userInfo = $this->findUserInfoByEmail($email);
        if (!$userInfo || $userInfo['deleted_at'] != null){
            throw new \Exception('',ApiCode::USER_NOT_FOUND);
        }
        if (!password_verify($password,$userInfo['password'])) {
            throw new \Exception('',ApiCode::USER_PASSWORD_ERROR);
        }

        return $userInfo->toArray();
    }

    public function findUserInfoByEmail(string $email){
        return $this->userDao->findUserInfoByEmail($email);
    }

    public function insertUser(array $data){
        return $this->userDao->insertUser($data);
    }

}
