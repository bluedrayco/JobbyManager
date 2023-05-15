<?php

namespace Jobby\Tests;

use DateTimeImmutable;
use Jobby\ScheduleChecker;
use PHPUnit\Framework\TestCase;

class ScheduleCheckerTest extends TestCase
{
    /**
     * @var ScheduleChecker
     */
    private $scheduleChecker;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->scheduleChecker = new ScheduleChecker();
    }

    /**
     * @return void
     */
    public function testItCanDetectADueJobFromADatetimeString()
    {
        $this->assertTrue($this->scheduleChecker->isDue(date('Y-m-d H:i:s')));
    }

    /**
     * @return void
     */
    public function testItCanDetectIfAJobIsDueWithAPassedInDateTimeInmutable()
    {
        $scheduleChecker = new ScheduleChecker(new DateTimeImmutable("2017-01-02 13:14:59"));

        $this->assertTrue($scheduleChecker->isDue(date("2017-01-02 13:14:12")));
        $this->assertFalse($scheduleChecker->isDue(date("2017-01-02 13:15:00")));
    }

    /**
     * @return void
     */
    public function testItCanDetectANonDueJobFromADatetimeString()
    {
        $this->assertFalse($this->scheduleChecker->isDue(date('Y-m-d H:i:s', strtotime('tomorrow'))));
    }

    /**
     * @return void
     */
    public function testItCanDetectADueJobFromACronExpression()
    {
        $this->assertTrue($this->scheduleChecker->isDue("* * * * *"));
    }

    /**
     * @return void
     */
    public function testItCanDetectADueJobFromANonTrivialCronExpression()
    {
        $scheduleChecker = new ScheduleChecker(new DateTimeImmutable("2017-04-01 00:00:00"));

        $this->assertTrue($scheduleChecker->isDue("0 0 1 */3 *"));
    }

    /**
     * @return void
     */
    public function testItCanDetectANonDueJobFromACronExpression()
    {
        $hour = date("H", strtotime('+1 hour'));
        $this->assertFalse($this->scheduleChecker->isDue("* {$hour} * * *"));
    }

    /**
     * @return void
     */
    public function testItCanUseAClosureToDetectADueJob()
    {
        $this->assertTrue(
            $this->scheduleChecker->isDue(function () {
                return true;
            })
        );
    }

    /**
     * @return void
     */
    public function testItCanUseAClosureToDetectANonDueJob()
    {
        $this->assertFalse(
            $this->scheduleChecker->isDue(function () {
                return false;
            })
        );
    }

    /**
     * @return void
     */
    public function testItCanDetectIfAJobIsDueWithAPassedInDateTimeInmmutableFromACronExpression()
    {
        $scheduleChecker = new ScheduleChecker(new DateTimeImmutable("2017-01-02 18:14:59"));

        $this->assertTrue($scheduleChecker->isDue("* 18 * * *"));
    }
}
