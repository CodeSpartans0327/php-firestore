<?php

namespace Morrislaptop\Firestore\Tests;

use Kreait\Firebase\ServiceAccount;
use Morrislaptop\Firestore\Factory;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static $fixturesDir = __DIR__ . '/_fixtures';

    /**
     * @var string
     */
    protected static $testCollection;

    /**
     * @var Firebase
     */
    protected static $firebase;

    /**
     * @var Firestore
     */
    protected static $firestore;

    /**
     * @var ServiceAccount
     */
    protected static $serviceAccount;

    public static function setUpBeforeClass()
    {
        self::setUpFirebase();

        self::$firestore = self::$firebase->getFirestore();
        self::$testCollection = 'tests';

        try {
            self::$firestore->getCollection(self::$testCollection)->remove();
        }
        catch (\Exception $e) {
            // assuming it just doesn't exist yet, continue with tests
        }
    }

    public static function setUpFirebase()
    {
        $credentialsPath = self::$fixturesDir.'/test_credentials.json';

        try {
            self::$serviceAccount = ServiceAccount::fromJsonFile($credentialsPath);
        } catch (\Throwable $e) {
            dump($e);
            self::markTestSkipped('The integration tests require a credentials file at "'.$credentialsPath.'"."');

            return;
        }

        self::$firebase = (new Factory())
            ->withServiceAccount(self::$serviceAccount)
            ->create();
    }
}
