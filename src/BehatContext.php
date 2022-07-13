<?php

namespace App;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Tester\Result\TestResult;
use ImageDiff\Compares;
use Webmozart\Assert\Assert;

class BehatContext extends RawMinkContext
{

    public const FAILURE_SCREENSHOT_DIRECTORY = '/build/failure';

    /**
     * Wait a while, for debugging.
     *
     * @When I wait :seconds second(s)
     */
    public function wait(int $seconds): void
    {
        sleep($seconds);
    }


    /**
     * Do a screenshot with name parameter
     *
     * @Then I screenshot :name
     */
    public function  makeScreenshot($name) {
        $this->saveScreenshot($name . '.png', dirname(__DIR__) . '/build/compare');

    }

    /**
     * Compare images by name with all path handling
     *
     * @Then I compare :name
     */
    public function compareScreenshots($name) {
        $pathCompare = dirname(__DIR__) . '/build/compare/' . $name . '.png';
        $pathExpected = dirname(__DIR__) . '/build/expected/' . $name . '.png';
        $diffImage = dirname(__DIR__) . '/build/diff/' . $name . '.png';

        $image = new Compares($pathCompare, $pathExpected);

        $image->Diff();
        $result = $image->Result();

        $image->getDiffImage($diffImage);

        Assert::same($result->getErrorPercentage(), 0.0);
    }

    /**
     * @BeforeSuite
     *
     */
    public static function prepare(BeforeSuiteScope $scope): void
    {
        $_SERVER['BEHAT_SCREENSHOT_ON_ERROR'] = true;
        $_SERVER['PANTHER_NO_HEADLESS'] = false;
        $_SERVER['PANTHER_DEVTOOLS'] = false;
        $_SERVER['PANTHER_CHROME_ARGUMENTS'] = '--window-size=1920,1080 --hide-scrollbars --force-device-scale-factor=1';
    }

    /**
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $scope): void
    {
        // no dotenv support for take direkt global
        $takeScreenshot = filter_var($_SERVER['BEHAT_SCREENSHOT_ON_ERROR'] ?? '0', FILTER_VALIDATE_BOOLEAN);

        if ($takeScreenshot && TestResult::FAILED === $scope->getTestResult()->getResultCode()) {
            $this->saveScreenshot(null, dirname(__DIR__) . self::FAILURE_SCREENSHOT_DIRECTORY);
        }
    }

}