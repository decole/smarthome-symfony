<?php
namespace App\Tests;

use App\Domain\Identity\Entity\User;
use Codeception\Scenario;
use Faker\Generator;
use Faker\Factory;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

    /** @var Generator */
    static $faker;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        static::$faker = Factory::create();
    }

    public function faker(): Generator
    {
        return self::$faker;
    }

    public function getUser(): User
    {
        $user = new User();
        $user->setTelegramId(random_int(10000000, 99999999));
        $user->setEmail($this->faker()->email());
        $user->setName($this->faker()->word());
        $user->setRoles([]);
        $user->setVerified();
        $user->setPassword($this->faker()->word());

        return $user;
    }
}
