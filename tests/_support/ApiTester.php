<?php
namespace App\Tests;

use Codeception\Scenario;
use Faker\Factory;
use Faker\Generator;

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
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

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

    public function seeResponseIsSuccessful(int $code = 200)
    {
        $this->seeResponseCodeIs($code);
        $this->seeResponseIsJson();
    }

    public function seeResponseIsValidationError(int $code = 422)
    {
        $this->seeResponseCodeIs($code);
        $this->seeResponseIsJson();
    }

    public function seeResponseIsForbidden(int $code = 403)
    {
        $this->seeResponseCodeIs($code);
        $this->seeResponseIsJson();
    }


    public function seeResponseIsException(int $code = 400)
    {
        $this->seeResponseCodeIs($code);
        $this->seeResponseIsJson();
    }
}
