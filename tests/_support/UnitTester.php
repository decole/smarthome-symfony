<?php
namespace App\Tests;

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

    /**
    * Define custom actions here
    */

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        static::$faker = Factory::create();
    }

    public function faker()
    {
        return self::$faker;
    }
}
